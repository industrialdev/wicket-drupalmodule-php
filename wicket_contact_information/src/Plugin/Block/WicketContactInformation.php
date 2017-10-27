<?php
namespace Drupal\wicket_contact_information\Plugin\Block;

/**
 * @file
 * Contains \Drupal\wicket_contact_information\Plugin\Block\WicketContactInformation.
 */
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Site\Settings;
use Drupal\wicket\Form\wicketSettingsForm;
/**
 * Provides a form from Wicket to update person contact information
 *
 * @Block(
 *   id = "wicket_contact_information",
 *   admin_label = @Translation("Wicket Contact Information"),
 *   category = @Translation("Custom")
 * )
 */

class WicketContactInformation extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $output = '';
    $client = wicket_api_client_current_user();
    $config = \Drupal::config('wicket.settings');
    $wicket_admin = $config->get(wicketSettingsForm::FORM_ID . '_wicket_admin') ?? null;
    $uid = \Drupal::currentUser()->id();
    $userData = \Drupal::service('user.data');
    $person_id = $userData->get('wicket', $uid, 'personUuid');

    if (!$client) {
      $output = t('Error initializing wicket api client');
    }

    $build = [
      '#theme' => 'contact_information',
      '#output' => $output,
      '#api_root' => rtrim($client->getApiEndpoint(), '/'),
      '#access_token' => $client->getAccessToken(),
      '#language' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      '#person_id' => $person_id,
      '#attached' => [
        'library' => ['wicket_contact_information/wicket_admin_react'],
        'drupalSettings' => [
          'wicket_contact_information' => [
            'wicket_admin_react_url' => "$wicket_admin/dist/widgets.js"
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
