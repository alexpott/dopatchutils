<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 09/08/2013
 * Time: 02:42
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\DoBrowser;
use DrupalPatchUtils\Issue;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SearchIssuePatch
 * @package DrupalPatchUtils\Command
 */
class IssuePruner extends CommandBase {

  protected function configure()
  {
    $this
      ->setName('issuePruner')
      ->setAliases(array('iprune'))
      ->setDescription('Removes failed and requeued test comments from an issue')
      ->addArgument(
        'url',
        InputArgument::REQUIRED,
        'What is the url of the issue to prune?'
      )
      ->addOption(
        'include-short-comments',
        'isc',
        InputOption::VALUE_OPTIONAL,
        'Search for short comments and ask if they should be deleted',
        FALSE
      );

  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $browser = new DoBrowser();
    if (!$browser->loggedIn()) {
      $browser->login($this->getConfig()->getDrupalUser(), $this->ask($output, "Enter your Drupal.org password: ", '', TRUE));
    }


    if ($issue = $this->getIssue($input->getArgument('url'), $browser)) {
      $this->pruneSystemComments($issue, $browser, $input, $output);

      if ($comment_words = $input->getOption('include-short-comments')) {
        $this->pruneUserComments($issue, $browser, $comment_words, $input, $output);
      }
    }
  }

  protected function pruneSystemComments(Issue $issue, DoBrowser $browser, InputInterface $input, OutputInterface $output)
  {
    $deleted_comments = 0;
    $delete_links = $issue->getCrawler()
      ->filter('div.system-message.testing-failed li.comment-delete a, div.system-message.queued-retesting li.comment-delete a')
      ->extract(array('href'));
    $output->writeln(count($delete_links) . ' comments to delete.');

    if (count($delete_links) > 0 && $this->getDialog()->askConfirmation($output, 'Delete all of these comments (yes/NO)? ', FALSE)) {
      foreach ($delete_links as $delete_link) {
        $output->writeln('Deleting ' . $delete_link);
        $crawler = $browser->getClient()->request('GET', DoBrowser::DO_URL . $delete_link);
        $form = $crawler->selectButton('Delete')->form();
        $browser->submitForm($form);
        $deleted_comments++;
      }
      $output->writeln("Deleted $deleted_comments system comments.");
    }


  }

  protected function pruneUserComments(Issue $issue, DoBrowser $browser, $comment_words, InputInterface $input, OutputInterface $output) {
    $deleted_comments = 0;

    /** @var \DOMElement $comment */
    foreach ($issue->getCrawler()->filter('section.comments div.comment') as $comment) {
      $words = 0;
      $crawler = new Crawler($comment);
      if ($crawler->filter('.nodechanges-file-changes')->count() > 0) {
        // Has a file attached ignore.
        continue;
      }
      $comment_body = $crawler->filter('.field-name-comment-body div.field-item');
      if ($comment_body->count()) {
        $text = $comment_body->text();
        $words = str_word_count(trim($text));
      }
      // Zero word comments are often issue summary updates extra - ignore them
      // for now.
      if ($words <= $comment_words) {
        $changes = $crawler->filter('.field-name-field-issue-changes div.field-item');
        if ($changes->count()) {
          $output->writeln("Comment issue changes: " . trim($changes->text()));
        }
        $output->writeln("Comment text: " . trim($text));

        if ($this->getDialog()->askConfirmation($output, 'Delete this comment (yes/NO)? ', FALSE)) {
          $delete_link = $crawler->filter('li.comment-delete a, div.system-message.queued-retesting li.comment-delete a')->extract(array('href'));
          $delete_link = $delete_link[0];
          $this->deleteComment($delete_link, $browser, $output);
          $deleted_comments++;
        }
        $output->writeln('');
      }
    }
    $output->writeln("Deleted $deleted_comments user comments.");
  }

  protected function deleteComment($delete_link, DoBrowser $browser, OutputInterface $output) {
    $output->writeln('Deleting ' . $delete_link);
    $crawler = $browser->getClient()->request('GET', DoBrowser::DO_URL . $delete_link);
    $form = $crawler->selectButton('Delete')->form();
    $browser->submitForm($form);
  }
}