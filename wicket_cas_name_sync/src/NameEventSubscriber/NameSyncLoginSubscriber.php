<?php

/**
 * @file
 * Contains \Drupal\wicket_cas_name_sync\NameEventSubscriber\NameSyncLoginSubscriber.
 * https://drupalize.me/blog/201502/responding-events-drupal-8
 */

namespace Drupal\wicket_cas_name_sync\NameEventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NameSyncLoginSubscriber.
 *
 * @package Drupal\wicket_cas_name_sync
 */
class NameSyncLoginSubscriber implements EventSubscriberInterface {

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
    // get the person UUID
    $property_bag = $event->getCasPropertyBag();
    $cas_attributes = $property_bag->getAttributes();
    $personUuid = $cas_attributes['personUuid'][0];

    // https://www.webomelette.com/storing-user-data-such-preferences-drupal-8-using-userdata-service
    // save the full_name to their Drupal account
    $userData = \Drupal::service('user.data');
    $uid = $event->getAccount()->id();
    // get current person from Wicket
    $person = wicket_get_person_by_id($personUuid);
    $userData->set('wicket', $uid, 'person_full_name', $person->full_name);
  }
}
