<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 08:29
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\RtbcQueue;
use DrupalPatchUtils\DoBrowser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateRtbcPatches extends ValidatePatch {

  protected function configure()
  {
    $this
    ->setName('validateRtbcPatches')
    ->setDescription('Checks RTBC patches still apply')
    ->addOption(
      'mark-needs-work',
      null,
      InputOption::VALUE_NONE,
      'Use if you want to automatically set the patches to needs work'
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->addArgument(
      'url',
      InputArgument::OPTIONAL,
      'What is the url of the issue to retrieve?'
    );
    $rtbc_queue = new RtbcQueue();
    $issues = $rtbc_queue->getIssueUris();
    $output->writeln(count($issues) . ' issues to check.');

    $progress = $this->getApplication()->getHelperSet()->get('progress');

    $failed_patches = array();
    $progress->start($output, count($issues));
    foreach ($issues as $item) {
      $input->setArgument('url', $item);
      // Ignore NULL return where checkPatch() is unable to determine if patch
      // applies or not. This normally occurs because the issue does not have
      // a patch.
      if ($this->checkPatch($input, $output) === FALSE) {
        $failed_patches[] = array('issue' => $item, 'patch' => $this->getPatchName(), 'output' => $this->getOutput());
      }
      $progress->advance();
    }
    $progress->finish();

    if (count($failed_patches)) {
      $output->writeln(array_map(function ($value) {return '<fg=red>' . $value['patch'] . ' on ' . $value['issue'] . ' no longer applies.</fg=red>';}, $failed_patches));
      if ($input->getOption('mark-needs-work') && $this->getDialog()->askConfirmation($output, 'Post comments to these issues (yes/NO)? ', FALSE)) {
        $browser = new DoBrowser();
        $browser->login($this->getConfig()->getDrupalUser(), $this->ask($output, "Enter your Drupal.org password: ", '', TRUE));
        foreach ($failed_patches as $item) {
          $comment_form = $browser->getCommentForm($item['issue']);
          $comment_form->setStatusNeedsWork();
          $comment_form->setCommentText($item['patch'] . " no longer applies.\n<code>\n" . $item['output']. "\n</code>");
          $comment_form->ensureTag($comment_form::TAG_NEEDS_REROLL);
          $browser->submitForm($comment_form->getForm());
        }
      }
    }
    else {
      $output->writeln('<fg=green>All patches apply</fg=green>');
    }
  }

}
