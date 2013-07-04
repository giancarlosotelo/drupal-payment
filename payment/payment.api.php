<?php

/**
 * @file
 * Hook documentation.
 */

use Drupal\payment\Plugin\Core\Entity\PaymentInterface;
use Drupal\payment\Plugin\Core\Entity\PaymentMethodInterface;
use Drupal\payment\Plugin\payment\status\PaymentStatusInterface;

/**
 * Alters payment status plugins.
 *
 * @param array $definitions
 *   Keys are plugin IDs. Values are plugin definitions.
 */
function hook_payment_status_alter(array &$definitions) {
  // Rename a plugin.
  $definitions['payment_failed']['label'] = 'Something went wrong!';
}

/**
 * Alters payment method plugins.
 *
 * @param array $definitions
 *   Keys are plugin IDs. Values are plugin definitions.
 */
function hook_payment_method_alter(array &$definitions) {
  // Remvove a payment method plugin.
  unset($definitions['foo_plugin_id']);

  // Replace a payment method plugin with another.
  $definitions['foo_plugin_id']['class'] = 'Drupal\foo\FooPaymentMethod';
}

/**
 * Alters line item plugins.
 *
 * @param array $definitions
 *   Keys are plugin IDs. Values are plugin definitions.
 */
function hook_payment_line_item_alter(array &$definitions) {
}

/**
 * Alters context plugins.
 *
 * @param array $definitions
 *   Keys are plugin IDs. Values are plugin definitions.
 */
function hook_payment_context_alter(array &$definitions) {
}

/**
 * Responds to a payment status being set.
 *
 * @see Payment::setStatus()
 *
 * @param \Drupal\payment\Plugin\Core\Entity\PaymentInterface $payment
 * @param \Drupal\payment\Plugin\payment\status\PaymentStatusInterface $previous_status
 *   The status the payment had before the new one was set. This may be
 *   identical to the current/new status.
 *
 * @return NULL
 */
function hook_payment_status_set(PaymentInterface $payment, PaymentStatusInterface $previous_status) {
  // Notify the site administrator, for instance.
}

/**
 * Executes before the payment context is resumed.
 *
 * @see \Drupal\payment\Plugin\payment\method\Base::resume()
 *
 * @param \Drupal\payment\Plugin\Core\Entity\PaymentInterface $payment
 */
function hook_payment_pre_resume_context(PaymentInterface $payment) {
  if ($payment->getStatus()->isOrHasAncestor('payment_success')) {
    drupal_set_message(t('Your payment was successfully completed.'));
  }
  else {
    drupal_set_message(t('Your payment was not completed.'));
  }
}

/**
 * Checks access for performing a payment method operation on a payment.
 *
 * @see \Drupal\payment\PaymentProcessingInterface::paymentOperationAccess()
 *
 * @param \Drupal\payment\Plugin\Core\Entity\PaymentInterface $payment
 *   $payment->getPaymentMethod() contains the method currently configured, but NOT the
 *   method that $payment should be tested against, which is $payment_method.
 * @param \Drupal\payment\Plugin\Core\entity\PaymentMethodInterface $payment_method
 * @param string $operation
 *
 * @return boolean
 *   Whether the operation can be performed on the payment.
 */
function hook_payment_operation_access(PaymentInterface $payment, PaymentMethodInterface $payment_method, $operation) {}

/**
 * Executes before a payment method operation is performed on a payment.
 *
 * @see \Drupal\payment\PaymentProcessingInterface::executePaymentOperation()
 *
 * @param \Drupal\payment\Plugin\Core\Entity\PaymentInterface $payment
 * @param string $operation
 */
function hook_payment_pre_operation(PaymentInterface $payment, $operation) {}
