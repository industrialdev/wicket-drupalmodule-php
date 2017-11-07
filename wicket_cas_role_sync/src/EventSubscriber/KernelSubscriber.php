<?php

/**
 * @file
 * Contains \Drupal\wicket_cas_role_sync\EventSubscriber\KernelSubscriber.
 * https://drupalize.me/blog/201502/responding-events-drupal-8
 */

namespace Drupal\wicket_cas_role_sync\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\EventDispatcher\Event;

/**
 * Class KernelSubscriber.
 *
 * @package Drupal\wicket_cas_role_sync
 */
class KernelSubscriber implements EventSubscriberInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['kernel.request'] = ['kernel_event'];

    return $events;
  }

  /**
   * This method is called whenever the Kernel event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function kernel_event($event) {
    $userData = \Drupal::service('user.data');
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $person_id = $userData->get('wicket', $user->id(), 'personUuid');

    /**-------------------------------------------------------------------
     * SYNC ROLES - similar to how we do it on login (LoginSubscriber.php)
     -------------------------------------------------------------------*/
    if ($person_id) {
      // get current user roles from Wicket
      $roles = wicket_get_person_roles_by_id($person_id);

      // sync roles for the user in Drupal
      if ($roles) {
        // first remove all user roles before we sync
        foreach ($user->getRoles() as $role) {
          $user->removeRole($role);
        }
        // get all whitelisted roles
        $whitelisted_roles = get_whitelisted_wicket_roles();

        foreach ($roles as $uuid => $role) {
          // if we need to skip certain roles, do it
          if (!empty($whitelisted_roles) && !in_array($uuid, $whitelisted_roles)) {
            continue;
          }
          // skip the Wicket "administrator" role from being synced.
          // this gets too confusing when in Drupal considering its built in "Administrator" role.
          // Hence why usually a new role in Wicket is created called "Drupal Admin" instead
          if ($role == 'administrator') {
            continue;
          }
          // first check to see if this role exists in Drupal
          $role_exists = \Drupal\user\Entity\Role::load($uuid);
          // if not, create it
          if (!$role_exists) {
            $new_role = array('id' => $uuid, 'label' => $role);
            $new_role = \Drupal\user\Entity\Role::create($new_role);
            $new_role->save();
          }
          if (!$user->hasRole($uuid)) {
            $user->addRole($uuid);
            $user->save();
          }
        }
      }
    }
  }

}
