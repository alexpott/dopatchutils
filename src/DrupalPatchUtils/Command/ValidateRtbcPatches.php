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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateRtbcPatches extends ValidatePatch {

  protected function configure()
  {
    $this
    ->setName('validateRtbcPatches')
    ->setDescription('Checks RTBC patches still apply');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->addArgument(
      'url',
      InputArgument::OPTIONAL,
      'What is the url of the issue to retrieve?'
    );
    $rtbc_queue = new RtbcQueue();
    $issues = $rtbc_queue->getIssueUris();
    $output->writeln(count($issues) . ' issues to check.');

    $progress = $this->getApplication()->getHelperSet()->get('progress');

    $failed_patches = array();
    $progress->start($output, count($issues));
    foreach ($issues as $item) {
      $input->setArgument('url', $item);
      if (!$this->checkPatch($input, $output)) {
        $failed_patches[] = '<fg=red>' . $item . ' no longer applies.</fg=red>';
      }
      $progress->advance();
    }
    $progress->finish();
    $output->writeln($failed_patches);
  }

}
