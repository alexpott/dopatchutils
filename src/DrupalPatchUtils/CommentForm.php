<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 01/09/2013
 * Time: 15:23
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils;


use Symfony\Component\DomCrawler\Form;

class CommentForm extends DoFormBase {

  public function setCommentText($text) {
    $comment = $this->form->get('nodechanges_comment[comment_body][und][0][value]');
    $comment->setValue($text);
    $this->form->set($comment);
    return $this;
  }

  public function addIssueCredit($text) {
    $comment = $this->form->get('add_credit');
    $comment->setValue($text);
    $this->form->set($comment);
    return $this;
  }

}
