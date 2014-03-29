<?php

/**
 * @file
 * Contains \Drupal\payment\Entity\PaymentStatusStorage.
 */

namespace Drupal\payment\Entity;

use Drupal\Component\Plugin\Discovery\CachedDiscoveryInterface;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\payment\Plugin\Payment\Status\PaymentStatusManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handles storage for payment_status entities.
 */
class PaymentStatusStorage extends ConfigEntityStorage {

  /**
   * The payment status plugin manager.
   *
   * @var \Drupal\payment\Plugin\Payment\Status\PaymentStatusManagerInterface
   */
  protected $paymentStatusManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeInterface $entity_info, ConfigFactoryInterface $config_factory, StorageInterface $config_storage, PaymentStatusManagerInterface $payment_status_manager, UuidInterface $uuid_service) {
    parent::__construct($entity_info, $config_factory, $config_storage, $uuid_service);
    $this->paymentStatusManager = $payment_status_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_info) {
    return new static($entity_info,$container->get('config.factory'), $container->get('config.storage'), $container->get('plugin.manager.payment.status'), $container->get('uuid'));
  }

  /**
   * {@inheritdoc}
   */
  public function save(EntityInterface $entity) {
    parent::save($entity);
    if ($this->paymentStatusManager instanceof CachedDiscoveryInterface) {
      $this->paymentStatusManager->clearCachedDefinitions();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $entities) {
    parent::delete($entities);
    if ($this->paymentStatusManager instanceof CachedDiscoveryInterface) {
      $this->paymentStatusManager->clearCachedDefinitions();
    }
  }
}