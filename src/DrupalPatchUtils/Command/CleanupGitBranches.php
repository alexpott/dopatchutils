<?php

/**
 * @file
 * Contains \DrupalPatchUtils\Command\CleanupGitBranches.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\DoBrowser;
use DrupalPatchUtils\DoFormBase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CleanupGitBranches extends CommandBase {

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setName('cleanupGitBranches')
      ->setAliases(['cgb'])
      ->setDescription('Removes all git branches which has closed issues.');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $process = new Process('git branch');
    $process->run();

    $branches = array_map('trim', explode("\n", $process->getOutput()));

    $browser = new DoBrowser();
    if (!$browser->loggedIn()) {
      $browser->login($this->getConfig()->getDrupalUser(), $this->ask($output, "Enter your Drupal.org password: ", '', TRUE));
    }

    // Find all branches which don't need further work on.
    foreach ($branches as $branch) {
      if (is_numeric($branch)) {
        $issue = $this->getIssue($branch);

        $form = $browser->getCommentForm($issue->getUri());
        if (in_array($form->getStatus(), [DoFormBase::STATUS_CLOSED_DUPLICATE, DoFormBase::STATUS_CLOSED_WONT_FIX, DoFormBase::STATUS_CLOSED_WORKS, DoFormBase::STATUS_CLOSED_CANT_REPRODUCE, DoFormBase::STATUS_CLOSED_FIXED])) {
          $output->writeln(sprintf("Remove branch: %s", $branch));
          $process = new Process(sprintf('git branch -d %s', $branch));
          $process->run();
        }
      }
    }
  }


}
