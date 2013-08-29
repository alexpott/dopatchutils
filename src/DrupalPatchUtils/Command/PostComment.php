<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 08:29
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\Config;
use DrupalPatchUtils\DoBrowser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class PostComment extends Command {

  protected function configure()
  {
    $this
    ->setName('postComment')
    ->setAliases(array('pc'))
    ->setDescription('Posts comment to d.o.');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $browser = new DoBrowser();
    $browser->login('alexpott', 'pass');
  }

}