<?php

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\Config;
use DrupalPatchUtils\Issue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class CommandBase extends Command
{
    /**
     * @var \DrupalPatchUtils\Config
     */
    protected $config;

    /**
     * @param OutputInterface $output
     * @param $messages string|array
     */
    protected function verbose(OutputInterface $output, $messages)
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln($messages);
        }
    }

    /**
     * @param $uri
     * @return Issue|bool
     */
    protected function getIssue($uri)
    {
        try {
            return new Issue($uri);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $question
     * @param string $default
     * @param bool $hidden_response
     *
     * @return string
     */
    protected function ask(OutputInterface $output, $question, $default = '', $hidden_response = FALSE)
    {
        // Need to choose patch.
        $dialog = $this->getDialog();
        if ($hidden_response) {
            $response = $dialog->askHiddenResponse($output, $question, $default);
        } else {
            $response = $dialog->ask($output, $question, $default);
        }

        return $response;
    }

    /**
     * @return \DrupalPatchUtils\Config
     */
    protected function getConfig()
    {
        if (!is_object($this->config)) {
            $this->config = new Config();
            $this->config->load();
        }

        return $this->config;
    }

    /**
     * @return \Symfony\Component\Console\Helper\DialogHelper
     */
    protected function getDialog()
    {
        $app = $this->getApplication();

        return $app->getHelperSet()->get('dialog');
    }

    /**
     * @param OutputInterface $output
     * @param $question
     * @param bool $default
     *
     * @return bool
     */
    protected function askConfirmation(OutputInterface $output, $question, $default = false)
    {
        return $this->getDialog()->askConfirmation($output, $question, $default);
    }
}
