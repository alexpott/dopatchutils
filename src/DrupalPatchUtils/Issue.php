<?php

namespace DrupalPatchUtils;

use Guzzle\Http\Client;

/**
 * Provides access to a D.O issue.
 */
class Issue {
  protected $issue;

  /**
   * @var string
   */
  protected $uri;

  /**
   * @param string $issue_id
   *   NID or URI of an issue.
   */
  public function __construct($issue_id) {
    if (is_numeric($issue_id)) {
      $this->uri = 'https://drupal.org/node/' . $issue_id . '/project-issue/json';
    }
    elseif (filter_var($issue_id, FILTER_VALIDATE_URL) !== false) {
      $this->uri = $issue_id . '/project-issue/json';
    }
    $this->getIssue();
  }

  protected function getIssue() {
    $client = new Client();
    $request = $client->get($this->uri);
    $response = $request->send();
    $this->issue = json_decode($response->getBody());
    return $this;
  }
  /**
   * Gets the URI of the issue.
   *
   * @return string
   */
  public function getUri() {
    return $this->issue->url;
  }

  /**
   * Gets the latest patch file URI for an issue.
   *
   * @return array
   *   An array of the patch URIs on the most recent comment that might have
   *   been tested by testbot.
   */
  public function getLatestPatch() {
    $comment_patches = array();
    foreach ($this->issue->attachments as $comment_number => $attachment) {
      $comment_patches[$comment_number] = array();
      foreach ($attachment->urls as $url) {
        if (preg_match('/\.(patch|diff)$/', $url) && !preg_match('/\.do_not_test\.patch$/', $url)) {
          $comment_patches[$comment_number][] = $url;
        }
      }
      if (empty($comment_patches[$comment_number])) {
        unset($comment_patches[$comment_number]);
      }
    }
    if (count($comment_patches)) {
      // Get the last valid comment patches.
      $comment_patches = array_pop($comment_patches);
    }
    return $comment_patches;
  }

  /**
   * Does this issue have a patch attached.
   *
   * @return bool
   */
  public function hasPatch() {
    foreach ($this->issue->attachments as $comment_number => $attachment) {
      foreach ($attachment->urls as $url) {
        if (substr($url, -6) == ".patch" || substr($url, -5) == ".diff") {
          return TRUE;
        }
      }
    }
    return FALSE;
  }
}