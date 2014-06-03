<?php

namespace DrupalPatchUtils\Command;

use GitWrapper\GitWrapper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ValidatePatchCommand extends PatchChooserBase
{
    protected $ensuredLatestRepo = false;

    /**
     * The output of applying the patch.
     *
     * @var string
     */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('validate-patch')
            ->setDescription('Checks that the latest patch on an issue still applies')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'What is the url of the issue to retrieve?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $patch_status = $this->checkPatch($input, $output);
        if ($patch_status === false) {
            $output->writeln('<fg=red>' . $this->patch . ' on ' . $input->getArgument('url') . ' no longer applies.</fg=red>');
        } elseif (is_null($patch_status)) {
            $output->writeln('<fg=red>Unable to check patch. Maybe ' . $input->getArgument('url') . ' does not have one.</fg=red>');
        } else {
            $output->writeln('<fg=green>' . $this->patch . ' on ' . $input->getArgument('url') . ' applies.</fg=green>');
        }

        return self::COMMAND_SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|null
     *   true is patch applies, false if patch does not, and NULL if something
     *   else occurs.
     */
    protected function checkPatch(InputInterface $input, OutputInterface $output)
    {
        $issue = $this->getIssue($input->getArgument('url'));
        if ($issue) {
            $patch = $this->choosePatch($issue, $input, $output);
            if ($patch) {
                $patch = $this->getPatch($patch);
                $this->verbose($output, "Checking $patch applies");

                $repo_dir = $this->getConfig()->getDrupalRepoDir();
                $this->ensureLatestRepo($repo_dir);

                $process = new Process("git apply --check $patch");
                $process->setWorkingDirectory($repo_dir);
                $process->run();
                if ($process->isSuccessful()) {
                    $this->output = $process->getOutput();

                    return true;
                }

                $this->output = $process->getErrorOutput();

                return false;
            }
        }

        // There is no patch, or there is a problem getting the issue.
        return null;
    }

    /**
     * Helper function to convert patch list into numbered list.
     *
     * @param array @repo_dir
     * @return array
     */
    protected function ensureLatestRepo($repo_dir)
    {
        if (!$this->ensuredLatestRepo) {
            $wrapper = new GitWrapper();
            $git = $wrapper->workingCopy($repo_dir);
            $git->pull();
            $this->ensuredLatestRepo = true;
        }
    }

    public function getOutput()
    {
        return $this->output;
    }
}
