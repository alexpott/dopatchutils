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

  /**
   * @var string
   */
  protected $cacheDir;

  /**
   * @var int
   */
  protected $honeypotSleepTime;

  protected $loaded = FALSE;

  /**
   * @return \DrupalPatchUtils\Config $this
   */
  public function load() {
    if (is_file($this->getConfigFilename())) {
      $yaml = new Yaml();
      $config = $yaml->parse(file_get_contents($this->getConfigFilename()));
      $this->drupalRepoDir = isset($config['drupal_repository_dir']) ? $config['drupal_repository_dir'] : '';
      $this->drupalUser = isset($config['drupal_user']) ? $config['drupal_user'] : '';
      $this->cacheDir = isset($config['cache_dir']) ? $config['cache_dir'] : '';
      $this->honeypotSleepTime = isset($config['honeypot_sleep_time']) ? $config['honeypot_sleep_time'] : NULL;
    }
    return $this;
  }

  public function setCacheDir($dir) {
    $this->cacheDir = $dir;
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

  public function setHoneypotSleepTime($seconds) {
    $this->honeypotSleepTime = $seconds;
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

  public function getCacheDir() {
    if (empty($this->cacheDir)) {
      throw new \InvalidArgumentException('No cache dir configured. Run ./dop configure first.');
    }
    return $this->cacheDir;

  }

  public function getHoneypotSleepTime() {
    if ($this->honeypotSleepTime === NULL) {
      throw new \InvalidArgumentException('No cache dir configured. Run ./dop configure first.');
    }
    return $this->honeypotSleepTime;
  }

  public function write() {
    $config = array(
      'cache_dir' => $this->cacheDir,
      'drupal_repository_dir' => $this->drupalRepoDir,
      'drupal_user' => $this->drupalUser,
      'honeypot_sleep_time' => $this->honeypotSleepTime,
    );
    $yaml = new Yaml();
    file_put_contents($this->getConfigFilename(), $yaml->dump($config));
    return $this;
  }

  protected function getConfigFilename() {
    return __DIR__ . '/../../config/app.yml';
  }
}
