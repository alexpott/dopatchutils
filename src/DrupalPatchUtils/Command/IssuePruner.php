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
use Guzzle\Http\Client;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
      );

  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $browser = new DoBrowser();
    if (!$browser->loggedIn()) {
      $browser->login($this->getConfig()->getDrupalUser(), $this->ask($output, "Enter your Drupal.org password: ", '', TRUE));
    }


    if ($issue = $this->getIssue($input->getArgument('url'), $browser)) {
      $delete_links = $issue->getCrawler()
        ->filter('div.system-message.testing-failed li.comment-delete a, div.system-message.queued-retesting li.comment-delete a')
        ->extract(array('href'));
      $output->writeln(count($delete_links) . ' comments to delete.');
      if ($this->getDialog()->askConfirmation($output, 'Delete all of these comments (yes/NO)? ', FALSE)) {
        foreach ($delete_links as $delete_link) {
          $output->writeln('Deleting ' . $delete_link);
          $crawler = $browser->getClient()->request('GET', DoBrowser::DO_URL . $delete_link);
          $form = $crawler->selectButton('Delete')->form();
          $browser->submitForm($form);
        }
      }
    }
  }


}