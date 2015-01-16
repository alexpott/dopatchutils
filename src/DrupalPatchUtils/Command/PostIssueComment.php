<?php

/**
 * @file
 * Contains DrupalPatchUtils\Command${NAME}.
 */


namespace DrupalPatchUtils\Command;


use DrupalPatchUtils\CommentEditor;
use DrupalPatchUtils\DoBrowser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostIssueComment extends CommandBase {

    protected function configure()
    {
        $this
            ->setName('postIssueComment')
            ->setAliases(array('pic'))
            ->setDescription('Posts comment on an issue to d.o.')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'What is the url/nid of the issue to retrieve?'
            )
            ->addArgument(
              'files',
              InputArgument::IS_ARRAY,
              'Files'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $issue = $this->getIssue($url);

        $browser = new DoBrowser();
        $this->ensureUserIsLoggedIn($browser, $output);

        $comment_form = $browser->getCommentForm($issue->getUri());

        $comment_editor = new CommentEditor($comment_form);
        print $comment_editor->generateContent($issue, $input->getArgument('files'));
        return;

        $comment_form->setCommentText('Added comment.');

        $browser->submitForm($comment_form->getForm());
    }

}
