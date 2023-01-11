<?php

namespace Drupal\ausy_family_event\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an event registration block.
 *
 * @Block(
 *   id = "registration_count_block",
 *   admin_label = @Translation("Event registration count block"),
 *   category = @Translation("Custom"),
 *   cache = {
 *     "contexts" = { "user" },
 *     "tags" = { "node:type:registration" },
 *     "max-age" = 60
 *   }
 * )
 */
class RegistrationCountBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $registration_count = $this->getRegistrationCount();
    return [
      '#markup' => $this->t('Registration count: @count', ['@count' => $registration_count]),
    ];
  }

  /**
   * Retrieves the registration count.
   *
   * @return int
   *   The registration count.
   */
  public function getRegistrationCount() {
  // Query the database to get the number of registration nodes.
  $query = \Drupal::entityQuery('node')
      ->condition('type', 'registration');
  $result = $query->count()->execute();
  return $result;
  }

}
