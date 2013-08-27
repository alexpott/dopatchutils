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

  protected $loaded = FALSE;

  public function load() {
    if (is_file($this->getConfigFilename())) {
      $yaml = new Yaml();
      $config = $yaml->parse($this->getConfigFilename());
      $this->drupalRepoDir = $config['drupal_repository_dir'];
    }
    return $this;
  }

  public function setDrupalRepoDir($dir) {
    $this->drupalRepoDir = $dir;
    return $this;
  }

  public function getDrupalRepoDir() {
    return $this->drupalRepoDir;
  }


  public function write() {
    $config = array(
      'drupal_repository_dir' => $this->drupalRepoDir
    );
    $yaml = new Yaml();
    file_put_contents($this->getConfigFilename(), $yaml->dump($config));
    return $this;
  }

  protected function getConfigFilename() {
    return __DIR__ . '/../../config/app.yml';
  }
}