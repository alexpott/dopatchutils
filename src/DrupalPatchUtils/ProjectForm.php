<?php

/**
 * @file
 * Contains \DrupalPatchUtils\ProjectForm.
 */

namespace DrupalPatchUtils;

class ProjectForm extends DoFormBase {

  public function setTitle($title) {
    $title_form = $this->form->get('title');
    $title_form->setValue($title);
    $this->form->set($title_form);
    return $this;
  }

  public function setComponent($component) {
    $component_form = $this->form->get('field_issue_component[und]');
    $component_form->setValue($component);
    $this->form->set($component_form);
    return $this;
  }

  public function setCategory($category) {
    $category_form = $this->form->get('field_issue_category[und]');
    $category_form->setValue($category);
    $this->form->set($category_form);
    return $this;
  }

  public function setBody($body) {
    $body_form = $this->form->get('body[und][0][value]');
    $body_form->setValue($body);
    $this->form->set($body_form);
    return $this;
  }

}

