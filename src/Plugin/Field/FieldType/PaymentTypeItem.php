<?php

/**
 * @file
 * Contains \Drupal\payment\Plugin\Field\FieldType\PaymentTypeItem.
 */

namespace Drupal\payment\Plugin\Field\FieldType;

use Drupal\payment\Payment;

/**
 * Provides a plugin bag for payment type plugins.
 *
 * @FieldType(
 *   id = "payment_type",
 *   label = @Translation("Payment type plugins")
 * )
 */
class PaymentTypeItem extends PaymentAwarePluginBagItemBase {

  /**
   * {@inheritdoc}
   */
  public function getPluginManager() {
    // @todo Unit-test this.
    return Payment::typeManager();
  }

}