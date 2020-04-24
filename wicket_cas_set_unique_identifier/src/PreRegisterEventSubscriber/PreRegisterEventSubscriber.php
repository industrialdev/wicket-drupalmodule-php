<?php

/**
 * @file
 * Contains \Drupal\wicket_cas_set_unique_identifier\PreRegisterEventSubscriber\PreRegisterEventSubscriber.
 * https://drupalize.me/blog/201502/responding-events-drupal-8
 * https://www.drupal.org/project/cas/issues/3015997
 */

namespace Drupal\wicket_cas_set_unique_identifier\PreRegisterEventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PreRegisterEventSubscriber.
 *
 * @package Drupal\wicket_cas_set_unique_identifier
 */
class PreRegisterEventSubscriber implements EventSubscriberInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['cas.pre_register'] = ['cas_pre_register_event'];
    $events['cas.pre_user_load'] = ['cas_pre_user_load_event'];
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
    // This method is called when the local user
    // account is already loaded. We can use it to alter
    // account values like roles, field values, etc.
    $property_bag = $event->getCasPropertyBag();
    $cas_attributes = $property_bag->getAttributes();
    $personUuid = $cas_attributes['personUuid'][0];
    $wicket_email = $cas_attributes['email'][0];

    $drupal_account = $event->getAccount();
    // update their email value in Drupal if they'd changed it in wicket
    if ($drupal_account->mail->value != $wicket_email || $drupal_account->name->value != $wicket_email) {
      $drupal_account->setEmail($wicket_email);
      $drupal_account->setUsername($wicket_email);
    }
  }

  /**
   * This method is called whenever the CasPreUserLoadEvent event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function cas_pre_user_load_event($event) {
    // We use this method to change the value that's used to lookup
    // a local account on each login to be the Wicket UUID
    // (that should already be populated in the CAS Username field)
    $property_bag = $event->getCasPropertyBag();
    $cas_attributes = $property_bag->getAttributes();
    $personUuid = $cas_attributes['personUuid'][0];
    $property_bag->setUsername($personUuid);
  }

  /**
   * This method is called whenever the CasPreRegisterEvent event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function cas_pre_register_event($event) {
    // Use the Wicket person UUID as the value for the "CAS Username" field
    // during initial creation of account in Drupal
    // It's used instead of email as it's unchanging and unique
    $property_bag = $event->getCasPropertyBag();
    $cas_attributes = $property_bag->getAttributes();
    $personUuid = $cas_attributes['personUuid'][0];
    $property_bag->setUsername($personUuid);
  }
}
