<?php
// https://www.drupal.org/docs/8/api/configuration-api/working-with-configuration-forms
namespace Drupal\wicket_cas_role_sync\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure wicket CAS role sync settings for this site.
 */
class wicketCasRoleSyncSettingsForm extends ConfigFormBase {

  const FORM_ID = 'wicket_cas_role_sync_admin_settings';

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
      'wicket_cas_role_sync.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('wicket_cas_role_sync.settings');

    $client = wicket_api_client();
    $roles = $client->roles;


    kint($roles);


    // $form[$this->getFormId() . '_embed_url'] = array(
    //   '#type' => 'textfield',
    //   '#title' => t('Onboarding embed url'),
    //   '#default_value' => $config->get($this->getFormId() . '_embed_url', ''),
    //   '#description' => t('URL used to embed the onboarding javascript application.'),
    //   '#required' => TRUE,
    // );
    // $form[$this->getFormId() . '_order_completed_path'] = array(
    //   '#type' => 'textfield',
    //   '#title' => t('Onboarding order completed redirect path'),
    //   '#default_value' => $config->get($this->getFormId() . '_order_completed_path', ''),
    //   '#description' => t('Path to redirect member to after completing order.'),
    //   '#required' => TRUE,
    // );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->config('wicket_cas_role_sync.settings')
      // Set the submitted configuration setting
      ->set($this->getFormId() . '_embed_url', $form_state->getValue($this->getFormId() . '_embed_url'))
      ->set($this->getFormId() . '_order_completed_path', $form_state->getValue($this->getFormId() . '_order_completed_path'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
