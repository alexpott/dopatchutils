<?php

namespace DrupalPatchUtils\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Configure extends CommandBase
{
    protected function configure()
    {
        $this
            ->setName('configure')
            ->setDescription('Configures d.o. patch utility')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig();
        $dialog = $this->getDialog();

        try {
            $default = $config->getCacheDir();
        } catch (\Exception $e) {
            $default = false;
        }

        $cache_dir = $dialog->askAndValidate($output, "Enter path to cache dir ($default): ", array($this, 'validateCacheDir'), false, $default);

        try {
            $default = $config->getDrupalRepoDir();
        } catch (\Exception $e) {
            $default = false;
        }

        $repo_dir = $dialog->askAndValidate($output, "Enter path to Drupal repository ($default): ", array($this, 'validateDrupalRepo'), false, $default);

        try {
            $default = $config->getDrupalUser();
        } catch (\Exception $e) {
            $default = false;
        }

        $douser = $dialog->ask($output, "Enter username to use on d.o ($default): ", $default);

        $config
            ->setCacheDir($cache_dir)
            ->setDrupalRepoDir($repo_dir)
            ->setDrupalUser($douser)
            ->write()
        ;
    }

    public function validateDrupalRepo($dir)
    {
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

    public function validateCacheDir($dir)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a directory.', $dir));
        }
        if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not writable.', $dir));
        }

        return $dir;
    }
}
