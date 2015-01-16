<?php

/**
 * @file
 * Contains DrupalPatchUtils\CommentEditor.
 */

namespace DrupalPatchUtils;


class CommentEditor {

    /**
     * @var \DrupalPatchUtils\CommentForm
     */
    protected $commentForm;

    public function __construct(CommentForm $comment_form)
    {
        $this->commentForm = $comment_form;
    }

    public function generateContent(Issue $issue, $new_files = [])
    {
        $output = [];

        $output[] = "# Please enter the comment message for your changes. Lines starting";
        $output[] = "# with '#' will be ignored, and an empty message aborts the comment.";
        $output[] = '#';
        $output[] = '# Comment on issue #' . $issue->getNid() . ': ' . $issue->getTitle();

        if (!empty($new_files)) {
            $output[] = '#';
            $output[] = '# Attached files';
            foreach ($new_files as $filename) {
                $output[] = '#  - ' . $filename;
            }
        }

        $output[] = '#';
        $output[] = '# Status: ' . IssueStatus::toString($this->commentForm->getStatus());
        foreach (IssueStatus::getDefinition() as $definition) {
            $output[] = '#  - ' . $definition['label'] . ' - ' . implode(', ', $definition['aliases']);
        }

        $output[] = '#';
        $output[] = '# Priority: ' . $this->commentForm->getPriority();
//        if (isset($issue_settings['priority'])) {
//            foreach (IssuePriority::getDefinition() as $definition) {
//                $output[] = '#  - ' . $definition['label'] . ' - ' . implode(', ', $definition['aliases']);
//            }
//        }

        $output[] = '#';
        $output[] = '# Tags: ' . implode(', ', $this->commentForm->getTags());

        return implode("\n", $output);
    }

    public function extractContent($text)
    {

    }

    protected function filterInvalidLines(array $lines)
    {

    }

}
