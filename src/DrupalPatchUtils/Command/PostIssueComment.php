<?php

/**
 * @file
 * Contains DrupalPatchUtils\Command${NAME}.
 */


namespace DrupalPatchUtils\Command;


use DrupalPatchUtils\CommentEditor;
use DrupalPatchUtils\DoBrowser;
use DrupalPatchUtils\IssuePriority;
use DrupalPatchUtils\IssueStatus;
use DrupalPatchUtils\TextEditor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
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
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \Exception('Console output needed.');
        }

        $url = $input->getArgument('url');
        $issue = $this->getIssue($url);

        $browser = new DoBrowser();
        $this->ensureUserIsLoggedIn($browser, $output);

        $comment_form = $browser->getCommentForm($issue->getUri());


        $files = $input->getArgument('files');

        $comment_editor = new CommentEditor($comment_form);
        $template = $comment_editor->generateContent($issue, $files);

        $editor = new TextEditor();
        $result = $editor->editor($output, $template);

        $body = $comment_editor->getCommentText($result);
        $metadata = $comment_editor->getMetadata($result);

        $comment_form->uploadFiles($files);

        $comment_form->setCommentText($body);

        if (isset($metadata['status'])) {
            $comment_form->setStatus(IssueStatus::toInteger($metadata['status']));
        }

        if (isset($metadata['priority'])) {
            $comment_form->setPriority(IssuePriority::toInteger($metadata['priority']));
        }

        $crawler = $browser->submitForm($comment_form->getForm());

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
