<?php

/**
 * @file
 * Contains \DrupalPatchUtils\Command\Logout.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\DoBrowser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Logout extends CommandBase {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('logout')
      ->setAliases(array('logout'))
      ->setDescription('Logout from d.o');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $browser = new DoBrowser();
    $browser->logout();

    $output->writeln('User logged out.');
  }

}
