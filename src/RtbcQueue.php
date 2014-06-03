<?php

namespace DrupalPatchUtils;

use Guzzle\Http\Client;
use Guzzle\Http\Url;
use Symfony\Component\DomCrawler\Crawler;

class RtbcQueue
{
    const DEFAULT_URI = 'https://drupal.org/project/issues/drupal?status=14&version=8.x&text=&priorities=All&categories=All&component=All&order=last_comment_timestamp&sort=asc';

    /**
     * @var \Guzzle\Http\Url
     */
    protected $uri;
    protected $issueUris = [];
    protected $nextPage = 0;

    public function __construct($uri = null)
    {
        if (empty($uri)) {
            $uri = $this::DEFAULT_URI;
        }

        $this->uri = Url::factory($uri);
    }

    /**
     * Gets an array of issue uri's from an d.o project issue page(s). Will use
     * the pager to determine when all the issues have been scraped.
     *
     * @return array
     *   An array of d.o issue uris.
     */
    public function getIssueUris()
    {
        if (empty($this->issueUris)) {
            while ($page = $this->getPage()) {
                $issues = $page->filter('table.project-issue td.views-field-title a');
                foreach ($issues as $issue) {
                    $this->issueUris[] = $this->uri->getScheme() . '://' . $this->uri->getHost() . $issue->getAttribute('href');
                }
            }
        }

        return $this->issueUris;
    }

    /**
     * Gets the next page of issues.
     *
     * @return bool|Crawler
     *   A crawler object representing the next page or FALSE if there are no more
     *   pages.
     */
    protected function getPage()
    {
        if ($this->nextPage === false) {
            return false;
        }

        $client = new Client();
        $request = $client->get($this->uri . '&page=' . $this->nextPage . '&' . substr(md5(microtime()), rand(0, 26), 5));
        $response = $request->send();

        // Set nextPage to FALSE if we've read the last page.
        $crawler = new Crawler((string) $response->getBody());
        $found = $crawler->filter('ul.pager li.pager-next a');
        if (!$found->count()) {
            $this->nextPage = false;
        } else {
            $this->nextPage++;
        }

        return $crawler;
    }
}
