<?php
namespace Drupal\wicket_order_details\Plugin\Block;

/**
 * @file
 * Contains \Drupal\wicket_order_details\Plugin\Block\WicketOrderDetails.
 */
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Site\Settings;
/**
 * Provides a detailed view for a Wicket Order
 *
 * @Block(
 *   id = "wicket_order_details",
 *   admin_label = @Translation("Wicket Order Details"),
 *   category = @Translation("Custom")
 * )
 */

class WicketOrderDetails extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $output = '';
    $client = wicket_api_client_current_user();

    if (!$client) {
      $output = t('Error initializing wicket api client');
    }

    $build = [
      '#theme' => 'order_details',
      '#output' => $output,
      '#api_root' => rtrim($client->getApiEndpoint(), '/'),
      '#access_token' => $client->getAccessToken(),
      '#language' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      '#order_id' => $_GET['order_id'] ?? '',
      '#attached' => [
        'library' => ['wicket_order_details/wicket_admin_react'],
        'drupalSettings' => [
          'wicket_order_details' => [
            'wicket_admin_react_url' => 'https://wicket.aom.ind.ninja/dist/widgets.js'
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
