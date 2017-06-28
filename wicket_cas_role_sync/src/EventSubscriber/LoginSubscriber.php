<?php

/**
 * @file
 * Contains \Drupal\wicket_cas_role_sync\EventSubscriber\LoginSubscriber.
 * https://drupalize.me/blog/201502/responding-events-drupal-8
 */

namespace Drupal\wicket_cas_role_sync\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class LoginSubscriber.
 *
 * @package Drupal\wicket_cas_role_sync
 */
class LoginSubscriber implements EventSubscriberInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['cas.pre_login'] = ['cas_pre_login_event'];

    return $events;
  }

  /**
   * This method is called whenever the CasPreLoginEvent event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function cas_pre_login_event($event) {
    $property_bag = $event->getCasPropertyBag();
    $cas_attributes = $property_bag->getAttributes();
    $personUuid = $cas_attributes['personUuid'][0];

    // https://www.webomelette.com/storing-user-data-such-preferences-drupal-8-using-userdata-service
    $userData = \Drupal::service('user.data');
    $uid = $event->getAccount()->id();
    $userData->set('wicket', $uid, 'personUuid', $personUuid);

    // drupal_set_message('Event search_api.task.addIndex thrown by LoginSubscriber in module wicket.', 'status', TRUE);
  }

}
