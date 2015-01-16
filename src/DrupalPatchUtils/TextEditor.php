<?php

/**
 * @file
 * Contains DrupalPatchUtils\TextEditor.
 */

namespace DrupalPatchUtils;


use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class TextEditor {

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return string
     */
    public function editor(OutputInterface $output, $text = '')
    {
        $temp_file = '/tmp/' . Uuid::generate() . ".txt";
        $filesystem = new Filesystem();
        $filesystem->touch($temp_file);
        $filesystem->dumpFile($temp_file, $text);

        $process = new Process(sprintf('vi %s', $temp_file), null, null, null,
          3600);

        $process->setTty(true);
        $process->start();
        $process->wait();

        $output->writeln($process->getOutput());
        $output->writeln($process->getErrorOutput());

        $body_text = file_get_contents($temp_file);
        return $body_text;
    }

}
