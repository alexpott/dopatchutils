<?php

/**
 * @file
 * Contains \DrupalPatchUtils\Command\CreateIssue.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\IssueSummaryTemplate;
use DrupalPatchUtils\Uuid;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
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

    $browser = $this->login($input, $output);

    $project = $input->getArgument('project');
    $project_form = $browser->getIssueForm($project);

    $question_helper = new QuestionHelper();

    $question = new Question('Enter title: ');
    $title = $question_helper->ask($input, $output, $question);
    $project_form->setTitle($title);

    // Limit the list of allowed values.
    $versions = array_slice($project_form->getVersions(), 0, 5);
    $question = new ChoiceQuestion('Select version: ', $versions);
    $version = $question_helper->ask($input, $output, $question);
    $project_form->setVersion($versions[$version]);

    $components = $project_form->getComponents();
    $question = new ChoiceQuestion('Select component: ', $components);
    $component = $question_helper->ask($input, $output, $question);
    $project_form->setComponent($components[$component]);

    $categories = [1 => 'Bug report', 2 => 'Task', 3 => 'Feature request', 4 => 'Support request'];
    $question = new ChoiceQuestion('Select category (Task): ', $categories);
    $category = $question_helper->ask($input, $output, $question);
    $project_form->setCategory($category);

    // Allow to input the main body either via an editor or in the shell.
    if ($input->getOption('editor')) {
      $temp_file = '/tmp/' . Uuid::generate() . ".txt";
      $filesystem = new Filesystem();
      $filesystem->touch($temp_file);
      $filesystem->dumpFile($temp_file, IssueSummaryTemplate::BODY);

      $process = new Process(sprintf('vi %s', $temp_file), NULL, NULL, NULL, 3600);

      $process->setTty(TRUE);
      $process->start();
      $process->wait();

      $output->writeln($process->getOutput());
      $output->writeln($process->getErrorOutput());

      $body_text = file_get_contents($temp_file);
    }
    else {
      $question = new Question('Enter body: ', 'TODO');
      $body_text = $question_helper->ask($input, $output, $question);
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
