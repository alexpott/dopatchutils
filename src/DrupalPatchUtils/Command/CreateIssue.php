<?php

/**
 * @file
 * Contains \DrupalPatchUtils\Command\CreateIssue.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\DoBrowser;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateIssue extends CommandBase {

  protected function configure()
  {
    $this
      ->setName('postIssue')
      ->setAliases(array('pi'))
      ->setDescription('Posts issues to d.o.')
      ->addArgument(
        'project',
        InputArgument::OPTIONAL,
        'What is the url of the issue to retrieve?',
        'drupal'
      );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    if (!$output instanceof ConsoleOutputInterface) {
      throw new \Exception('console output needed.');
    }

    $browser = new DoBrowser();
    $browser->login($this->getConfig()->getDrupalUser(), $this->ask($output, "Enter your Drupal.org password: ", '', TRUE));

    $project = $input->getArgument('project');
    $project_form = $browser->getIssueForm($project);

    // Ask for the common stuff.
    $app = $this->getApplication();
    $dialog = $app->getHelperSet()->get('dialog');
    /** @var DialogHelper $dialog */

    $title = $dialog->ask($output, 'Enter title: ');
    $project_form->setTitle($title);

    $components = ['Code' => 'Code', 'Documentation' => 'Documentation', 'Miscellaneous' => 'Miscellaneous', 'User interface' => 'User interface'];
    $component = $dialog->select($output, 'Select component: ', $components, 'Code');
    $project_form->setComponent($component);

    $categories = [1 => 'Bug report', 2 => 'Task', 3 => 'Feature request', 4 => 'Support request'];
    $category = $dialog->select($output, 'Select category: ', $categories, 2);
    $project_form->setCategory($category);

    $body_text = $dialog->ask($output, 'Enter body: ', 'TODO');
    $project_form->setBody($body_text);

    $crawler = $browser->submitForm($project_form->getForm());

    if ($errors = $browser->getErrors($crawler)) {
      $output->getErrorOutput()->writeln($errors);
    }
    else {
      $uri = $browser->getClient()->getHistory()->current()->getUri();
      $output->writeln(sprintf('Posting the issue was successful: %s', $uri));
    }
    return;
  }

}
