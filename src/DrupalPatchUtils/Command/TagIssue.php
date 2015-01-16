<?php

/**
 * @file
 * Contains DrupalPatchUtils\Command\TagIssue.
 */

namespace DrupalPatchUtils\Command;


use DrupalPatchUtils\DoBrowser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TagIssue extends CommandBase {

    protected function configure()
    {
        $this
          ->setName('tagIssue')
          ->setAliases(array('ti'))
          ->setDescription('Add a tag to an existing issue')
          ->addArgument(
            'url',
            InputArgument::REQUIRED,
            'What is the url of the issue to retrieve?'
          )
          ->addArgument(
            'tag',
            InputArgument::REQUIRED,
            'The new tag'
          );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $issue = $this->getIssue($input->getArgument('url'));

        $browser = new DoBrowser();
        if (!$browser->loggedIn()) {
            $browser->login($this->getConfig()->getDrupalUser(), $this->ask($output, "Enter your Drupal.org password: ", '', TRUE));
        }

        $comment_form = $browser->getCommentForm($issue->getUri());

        $comment_form->setCommentText('Add tag');
        $comment_form->ensureTag($input->getArgument('tag'));

        $browser->submitForm($comment_form->getForm());
        $output->writeln('Added tag');
    }

}
