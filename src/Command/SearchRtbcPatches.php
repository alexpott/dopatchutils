<?php

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\RtbcQueue;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchRtbcPatches extends SearchIssuePatch
{
    protected function configure()
    {
        $this
            ->setName('searchRtbcPatches')
            ->setDescription('Searches RTBC patches for text')
            ->addArgument(
                'searchText',
                InputArgument::REQUIRED,
                'What is the text to search for?'
            )
            ->addOption(
                'regex',
                null,
                InputOption::VALUE_NONE,
                'If set use preg_match to search patch'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addArgument(
            'url',
            InputArgument::OPTIONAL,
            'What is the url of the issue to retrieve?'
        );
        $rtbc_queue = new RtbcQueue();
        $issues_to_search = $rtbc_queue->getIssueUris();
        $output->writeln(count($issues_to_search) . ' issues to search.');

        $progress = $this->getApplication()->getHelperSet()->get('progress');
        $progress->start($output, count($issues_to_search));
        foreach ($issues_to_search as $item) {
            $input->setArgument('url', $item);
            parent::execute($input, $output);
            $progress->advance();
        }
        $progress->finish();
    }
}
