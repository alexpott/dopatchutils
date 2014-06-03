<?php

namespace DrupalPatchUtils;

use Symfony\Component\Yaml\Yaml;

class Config
{
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

    protected $loaded = false;

    /**
     * @return \DrupalPatchUtils\Config
     */
    public function load()
    {
        if (is_file($this->getConfigFilename())) {
            $config = Yaml::parse($this->getConfigFilename());
            $this->drupalRepoDir = isset($config['drupal_repository_dir']) ? $config['drupal_repository_dir'] : '';
            $this->drupalUser = isset($config['drupal_user']) ? $config['drupal_user'] : '';
            $this->cacheDir = isset($config['cache_dir']) ? $config['cache_dir'] : '';
        }

        return $this;
    }

    public function setCacheDir($dir)
    {
        $this->cacheDir = $dir;

        return $this;
    }

    public function setDrupalRepoDir($dir)
    {
        $this->drupalRepoDir = $dir;

        return $this;
    }

    public function setDrupalUser($name)
    {
        $this->drupalUser = $name;

        return $this;
    }

    public function getDrupalRepoDir()
    {
        if (empty($this->drupalRepoDir)) {
            throw new \InvalidArgumentException('No Drupal repository configured. Run php bin/dop configure first.');
        }

        return $this->drupalRepoDir;
    }

    public function getDrupalUser()
    {
        if (empty($this->drupalUser)) {
            throw new \InvalidArgumentException('No Drupal user configured. Run php bin/dop configure first.');
        }

        return $this->drupalUser;
    }

    public function getCacheDir()
    {
        if (empty($this->cacheDir)) {
            throw new \InvalidArgumentException('No cache dir configured. Run php bin/dop configure first.');
        }

        return $this->cacheDir;
    }

    public function write()
    {
        $config = [
            'cache_dir' => $this->cacheDir,
            'drupal_repository_dir' => $this->drupalRepoDir,
            'drupal_user' => $this->drupalUser,
        ];
        file_put_contents($this->getConfigFilename(), Yaml::dump($config));

        return $this;
    }

    protected function getConfigFilename()
    {
        return __DIR__ . '/../config/app.yml';
    }
}
