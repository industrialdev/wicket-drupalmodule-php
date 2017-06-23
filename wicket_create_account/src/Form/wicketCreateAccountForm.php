<?php
// https://www.drupal.org/docs/8/api/configuration-api/working-with-configuration-forms
namespace Drupal\wicket_create_account\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provide a wicket person create account form.
 */
class wicketCreateAccountForm extends ConfigFormBase {

  const FORM_ID = 'wicket_create_account_form';

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
      'wicket_create_account.settings',
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue($this->getFormId() . '_given_name')) == 0) {
     $form_state->setErrorByName($this->getFormId() . '_given_name', $this->t('Please enter your first name.'));
    }
    if (strlen($form_state->getValue($this->getFormId() . '_family_name')) == 0) {
     $form_state->setErrorByName($this->getFormId() . '_family_name', $this->t('Please enter your last name.'));
    }
    if (strlen($form_state->getValue($this->getFormId() . '_address')) == 0) {
     $form_state->setErrorByName($this->getFormId() . '_address', $this->t('Please enter your e-mail address.'));
    }
    if (!\Drupal::service('email.validator')->isValid($form_state->getValue($this->getFormId() . '_address'))) {
     $form_state->setErrorByName($this->getFormId() . '_address', $this->t('Please enter a valid e-mail address.'));
    }
    if (strlen($form_state->getValue($this->getFormId() . '_password')) == 0) {
     $form_state->setErrorByName($this->getFormId() . '_password', $this->t('Please enter a password.'));
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
    $config = $this->config('wicket_create_account.settings');

    $form[$this->getFormId() . '_given_name'] = array(
      '#type' => 'textfield',
      '#title' => t('First Name'),
      '#default_value' => $config->get($this->getFormId() . '_given_name', ''),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_family_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Last Name'),
      '#default_value' => $config->get($this->getFormId() . '_family_name', ''),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_address'] = array(
      '#type' => 'email',
      '#title' => t('Email'),
      '#default_value' => $config->get($this->getFormId() . '_address', ''),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_password'] = array(
      '#type' => 'password',
      '#title' => t('Password'),
      '#default_value' => $config->get($this->getFormId() . '_password', ''),
      '#required' => TRUE,
    );
    $form[$this->getFormId() . '_password_confirmation'] = array(
      '#type' => 'password',
      '#title' => t('Confirm Password'),
      '#default_value' => $config->get($this->getFormId() . '_password_confirmation', ''),
      '#required' => TRUE,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $client = wicket_api_client();
    $org = $client->organizations->all()->first(function ($item) {
      return $item->alternate_name == 'AOM';
    });

    $user = [
      'given_name'            => $form_state->getValue($this->getFormId() . '_given_name'),
      'family_name'           => $form_state->getValue($this->getFormId() . '_family_name'),
      'password'              => $form_state->getValue($this->getFormId() . '_password'),
      'password_confirmation' => $form_state->getValue($this->getFormId() . '_password_confirmation'),
    ];

    $person = new \Wicket\Entities\People($user);
    $email = new \Wicket\Entities\Emails([
      'address' => $form_state->getValue($this->getFormId() . '_address'),
      'primary' => true,
    ]);
    $person->attach($email);

    try {
      $new_person = $client->people->create($person, (object)$org);
    } catch (\Exception $e) {
      $errors = json_decode($e->getResponse()->getBody())->errors;
    }

    if (isset($errors)) {
      $counter = 1;
      $output = "<ul>";
      foreach ($errors as $key => $error) {
        if ($error->meta->field == 'given_name') {
          $prefix = t("First Name").' ';
          $output .= "<li><a href='#edit-".str_replace('_','-',$this->getFormId().'_given_name')."'><strong>".t("Error @count:", array('@count' => $counter)).'</strong> '.$prefix.t($error->title)."</a></li>";
        }
        if ($error->meta->field == 'family_name') {
          $prefix = t("Last Name").' ';
          $output .= "<li><a href='#edit-".str_replace('_','-',$this->getFormId().'_family_name')."'><strong>".t("Error @count:", array('@count' => $counter)).'</strong> '.$prefix.t($error->title)."</a></li>";
        }
        if ($error->meta->field == 'emails.address') {
          $prefix = t("Email").' ';
          $output .= "<li><a href='#edit-".str_replace('_','-',$this->getFormId().'_address')."'><strong>".t("Error @count:", array('@count' => $counter)).'</strong> '.$prefix.t($error->title)."</a></li>";
        }
        if ($error->meta->field == 'user.password') {
          $prefix = t("Password").' ';
          $output .= "<li><a href='#edit-".str_replace('_','-',$this->getFormId().'_password')."'><strong>".t("Error @count:", array('@count' => $counter)).'</strong> '.$prefix.t($error->title)."</a></li>";
        }
        if ($error->meta->field == 'user.password_confirmation') {
          $prefix = t("Confirm Password").' ';
          $output .= "<li><a href='#edit-".str_replace('_','-',$this->getFormId().'_password_confirmation')."'><strong>".t("Error @count:", array('@count' => $counter)).'</strong> '.$prefix.t($error->title)."</a></li>";
        }
        $counter++;
      }
      $output .= "</ul>";
      // Prevent the link from being escaped by the render system.
      $rendered_message = \Drupal\Core\Render\Markup::create($output);
      drupal_set_message(t('The account could not be created. @errors',['@errors' => $rendered_message]), 'error');
      $form_state->setRebuild();
    }else {
      // redirect instead of staying on page
      header('Location: /verify-account');
      die;
      // parent::submitForm($form, $form_state);
    }

  }
}
