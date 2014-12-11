<?php

/**
 * @file
 * Contains \DrupalPatchUtils\Command\Login.
 */

namespace DrupalPatchUtils\Command;
use DrupalPatchUtils\DoBrowser;
use Guzzle\Plugin\Cookie\CookieJar\FileCookieJar;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Login yourself onto drupal.org and store the session cookie inside /tmp.
 */
class Login extends CommandBase {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('login')
      ->setAliases(array('login'))
      ->setDescription('Login into d.o');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    if (!$output instanceof ConsoleOutputInterface) {
      throw new \Exception('console output needed.');
    }

    $browser = new DoBrowser();
    $crawler = $browser->login($this->getConfig()->getDrupalUser(), $this->ask($output, "Enter your Drupal.org password: ", '', TRUE));

    if ($errors = $browser->getErrors($crawler)) {
      $output->getErrorOutput()->writeln($errors);
      return;
    }

    $output->writeln('Login successful or already logged in.');
  }

} 
