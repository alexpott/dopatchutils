<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 08:29
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Configure extends Command {

  protected function configure()
  {
    $this
    ->setName('configure')
    ->setDescription('Configures d.o. patch utility');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $config = new Config();
    $config->load();
    $question_helper = new QuestionHelper();

    try {
      $default = $config->getCacheDir();
    }
    catch (\Exception $e) {
      $default = FALSE;
    }
    $question = new Question("Enter path to cache dir ($default): ", $default);
    $question->setValidator(array($this, 'validateCacheDir'));
    $cache_dir = $question_helper->ask($input, $output, $question);

    try {
      $default = $config->getDrupalRepoDir();
    }
    catch (\Exception $e) {
      $default = FALSE;
    }
    $question = new Question("Enter path to Drupal repository ($default): ", $default);
    $question->setValidator(array($this, 'validateDrupalRepo'));
    $repo_dir = $question_helper->ask($input, $output, $question);

    try {
      $default = $config->getDrupalUser();
    }
    catch (\Exception $e) {
      $default = FALSE;
    }
    $question = new Question("Enter username to use on d.o ($default): ", $default);
    $douser = $question_helper->ask($input, $output, $question);

    try {
      $default = $config->getHoneypotSleepTime();
    }
    catch (\Exception $e) {
      $default = 20;
    }
    $question = new Question("Enter honeypot sleep time ($default): ", $default);
    $honeypot_sleep_time = $question_helper->ask($input, $output, $question);

    $config
      ->setCacheDir($cache_dir)
      ->setDrupalRepoDir($repo_dir)
      ->setDrupalUser($douser)
      ->setHoneypotSleepTime($honeypot_sleep_time)
      ->write();
  }

  public function validateDrupalRepo($dir) {
    if (!is_dir($dir)) {
      throw new \InvalidArgumentException(sprintf('"%s" is not a directory.', $dir));
    }
    if (!is_dir($dir . '/.git')) {
      throw new \InvalidArgumentException(sprintf('"%s" is not a git repository.', $dir));
    }
    if (!is_dir($dir . '/modules') && !is_dir($dir . '/themes')) {
      throw new \InvalidArgumentException(sprintf('"%s" is not likely to be Drupal. There are no modules and themes directories.', $dir));
    }
    return $dir;
  }

  public function validateCacheDir($dir) {
    if (!is_dir($dir)) {
      throw new \InvalidArgumentException(sprintf('"%s" is not a directory.', $dir));
    }
    if (!is_writable($dir)) {
      throw new \InvalidArgumentException(sprintf('"%s" is not writable.', $dir));
    }
    return $dir;

  }
}
