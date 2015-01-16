<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 01/09/2013
 * Time: 15:23
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils;

class CommentForm extends DoFormBase {

  public function setCommentText($text) {
    $comment = $this->form->get('nodechanges_comment_body[value]');
    $comment->setValue($text);
    $this->form->set($comment);
    return $this;
  }

}
