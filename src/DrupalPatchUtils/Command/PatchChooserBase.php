<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 16:39
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\Issue;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class PatchChooserBase extends CommandBase {

  //protected $issue;


  /**
   * @param Issue $issue
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return string|bool
   */
  protected function choosePatch(Issue $issue, InputInterface $input, OutputInterface $output) {
    $patch = FALSE;
    $patches_to_search = $issue->getLatestPatch();
    if (count($patches_to_search) > 1) {
      // Need to choose patch.
      $app = $this->getApplication();
      $dialog = $app->getHelperSet()->get('dialog');
      $output->writeln('Multiple patches detected:');
      $output->writeln($this->getChoices($patches_to_search));

      $patch_key = $dialog->askAndValidate($output, 'Choose patch to search: ', function ($patch_key) use ($patches_to_search) {
        if (!in_array($patch_key, range(0 ,count($patches_to_search)))) {
          throw new \InvalidArgumentException(sprintf('Choice "%s" is invalid.', $patch_key));
        }
        return $patch_key;
      }, false, 1);
      $patch = $patches_to_search[$patch_key - 1];
    }
    elseif (count($patches_to_search) == 1) {
      $patch = $patches_to_search[0];
    }
    else {
      // Nothing to do.
      $output->writeln("No patches available on ". $issue->getUri());
    }
    return $patch;
  }

  /**
   * Helper function to convert patch list into numbered list.
   *
   * @param array $patches_to_search
   * @return array
   */
  protected function getChoices(array $patches_to_search) {
    return array_map(function ($value) {
      static $counter = 1;
      $value = '[' . $counter . '] ' . $value;
      if ($counter == 1) {
        $value .= ' (default)';
      }
      $counter++;
      return $value;
    }, $patches_to_search);
  }
}
