<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 09/08/2013
 * Time: 02:42
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\Config;
use GitWrapper\GitWrapper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class SearchIssuePatch
 * @package DrupalPatchUtils\Command
 */
class ValidatePatch extends PatchChooserBase {

  protected $ensuredLatestRepo = FALSE;

  protected function configure()
  {
    $this
    ->setName('validatePatch')
    ->setDescription('Checks that the latest patch on an issue still applies')
    ->addArgument(
      'url',
      InputArgument::REQUIRED,
      'What is the url of the issue to retrieve?'
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $patch_status = $this->checkPatch($input, $output);
    if ($patch_status === FALSE) {
      $output->writeln('<fg=red>' . $input->getArgument('url') . ' no longer applies.</fg=red>');
    }
    else if (is_null($patch_status)) {
      $output->writeln('<fg=red>Unable to check patch. Maybe ' . $input->getArgument('url') . ' does not have one.</fg=red>');
    }
    else {
      $output->writeln('<fg=green>' . $input->getArgument('url') . ' applies.</fg=green>');
    }
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return bool|null
   *   TRUE is patch applies, FALSE if patch does not, and NULL if something
   *   else occurs.
   */
  protected function checkPatch(InputInterface $input, OutputInterface $output) {
    $issue = $this->getIssue($input->getArgument('url'));
    if ($issue) {
      $patch = $this->choosePatch($issue, $input, $output);
      if ($patch) {
        $this->verbose($output, "Checking $patch applies");
        $repo_dir = $this->getConfig()->getDrupalRepoDir();
        $this->ensureLatestRepo($repo_dir);

        $process = new Process("curl $patch | git apply --check");
        $process->setWorkingDirectory($repo_dir);
        $process->run();
        if ($process->isSuccessful()) {
          return TRUE;
        }
        else {
          return FALSE;
        }
      }
    }
    // There is no patch, or there is a problem getting the issue.
    return NULL;
  }

  /**
   * Helper function to convert patch list into numbered list.
   *
   * @param array $patches_to_search
   * @return array
   */
  protected function ensureLatestRepo($repo_dir) {
    if (!$this->ensuredLatestRepo) {
      $wrapper = new GitWrapper();
      $git = $wrapper->workingCopy($repo_dir);
      $git->pull();
      $this->ensuredLatestRepo = TRUE;
    }
  }
}