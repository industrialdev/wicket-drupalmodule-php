<?php
// https://www.drupal.org/docs/8/api/configuration-api/working-with-configuration-forms
namespace Drupal\wicket_update_password\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\wicket\Form\wicketSettingsForm;

/**
 * Provide a wicket person update password form.
 */
class wicketUpdatePasswordForm extends ConfigFormBase {

  const FORM_ID = 'wicket_update_password_form';

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
      'wicket_update_password.settings',
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue($this->getFormId() . '_current_password')) == 0) {
     $form_state->setErrorByName($this->getFormId() . '_current_password', $this->t('Please enter your current password.'));
    }
    if (strlen($form_state->getValue($this->getFormId() . '_password')) == 0) {
     $form_state->setErrorByName($this->getFormId() . '_password', $this->t('Please enter a password.'));
    }
    if (strlen($form_state->getValue($this->getFormId() . '_password_confirmation')) == 0) {
     $form_state->setErrorByName($this->getFormId() . '_password_confirmation', $this->t('Please confirm your new password.'));
    }
    if (strlen($form_state->getValue($this->getFormId() . '_password')) < 8) {
     $form_state->setErrorByName($this->getFormId() . '_password', $this->t('Password is too short (minimum is 8 characters)'));
    }
    if ($form_state->getValue($this->getFormId() . '_password') != $form_state->getValue($this->getFormId() . '_password_confirmation')) {
     $form_state->setErrorByName($this->getFormId() . '_password_confirmation', $this->t("Confirm Password doesn't match Password"));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('wicket_update_password.settings');

    $form[$this->getFormId() . '_current_password'] = array(
      '#type' => 'password',
      '#title' => t('Current Password'),
      '#default_value' => $config->get($this->getFormId() . '_current_password', ''),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_password'] = array(
      '#type' => 'password',
      '#title' => t('New Password'),
      '#default_value' => $config->get($this->getFormId() . '_password', ''),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_password_confirmation'] = array(
      '#type' => 'password',
      '#title' => t('Confirm New Password'),
      '#default_value' => $config->get($this->getFormId() . '_password_confirmation', ''),
      '#required' => TRUE,
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
    $client = wicket_api_client();
    $userData = \Drupal::service('user.data');
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $person_id = $userData->get('wicket', $user->id(), 'personUuid');
    $person = wicket_get_person_by_id($person_id);
    $update_user = new \Wicket\Entities\People(['user' => ['current_password' => $form_state->getValue($this->getFormId() . '_current_password'),
                                                          'password' => $form_state->getValue($this->getFormId() . '_password'),
                                                          'password_confirmation' => $form_state->getValue($this->getFormId() . '_password_confirmation')
                                                         ]
                                              ]);
    $update_user->id = $person->id;
    $update_user->type = $person->type;

    try {
      $client = wicket_api_client();
      $client->people->update($update_user);
    } catch (Exception $e) {
      $errors_obj = json_decode($e->getResponse()->getBody())->errors;
      // typically if it fails at this point, the current password is likely invalid.
      // set it up though to catch errors regardless
      foreach ($errors_obj as $error) {
        switch ($error->meta->field) {
          case 'user.current_password':
            $current_pass = [];
            $current_pass['meta']['field'] = 'user.current_password';
            $current_pass['title'] = t($error->title);
            $errors[] = $current_pass;
            break;
        }
      }
    }
    // redirect here if there was updates made and prevent form re-submission
    if (empty($errors)) {
      drupal_set_message(t('Password has been successfully updated'));
      header('Location:'.\Drupal::url('<current>'));
      die;
    }

    if (isset($errors)) {
      $counter = 1;
      $output = "<ul>";
      foreach ($errors as $key => $error) {
        if ($error->meta->field == 'user.password') {
          $prefix = t("Password").' ';
          $output .= "<li><a href='#edit-".str_replace('_','-',$this->getFormId().'_password')."'><strong>".t("Error @count:", array('@count' => $counter)).'</strong> '.$prefix.t($error->title)."</a></li>";
        }
        if ($error->meta->field == 'user.password_confirmation') {
          $prefix = t("Confirm New Password").' ';
          $output .= "<li><a href='#edit-".str_replace('_','-',$this->getFormId().'_password_confirmation')."'><strong>".t("Error @count:", array('@count' => $counter)).'</strong> '.$prefix.t($error->title)."</a></li>";
        }
        $counter++;
      }
      $output .= "</ul>";
      // Prevent the link from being escaped by the render system.
      $rendered_message = \Drupal\Core\Render\Markup::create($output);
      drupal_set_message(t('The password could not be updated. @errors',['@errors' => $rendered_message]), 'error');
      $form_state->setRebuild();
    }

  }
}
