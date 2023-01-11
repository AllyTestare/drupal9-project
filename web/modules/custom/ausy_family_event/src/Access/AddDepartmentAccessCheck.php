<?php

namespace Drupal\ausy_family_event\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;

class AddDepartmentAccessCheck implements AccessInterface {
    public function access(AccountInterface $account) {
        return AccessResult::allowedIfHasPermission($account, 'administer add department');
    }
}