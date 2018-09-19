<?php
namespace Drupal\wicket_order_history\Plugin\Block;

/**
 * @file
 * Contains \Drupal\wicket_order_history\Plugin\Block\WicketOrderHistory.
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
 *   id = "wicket_order_history",
 *   admin_label = @Translation("Wicket Order History"),
 *   category = @Translation("Custom")
 * )
 */

class WicketOrderHistory extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $orders = $this->wicket_order_history_build();
    $client = wicket_api_client_current_user();

    if (!$client) {
      $orders = t('Error initializing wicket api client');
    }

    $build = [
      '#theme' => 'order_history',
      '#orders' => $orders,
      '#cache' => [
        'context' => ['url.query_args'],
        'max-age' => 0
      ]
    ];

    return $build;

  }

  function wicket_order_history_build(){
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    try {
      $orders = wicket_get_person_orders(wicket_current_person_uuid());
      $orders = $orders ? array_reverse($orders->all()) : null;
    } catch (Exception $e) {
    }

    if ($orders) {
      $built_orders = [];
      foreach ($orders as $order) {
        $temp_order = [];

        $interval = wicket_get_included_resource($order, $order->relationship('interval')[0]);
        $order_interval_starts_at = strtotime($interval['attributes']['starts_at']);

        // only show completed orders
        // if (($order->state != 'completed' && $order->state != 'refunded') || $order->completed_at == '') {
          // continue;
        // }

        $temp_order['order_number'] = $order->number;
        $date = $order->created_at;
        $temp_order['order_date'] = format_date(strtotime($date), 'custom', 'F j, Y');
        $temp_order['order_total'] = $language == 'fr' ? number_format($order->total, 2, ',', "." ).' $' : '$'.number_format($order->total, 2, '.', "." );
        $temp_order['order_status'] = ucfirst(t($order->state));
        $temp_order['order_details_link'] = '/order-history-details?order_id='.$order->id;

        $built_orders[] = $temp_order;
      }
      return $built_orders;
    }
  }

}
