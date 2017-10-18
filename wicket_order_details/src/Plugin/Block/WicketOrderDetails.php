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
    $output = $this->wicket_order_details_block_view();
    $client = wicket_api_client();

    $uid = \Drupal::currentUser()->id();
    $userData = \Drupal::service('user.data');
    $person_id = $userData->get('wicket', $uid, 'personUuid');
    $client->authorize($person_id);

    if (!$client) {
      $output = t('Error initializing wicket api client');
    }

    if (empty($person_id)) {
      $output = t('Invalid user');
    }

    $build = [
      '#theme' => 'order_details',
      '#output' => $output,
      '#api_root' => rtrim($client->getApiEndpoint(), '/'),
      '#access_token' => $client->getAccessToken(),
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

  function wicket_order_details_block_view() {
    return ['#markup' => '<p>testing the order details</p>'];
  }

}
