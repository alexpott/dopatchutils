<?php

namespace DrupalPatchUtils;

/**
 * Provides access to a D.O issue.
 */
class IssueInfo {
  protected $issue;

  public function __construct($json) {
    $this->issue = json_decode($json);
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
        if (substr($url, -6) == ".patch" || substr($url, -5) == ".diff") {
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