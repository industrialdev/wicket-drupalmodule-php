<?php

namespace Drupal\wicket_create_account\Controller;

use Drupal\Core\Controller\ControllerBase;

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

    $form_class = '\Drupal\wicket_create_account\Form\wicketCreateAccountForm';
    $build['form'] = \Drupal::formBuilder()->getForm($form_class);

    return [
      '#title' => 'Create your AOM Login Account',
      '#markup' => '<h1>Create your AOM Login Account</h1><p>To become a member or place an order with the Association of Ontario Midwives, please start by creating a login account. After you submit, check your email to confirm your account.</p>
       <p>Required fields marked by <span class="required">*</span></p>',
      'form' => $build];
  }

}
