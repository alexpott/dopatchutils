<?php

/**
 * @file
 * Contains \DrupalPatchUtils\IssueForm.
 */

namespace DrupalPatchUtils;

use Symfony\Component\DomCrawler\Field\ChoiceFormField;

class IssueForm extends DoFormBase {

  public function setTitle($title) {
    $title_form = $this->form->get('title');
    $title_form->setValue($title);
    $this->form->set($title_form);
    return $this;
  }

  public function setBody($body) {
    $body_form = $this->form->get('body[und][0][value]');
    $body_form->setValue($body);
    $this->form->set($body_form);
    return $this;
  }

}

