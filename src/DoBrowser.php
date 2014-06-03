<?php

namespace DrupalPatchUtils;

use Goutte\Client;
use Symfony\Component\DomCrawler\Form;

class DoBrowser
{
    public function __construct()
    {
        // No point using a cache as d.o emits headers with a max-age=0.
        $this->client = new Client();
    }

    public function login($user, $pass)
    {
        $crawler = $this->client->request('GET', 'https://drupal.org/user/');
        $form = $crawler->selectButton('Log in')->form();
        $this->client->submit($form, ['name' => $user, 'pass' => $pass]);
    }

    /**
     * @param string $issue_uri
     *
     * @return \DrupalPatchUtils\CommentForm
     */
    public function getCommentForm($issue_uri)
    {
        $crawler = $this->client->request('GET', $issue_uri . '/edit');

        return new CommentForm($crawler->selectButton('Save')->form());
    }

    public function submitForm(Form $form)
    {
        $this->client->submit(($form));
    }

    /**
     * @return \Goutte\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
