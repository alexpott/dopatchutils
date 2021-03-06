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
use Guzzle\Http\Client;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class PatchChooserBase extends CommandBase {

  //protected $issue;

  /**
   * The name of the patch.
   *
   * @var string
   */
  protected $patch;

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
      $question_helper = new QuestionHelper();
      $output->writeln('Multiple patches detected:');
      $output->writeln($this->getChoices($patches_to_search));

      $question = new Question('Choose patch to search: ', 1);
      $question->setValidator(function ($patch_key) use ($patches_to_search) {
        if (!in_array($patch_key, range(0 ,count($patches_to_search)))) {
          throw new \InvalidArgumentException(sprintf('Choice "%s" is invalid.', $patch_key));
        }
        return $patch_key;
      });
      $patch_key = $question_helper->ask($input, $output, $question);
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

  protected function getPatch($patch) {
    $cache_dir = $this->getConfig()
                      ->getCacheDir() . DIRECTORY_SEPARATOR . 'patches';

    if (!is_dir($cache_dir)) {
      mkdir($cache_dir);
    }
    $this->patch = basename($patch);
    $cached_patch = $cache_dir . DIRECTORY_SEPARATOR . $this->patch;
    if (!file_exists($cached_patch)) {
      // Do not use a cached client since we're implementing caching.
      $client = new Client();
      $contents = $client->get($patch)
                         ->send()
                         ->getBody(TRUE);
      file_put_contents($cached_patch, $contents);
    }
    return $cached_patch;
  }

  public function getPatchName() {
    return $this->patch;
  }
}
