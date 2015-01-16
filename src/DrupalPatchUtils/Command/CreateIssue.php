<?php

/**
 * @file
 * Contains \DrupalPatchUtils\Command\CreateIssue.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\DoBrowser;
use DrupalPatchUtils\IssueSummaryTemplate;
use DrupalPatchUtils\TextEditor;
use DrupalPatchUtils\Uuid;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

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
      )
      ->addOption('editor', NULL, InputOption::VALUE_NONE)
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    if (!$output instanceof ConsoleOutputInterface) {
      throw new \Exception('Console output needed.');
    }

    $browser = new DoBrowser();
    $this->ensureUserIsLoggedIn($browser, $output);

    $project = $input->getArgument('project');
    $project_form = $browser->getIssueForm($project);

    // Ask for the common stuff.
    $app = $this->getApplication();
    $dialog = $app->getHelperSet()->get('dialog');
    /** @var DialogHelper $dialog */

    $title = $dialog->ask($output, 'Enter title: ');
    $project_form->setTitle($title);

    // Limit the list of allowed values.
    $versions = array_slice($project_form->getVersions(), 0, 5);
    $version = $dialog->select($output, 'Select version: ', $versions);
    $project_form->setVersion($versions[$version]);

    $components = $project_form->getComponents();
    $component = $dialog->select($output, 'Select component: ', $components);
    $project_form->setComponent($components[$component]);

    $categories = [1 => 'Bug report', 2 => 'Task', 3 => 'Feature request', 4 => 'Support request'];
    $category = $dialog->select($output, 'Select category: (Task) ', $categories, 2);
    $project_form->setCategory($category);

    // Allow to input the main body either via an editor or in the shell.
    if ($input->getOption('editor')) {
      $editor = new TextEditor();
      $body_text = $editor->editor($output, IssueSummaryTemplate::BODY);
    }
    else {
      $body_text = $dialog->ask($output, 'Enter body: ', 'TODO');
    }

    $project_form->setBody($body_text);

    // Ensure that honeypot doesn't block the request.
    // @TODO find the minimal required value.
    $seconds = $this->getConfig()->getHoneypotSleepTime();
    while ($seconds--) {
      $output->write(sprintf("\rWait %s seconds for honeypot", $seconds));
      sleep(1);
    }
    $output->write("\n");

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
