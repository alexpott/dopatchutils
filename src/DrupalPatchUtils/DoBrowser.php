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

class DoBrowser {

  public function __construct() {
    $this->client = new Client();
  }

  public function login($user, $pass) {
    $crawler = $this->client->request('GET', 'https://drupal.org/user/');
    $form = $crawler->selectButton('Log in')->form();
    $this->client->submit($form, array('name' => $user, 'pass' => $pass));
    var_dump($this->client->getResponse());
  }


}