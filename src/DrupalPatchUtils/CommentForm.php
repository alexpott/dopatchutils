<?php

/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 01/09/2013
 * Time: 15:23
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils;

class CommentForm extends DoFormBase
{

    /**
     * The browser.
     *
     * @var \DrupalPatchUtils\DoBrowser
     */
    protected $browser;

    public function __construct(DoBrowser $browser)
    {
        $this->browser = $browser;
        $this->form = $this->getCrawler()->selectButton('Save')->form();
    }

    public function setCommentText($text)
    {
        $comment = $this->form->get('nodechanges_comment_body[value]');
        $comment->setValue($text);
        $this->form->set($comment);
        return $this;
    }

    public function uploadFiles(array $files = [])
    {
        $file_nr = 0;
        foreach ($files as $key => $file) {
            $this->form = $this->getCrawler()->selectButton('Upload')->form();

            while (!($this->form->has("files[field_issue_files_und_$file_nr]"))) {
                $file_nr++;
            }
            $this->form["files[field_issue_files_und_$file_nr]"]->setFilePath($file);

            $this->browser->getClient()->submit($this->form);
        }

        $this->form = $this->getCrawler()->selectButton('Save')->form();
    }

    /**
     * @return null|\Symfony\Component\DomCrawler\Crawler
     */
    protected function getCrawler()
    {
        return $this->browser->getClient()->getCrawler();
    }

}
