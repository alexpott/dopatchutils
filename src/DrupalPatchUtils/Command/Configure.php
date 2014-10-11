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
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

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
    $app = $this->getApplication();

    /** @var \Symfony\Component\Console\Helper\DialogHelper $dialog */
    $dialog = $app->getHelperSet()->get('dialog');
    try {
      $default = $config->getCacheDir();
    }
    catch (\Exception $e) {
      $default = FALSE;
    }
    $cache_dir = $dialog->askAndValidate($output, "Enter path to cache dir ($default): ", array($this, 'validateCacheDir'), FALSE, $default);

    try {
      $default = $config->getDrupalRepoDir();
    }
    catch (\Exception $e) {
      $default = FALSE;
    }
    $repo_dir = $dialog->askAndValidate($output, "Enter path to Drupal repository ($default): ", array($this, 'validateDrupalRepo'), FALSE, $default);
    try {
      $default = $config->getDrupalUser();
    }
    catch (\Exception $e) {
      $default = FALSE;
    }
    $douser = $dialog->ask($output, "Enter username to use on d.o ($default): ", $default);

    try {
      $default = $config->getHoneypotSleepTime();
    }
    catch (\Exception $e) {
      $default = 20;
    }
    $honeypot_sleep_time = $dialog->ask($output, "Enter honeypot sleep time: ", $default);

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
