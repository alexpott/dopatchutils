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
use Guzzle\Plugin\Cookie\CookieJar\FileCookieJar;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

class DoBrowser {

  /** @var \Goutte\Client */
  protected $client;

  public function __construct() {
    // No point using a cache as d.o emits headers with a max-age=0.
    $this->client = new Client();

    // Add the cooke plugin from guzzle to ensure that the cookie is stored.
    $guzzle_client = $this->getClient()->getClient();
    $cookie_plugin = new CookiePlugin(new FileCookieJar($this->ensureCookieFilepath()));
    $guzzle_client->addSubscriber($cookie_plugin);
  }

  /**
   * Determines whether the user is already logged in.
   *
   * @return bool
   */
  public function loggedIn() {
    $crawler = $this->client->request('GET', 'https://www.drupal.org/user/');

    $log_in_button = $crawler->selectButton('Log in');
    return $log_in_button->count() == 0;
  }

  public function login($user, $pass) {
    $crawler = $this->client->request('GET', 'https://www.drupal.org/user/');
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
    return $crawler;
  }

  public function logout() {
    if ($this->loggedIn()) {
      $this->client->request('GET', 'https://www.drupal.org/user/logout');
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

  public function getIssueForm($project) {
    $uri = 'https://www.drupal.org/node/add/project-issue/' . $project;
    $crawler = $this->client->request('GET', $uri);
    return new IssueForm($crawler->selectButton('Save')->form());
  }

  public function getErrors(Crawler $crawler) {
    $login_errors = $crawler->filter('.messages.error');
    $errors = [];
    if ($login_errors->count() > 0) {
      foreach ($login_errors as $login_error) {
        $errors[] = $login_error->nodeValue;
      }
    }
    return $errors;
  }

  public function submitForm(Form $form) {
    return $this->client->submit(($form));
  }

  /**
   * @return \Goutte\Client
   */
  public function getClient() {
    return $this->client;
  }

  protected function ensureCookieFilepath() {
    $filepath = sys_get_temp_dir() . '/dopathutils';
    if (!file_exists($filepath)) {
      touch($filepath);
    }
    return $filepath;
  }

}
