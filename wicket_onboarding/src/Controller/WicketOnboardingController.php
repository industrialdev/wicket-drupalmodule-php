<?php

namespace Drupal\wicket_onboarding\Controller;

use Drupal\Core\Controller\ControllerBase;
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

    $current_base_name = $language == 'fr' ? '/fr/member/onboarding' : '/member/onboarding';
    $client = wicket_api_client();

    $uid = \Drupal::currentUser()->id();
    $userData = \Drupal::service('user.data');
    $person_id = $userData->get('wicket', $uid, 'personUuid');

    $config = \Drupal::config('wicket_onboarding.settings');
    $onboarding_embed_url = $config->get(wicketOnboardingSettingsForm::FORM_ID . '_embed_url');

    if (!$client) {
      drupal_set_message(t('Error initializing wicket api client'), 'error');
      return [];
    }

    if (empty($onboarding_embed_url)) {
      drupal_set_message(t('Invalid embed url'), 'error');
      return [];
    }

    if (empty($person_id)) {
      drupal_set_message(t('Invalid user.'), 'error');
      return [];
    }

    $client->authorize($person_id);

    $order_completed_path = $config->get(wicketOnboardingSettingsForm::FORM_ID . '_order_completed_path');
    $output = ["#markup" => '<div id="wicket-onboarding-content-root" class="wicket"></div>'];
    // https://medium.com/@ToddZebert/loading-and-using-javascript-in-drupal-8-f6643d19ae0f
    $output['#attached']['library'][] = 'wicket_onboarding/wicket_onboarding';
    $output['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        // The HTML tag to add, in this case a tag.
        '#tag' => 'script',
        // The value of the HTML tag, here we want to end up with
        '#value' => '',
        // Set attributes like src to load a file.
        '#attributes' => array('src' => $onboarding_embed_url),
      ],
      // A key, to make it possible to recognize this HTML element when altering.
      'wicket_onboarding',
    ];

    $output['#attached']['drupalSettings']['wicket_onboarding']['wicket_onboarding']['data'] =
    [
      'wicketOnboarding' => [
        'baseName' => $current_base_name,
        'apiRoot' => rtrim($client->getApiEndpoint(), '/'),
        'accessToken' => $client->getAccessToken(),
        'lang' => $language,
        'orderCompletePath' => $order_completed_path
      ]
    ];

    return $output;
  }

}
