<?php

namespace DrupalPatchUtils;

use Guzzle\Http\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides access to a D.O issue.
 */
class Issue
{
    protected $issue;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @param string $issue_id
     *   NID or URI of an issue.
     */
    public function __construct($issue_id)
    {
        if (is_numeric($issue_id)) {
            $this->uri = 'https://drupal.org/node/' . $issue_id;
        } elseif (filter_var($issue_id, FILTER_VALIDATE_URL) !== false) {
            $this->uri = $issue_id;
        }
        $this->getIssue();
    }

    protected function getIssue()
    {
        $doBrowser = new DoBrowser();
        // Get guzzle client.
        // @todo swap all this for proper dependency injection.
        $client = $doBrowser->getClient()->getClient();
        $this->html = $client
            ->get($this->uri)
            ->send()
            ->getBody(TRUE);
        $this->crawler = new Crawler($this->html);

        return $this;
    }

    /**
     * Gets the URI of the issue.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Gets the latest patch file URI for an issue.
     *
     * @return array
     *   An array of the patch URIs on the most recent comment that might have
     *   been tested by testbot.
     */
    public function getLatestPatch()
    {
        $patches = $this->getPatches();
        $choices = array();
        foreach ($patches as $patch) {
            $choices[] = $patch;
            // Continue to loop if possible interdiff or patch meant to fail.
            if (strpos($patch, 'interdiff') === FALSE &&
                strpos($patch, 'fail') === FALSE
            ) {
                break;
            }
        }

        return $choices;
    }

    protected function getPatches()
    {
        if (!isset($this->patches)) {
            $files = $this->crawler
                ->filter('table#extended-file-field-table-field-issue-files td.extended-file-field-table-filename a')
                ->extract(array('href'));
            $this->patches = array_filter($files, function ($item) {
                if (preg_match('/\.(patch|diff)$/', $item) && !preg_match('/do(_|-)not(_|-)test\.patch$/', $item)) {
                    return TRUE;
                }

                return FALSE;
            });
        }

        return $this->patches;
    }

    /**
     * Does this issue have a patch attached.
     *
     * @return bool
     */
    public function hasPatch()
    {
        $patches = $this->getPatches();
        if (!empty($patches)) {
            return TRUE;
        }

        return FALSE;
    }
}
