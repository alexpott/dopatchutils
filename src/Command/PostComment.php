<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 08:29
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\DoBrowser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostComment extends CommandBase {

  protected function configure()
  {
    $this
    ->setName('postComment')
    ->setAliases(array('pc'))
    ->setDescription('Posts comment to d.o.')
    ->addArgument(
      'url',
      InputArgument::REQUIRED,
      'What is the url of the issue to retrieve?'
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $issue = $this->getIssue($input->getArgument('url'));
    if ($issue) {
      $browser = new DoBrowser();
      $browser->login($this->getConfig()->getDrupalUser(), $this->ask($output, "Enter your Drupal.org password: ", '', TRUE));
      $comment_form = $browser->getCommentForm($issue->getUri());

      $comment_form->setStatusNeedsWork();
      $comment_form->setCommentText('Automated Alex!');
      $comment_form->ensureTag($comment_form::TAG_NEEDS_REROLL);

      $browser->submitForm($comment_form->getForm());

      //$comment = $comment_form->get('comment');
      //$comment->setValue('Testing...');
      //$comment_form->set($comment);

      //$tags = $comment_form->get('taxonomy[tags]');
      //var_dump($tags);

      //$browser->submitForm($comment_form);

      //var_dump($comment);
      //var_dump($comment_form);
    }
  }

}