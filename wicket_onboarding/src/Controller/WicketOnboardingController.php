<?php

namespace Drupal\wicket_onboarding\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\wicket\Form\wicketSettingsForm;
use Drupal\wicket_onboarding\Form\wicketOnboardingSettingsForm;

/**
 * Provides route responses for the wicket onboarding module.
 */
class WicketOnboardingController extends ControllerBase {

  /**
   * Returns the Wicket Onboarding JS React.
   *
   * @return array
   *   A simple renderable array.
   */
  public function onboarding() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $client = wicket_api_client_current_user();
    $current_base_name = \Drupal\Core\Url::fromUserInput('/member/onboarding')->toString();

    $uid = \Drupal::currentUser()->id();
    $userData = \Drupal::service('user.data');
    $person_id = $userData->get('wicket', $uid, 'personUuid');

    $onboarding_config = \Drupal::config('wicket_onboarding.settings');
    $onboarding_embed_url = $onboarding_config->get(wicketOnboardingSettingsForm::FORM_ID . '_embed_url');
    $order_completed_path = $onboarding_config->get(wicketOnboardingSettingsForm::FORM_ID . '_order_completed_path');
    $wicket_config = \Drupal::config('wicket.settings');
    $wicket_admin = $wicket_config->get(wicketSettingsForm::FORM_ID . '_wicket_admin') ?? null;

    $output = '';
    if (!$client) {
      $output = t('Error initializing wicket api client');
    }

    if (empty($onboarding_embed_url)) {
      $output = t('Invalid embed url');
    }

    if (empty($person_id)) {
      $output = t('Invalid user.');
    }

    $client->authorize($person_id);

    $build = [
      '#theme' => 'onboarding',
      '#output' => $output,
      '#api_root' => rtrim($client->getApiEndpoint(), '/'),
      '#access_token' => $client->getAccessToken(),
      '#language' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      '#base_name' => $current_base_name,
      '#order_completed_path' => $order_completed_path,
      '#attached' => [
        'library' => ['wicket_onboarding/wicket_admin_react'],
        'drupalSettings' => [
          'wicket_onboarding' => [
            'wicket_admin_react_url' => $onboarding_embed_url
          ]
        ]
      ],
      '#cache' => [
        'context' => ['url.query_args'],
        'max-age' => 0
      ]
    ];

    return $build;
  }

}
