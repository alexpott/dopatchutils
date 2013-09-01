<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 16:04
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils;


use Symfony\Component\Yaml\Yaml;

class Config {

  /**
   * @var string
   */
  protected $drupalRepoDir;

  /**
   * @var string
   */
  protected $drupalUser;

  protected $loaded = FALSE;

  /**
   * @return \DrupalPatchUtils\Config $this
   */
  public function load() {
    if (is_file($this->getConfigFilename())) {
      $yaml = new Yaml();
      $config = $yaml->parse($this->getConfigFilename());
      $this->drupalRepoDir = isset($config['drupal_repository_dir']) ? $config['drupal_repository_dir'] : '';
      $this->drupalUser = isset($config['drupal_user']) ? $config['drupal_user'] : '';
    }
    return $this;
  }

  public function setDrupalRepoDir($dir) {
    $this->drupalRepoDir = $dir;
    return $this;
  }

  public function setDrupalUser($name) {
    $this->drupalUser = $name;
    return $this;
  }

  public function getDrupalRepoDir() {
    if (empty($this->drupalRepoDir)) {
      throw new \InvalidArgumentException('No Drupal repository configured. Run ./dop configure first.');
    }
    return $this->drupalRepoDir;
  }

  public function getDrupalUser() {
    if (empty($this->drupalUser)) {
      throw new \InvalidArgumentException('No Drupal user configured. Run ./dop configure first.');
    }
    return $this->drupalUser;
  }


  public function write() {
    $config = array(
      'drupal_repository_dir' => $this->drupalRepoDir,
      'drupal_user' => $this->drupalUser,
    );
    $yaml = new Yaml();
    file_put_contents($this->getConfigFilename(), $yaml->dump($config));
    return $this;
  }

  protected function getConfigFilename() {
    return __DIR__ . '/../../config/app.yml';
  }
}