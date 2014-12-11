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

  /**
   * Returns the list of available components.
   *
   * @return array
   */
  public function getComponents() {
    /** @var ChoiceFormField $component_form */
    $component_form = $this->form->get('field_issue_component[und]');
    return $component_form->availableOptionValues();
  }

  public function setComponent($component) {
    $component_form = $this->form->get('field_issue_component[und]');
    $component_form->setValue($component);
    $this->form->set($component_form);
    return $this;
  }

  /**
   * Returns the list of available versions.
   *
   * @return string[]
   */
  public function getVersions() {
    /** @var ChoiceFormField $version_form */
    $version_form = $this->form->get('field_issue_version[und]');
    return $version_form->availableOptionValues();
  }

  public function setVersion($version) {
    $version_form = $this->form->get('field_issue_version[und]');
    $version_form->setValue($version);
    $this->form->set($version_form);
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

