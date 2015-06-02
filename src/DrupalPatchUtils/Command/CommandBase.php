<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 30/08/2013
 * Time: 23:24
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\Config;
use DrupalPatchUtils\DoBrowser;
use DrupalPatchUtils\Issue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class CommandBase extends Command {

  /**
   * @var \DrupalPatchUtils\Config
   */
  protected $config;

  /**
   * @param OutputInterface $output
   * @param $messages string|array
   */
  protected function verbose (OutputInterface $output, $messages) {
    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
      $output->writeln($messages);
    }
  }

  /**
   * @param $uri
   * @return Issue|bool
   */
  protected function getIssue($uri, DoBrowser $browser = NULL) {
    try {
      return new Issue($uri, $browser);
    } catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * @param OutputInterface $output
   * @param string $question
   * @param string $default
   *
   * @return string
   */
  protected function ask (InputInterface $input, OutputInterface $output, Question $question, $hidden_response = FALSE) {
    $question->setHidden($hidden_response);
    $questionHelper = new QuestionHelper();
    return $questionHelper->ask($input, $output, $question);
  }

  /**
   * @return \DrupalPatchUtils\Config
   */
  protected function getConfig() {
    if (!is_object($this->config)) {
      $this->config = new Config();
      $this->config->load();
    }
    return $this->config;
  }

  /**
   * @param \Symfony\Component\Console\Input\InputInterface $input
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   * @param $question
   * @return string
   */
  protected function askConfirmation(InputInterface $input, OutputInterface $output, $question) {
    $confim = new ConfirmationQuestion($question);
    $question_helper = new QuestionHelper();
    return $question_helper->ask($input, $output, $confim);
  }

  /**
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *
   * @return \DrupalPatchUtils\DoBrowser
   * @throws \Exception
   */
  protected function login(InputInterface $input, OutputInterface $output) {
    $browser = new DoBrowser();
    if (!$browser->loggedIn()) {
      $browser->login($this->getConfig()->getDrupalUser(), $this->ask($input, $output, new Question("Enter your Drupal.org password: "), TRUE), $this->ask($input, $output, new Question("Enter tfa code (if activated for account): "), TRUE));
    }
    return $browser;
  }
}
