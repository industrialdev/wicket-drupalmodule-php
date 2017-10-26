<?php
// https://www.drupal.org/docs/8/api/configuration-api/working-with-configuration-forms
namespace Drupal\wicket_create_account\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\wicket\Form\wicketSettingsForm;

/**
 * Provide a form to manage settings for the Wicket person create account form.
 */
class wicketCreateAccountSettingsForm extends ConfigFormBase {

  const FORM_ID = 'wicket_create_account_settings_form';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return self::FORM_ID;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'wicket_create_account_settings.settings',
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('wicket_create_account_settings.settings');

    $form[$this->getFormId() . '_browser_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Browser Title'),
      '#default_value' => $config->get($this->getFormId() . '_browser_title', ''),
      '#required' => TRUE,
      '#description' => t('The title for the page that will appear in the browser tab'),
    );
    $form[$this->getFormId() . '_page_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Page Title'),
      '#default_value' => $config->get($this->getFormId() . '_page_title', ''),
      '#required' => TRUE,
      '#description' => t('The title for the page that will appear in the page as a heading'),
    );
    $form[$this->getFormId() . '_page_description'] = array(
      '#type' => 'textarea',
      '#title' => t('Page Description'),
      '#default_value' => $config->get($this->getFormId() . '_page_description', ''),
      '#required' => TRUE,
      '#description' => t('The description for the page that will appear above the form. Include any needed HTML in here as well'),
    );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->config('wicket_create_account_settings.settings')
      // Set the submitted configuration setting
      ->set($this->getFormId() . '_browser_title', $form_state->getValue($this->getFormId() . '_browser_title'))
      ->set($this->getFormId() . '_page_title', $form_state->getValue($this->getFormId() . '_page_title'))
      ->set($this->getFormId() . '_page_description', $form_state->getValue($this->getFormId() . '_page_description'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
