<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 08:29
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\RtbcQueue;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchRtbcPatches extends SearchIssuePatch {

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
      );

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

    $progress = new ProgressBar($output, 50);
    $progress->start();
    foreach ($issues_to_search as $item) {
      $input->setArgument('url', $item);
      parent::execute($input, $output);
      $progress->advance();
    }
    $progress->finish();
  }

}
