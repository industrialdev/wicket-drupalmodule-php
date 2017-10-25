<?php
// https://www.drupal.org/docs/8/api/configuration-api/working-with-configuration-forms
namespace Drupal\wicket\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure wicket settings for this site.
 */
class wicketSettingsForm extends ConfigFormBase {

  const FORM_ID = 'wicket_admin_settings';

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
      'wicket.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('wicket.settings');

    $form[$this->getFormId() . '_api_endpoint'] = array(
      '#type' => 'textfield',
      '#title' => t('API Endpoint'),
      '#default_value' => $config->get($this->getFormId() . '_api_endpoint', ''),
      '#description' => t('API endpoint'),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_app_key'] = array(
      '#type' => 'textfield',
      '#title' => t('APP Key'),
      '#default_value' => $config->get($this->getFormId() . '_app_key', ''),
      '#description' => t('APP key from wicket'),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_secret_key'] = array(
      '#type' => 'textfield',
      '#title' => t('JWT Secret Key'),
      '#default_value' => $config->get($this->getFormId() . '_secret_key', ''),
      '#description' => t('Secret key from wicket'),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_person_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Person ID'),
      '#default_value' => $config->get($this->getFormId() . '_person_id', ''),
      '#description' => t('Person ID from wicket'),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_parent_org'] = array(
      '#type' => 'textfield',
      '#title' => t('Parent Org'),
      '#default_value' => $config->get($this->getFormId() . '_parent_org', ''),
      '#description' => t('Top level organization used for creating new people on the create account form. <br>This is the "alternate name" found in Wicket under "Organizations" for the top most organization.'),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_wicket_admin'] = array(
      '#type' => 'textfield',
      '#title' => t('Wicket Admin'),
      '#default_value' => $config->get($this->getFormId() . '_wicket_admin', ''),
      '#description' => t('The address of the admin interface. Ex: https://wicket.abc.ind.ninja'),
      '#required' => TRUE,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->config('wicket.settings')
      // Set the submitted configuration setting
      ->set($this->getFormId() . '_api_endpoint', $form_state->getValue($this->getFormId() . '_api_endpoint'))
      ->set($this->getFormId() . '_app_key', $form_state->getValue($this->getFormId() . '_app_key'))
      ->set($this->getFormId() . '_secret_key', $form_state->getValue($this->getFormId() . '_secret_key'))
      ->set($this->getFormId() . '_person_id', $form_state->getValue($this->getFormId() . '_person_id'))
      ->set($this->getFormId() . '_parent_org', $form_state->getValue($this->getFormId() . '_parent_org'))
      ->set($this->getFormId() . '_wicket_admin', $form_state->getValue($this->getFormId() . '_wicket_admin'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
