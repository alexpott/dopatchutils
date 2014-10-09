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
    // No point using a cache as d.o emits headers with a max-age=0.
    $this->client = new Client();
  }

  public function login($user, $pass) {
    $crawler = $this->client->request('GET', 'https://drupal.org/user/');
    // Check if already logged in.
    if (($select_button = $crawler->selectButton('Log in')) && $select_button->count()) {
      $form = $select_button->form();

      $crawler = $this->client->submit($form, array('name' => $user, 'pass' => $pass));
      $login_errors = $crawler->filter('.messages-error');
      if ($login_errors->count() > 0) {
        print_r($login_errors);
        throw new \Exception("Login to drupal.org failed.");
      }
    }
  }

  /**
   * @param string $issue_uri
   * @return \DrupalPatchUtils\CommentForm
   */
  public function getCommentForm($issue_uri) {
    $crawler = $this->client->request('GET', $issue_uri . '/edit');
    return new CommentForm($crawler->selectButton('Save')->form());
  }

  public function submitForm (Form $form) {
    $this->client->submit(($form));
  }

  /**
   * @return \Goutte\Client
   */
  public function getClient() {
    return $this->client;
  }
}