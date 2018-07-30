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

    // get all roles from Wicket to build the list
    $client = wicket_api_client();
    $roles = $client->roles->all();
    $role_options = [];
    if ($roles) {
      foreach ($roles as $role) {
        // skip the Wicket "administrator" role from being shown as an option.
        // this gets too confusing when in Drupal considering its built in "Administrator" role.
        // Hence why usually a new role in Wicket is created called "Drupal Admin" instead
        if ($role->name == 'administrator') {
          continue;
        }
        $role = $client->roles->fetch($role->id);
        $role_org_id = $role->relationship('resource')[0]->id ?? '';
        $role_id = str_replace('-','_',$role->id);
        // if this role has an associated ORG, get its name and add as a suffix
        if ($role_org_id) {
          $client = wicket_api_client();
          $org = $client->organizations->fetch($role_org_id);
          // convert dashes to underscores for Drupal's role machine name. It doesn't accept dashes
          $role_options[$role_id] = $org->legal_name.' - '.$role->name;
        }else {
          $role_options[$role_id] = $role->name;
        }
      }
    }

    $form[$this->getFormId() . '_sync_all_roles'] = array(
      '#type' => 'checkbox',
      '#title' => t('Sync all roles'),
      '#default_value' => $config->get($this->getFormId() . '_sync_all_roles', ''),
      '#description' => t('If checked, all roles will be considered for synchronization into Drupal.'),
    );
    $form[$this->getFormId() . '_sync_roles_during_every_page_request'] = array(
      '#type' => 'checkbox',
      '#title' => t('Sync roles during each page request'),
      '#default_value' => $config->get($this->getFormId() . '_sync_roles_during_every_page_request', ''),
      '#description' => t('If checked, role synchronization will occur during each page request. <br>NOTE: If this functionality isn\'t needed, this should be turned off for performance reasons.'),
    );
    $form[$this->getFormId() . '_whitelisted_roles'] = array(
      '#type' => 'checkboxes',
      '#options' => $role_options,
      '#title' => t('Whitelisted Roles'),
      '#default_value' => $config->get($this->getFormId() . '_whitelisted_roles', ''),
      '#description' => t('These are all the roles in Wicket. Choose which to whitelist (which ones will be considered for synchronization).'),
    );
    $form['#attached']['library'][] = 'wicket_cas_role_sync/wicket_cas_role_sync';

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->config('wicket_cas_role_sync.settings')
      // Set the submitted configuration setting
      ->set($this->getFormId() . '_sync_all_roles', $form_state->getValue($this->getFormId() . '_sync_all_roles'))
      ->set($this->getFormId() . '_sync_roles_during_every_page_request', $form_state->getValue($this->getFormId() . '_sync_roles_during_every_page_request'))
      ->set($this->getFormId() . '_whitelisted_roles', $form_state->getValue($this->getFormId() . '_whitelisted_roles'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
