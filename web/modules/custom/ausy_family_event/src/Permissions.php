<?php

namespace Drupal\ausy_family_event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Permissions.
 */
class Permissions implements EventSubscriberInterface {


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'user.role.grant.permissions' => 'roleGrantPermission',
    ];
  }

  /**
   * Grant permission to role.
   */
  public function roleGrantPermission(Event $event) {
    $role = $event->getRole();
    if ($role->id() == 'department_manager') {
      $role->grantPermission('manage event registrations');
      $role->save();
    }
  }

}