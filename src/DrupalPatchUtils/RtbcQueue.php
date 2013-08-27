<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 27/08/2013
 * Time: 08:50
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils;

use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Uri\Uri;

class RtbcQueue {

  protected $uri;

  protected $issueUris = array();

  protected $nextPage = 0;

  public function __construct($uri = 'https://drupal.org/project/issues/drupal?order=last_comment_timestamp&sort=asc&status=14&version=8.x&text=&priorities=All&categories=All&component=All') {
    $this->uri = new Uri($uri);
  }

  /**
   * Gets an array of issue uri's from an d.o project issue page(s). Will use
   * the pager to determine when all the issues have been scraped.
   *
   * @return array
   *   An array of d.o issue uris.
   */
  public function getIssueUris () {
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
  protected function getPage() {
    if ($this->nextPage === FALSE) {
      return FALSE;
    }
    $request = new Request();
    $request->setUri($this->uri . '&page=' . $this->nextPage);

    $client = new Client();
    $response = $client->dispatch($request);

    // Set nextPage to FALSE if we've read the last page.
    $crawler = new Crawler($response->getBody());
    $found = $crawler->filter('ul.pager li.pager-next');
    if (!$found->count()) {
      $this->nextPage = FALSE;
    }
    else {
      $this->nextPage++;
    }
    return $crawler;
  }
}