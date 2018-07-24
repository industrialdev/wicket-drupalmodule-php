<?php

namespace Drupal\wicket_create_account\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\wicket_create_account\Form\wicketCreateAccountSettingsForm;

/**
 * Provides route for showing the wicket create account form.
 */
class WicketCreateAccountController extends ControllerBase {

  /**
   * Returns the wicketCreateAccountForm form
   *
   * @return array
   *   A simple renderable array.
   */
  public function create_account() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $config = \Drupal::config('wicket_create_account_settings.settings');
    $browser_title = $config->get(wicketCreateAccountSettingsForm::FORM_ID . '_browser_title') != '' ? t($config->get(wicketCreateAccountSettingsForm::FORM_ID . '_browser_title')) : null;
    $page_title = $config->get(wicketCreateAccountSettingsForm::FORM_ID . '_page_title') != '' ? t($config->get(wicketCreateAccountSettingsForm::FORM_ID . '_page_title')) : null;
    $page_description = $config->get(wicketCreateAccountSettingsForm::FORM_ID . '_page_description') != '' ? t($config->get(wicketCreateAccountSettingsForm::FORM_ID . '_page_description')) : null;

    $form_class = '\Drupal\wicket_create_account\Form\wicketCreateAccountForm';
    $build['form'] = \Drupal::formBuilder()->getForm($form_class);

    return [
      '#title' => $browser_title,
      '#markup' => "<h1 class='wicket_create_account_header'>$page_title</h1>$page_description".t("<p class='wicket_required_create_account'>Required fields marked by <span class='required'>*</span></p>"),
      'form' => $build
    ];
  }

}
