<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 28/08/2013
 * Time: 06:24
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils;

use Goutte\Client;
use Symfony\Component\DomCrawler\Form;

class DoBrowser {

  public function __construct() {
    $this->client = new Client();
  }

  public function login($user, $pass) {
    $crawler = $this->client->request('GET', 'https://drupal.org/user/');
    $form = $crawler->selectButton('Log in')->form();
    $this->client->submit($form, array('name' => $user, 'pass' => $pass));
  }

  /**
   * @param Issue $issue
   * @return \DrupalPatchUtils\CommentForm
   */
  public function getCommentForm(Issue $issue) {
    $crawler = $this->client->request('GET', $issue->getUri());
    return new CommentForm($crawler->selectButton('Save')->form());
  }

  public function submitForm (Form $form) {
    $this->client->submit(($form));
  }
}