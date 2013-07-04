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
 * Executes when a payment status is being set.
 *
 * @see Payment::setStatus()
 *
 * @param \Drupal\payment\Plugin\Core\Entity\PaymentInterface $payment
 * @param \Drupal\payment\Plugin\payment\status\PaymentStatusInterface $previous_status_item
 *   The status the payment had before it was set.
 *
 * @return NULL
 */
function hook_payment_status_change(PaymentInterface $payment, PaymentStatusInterface $previous_status_item) {
  // Notify the site administrator, for instance.
}

/**
 * Executes right before a payment is executed. This is the place to
 * programmatically alter payments.
 *
 * @see Payment::execute()
 *
 * @param \Drupal\payment\Plugin\Core\Entity\PaymentInterface $payment
 *
 * @return NULL
 */
function hook_payment_pre_execute(PaymentInterface $payment) {
  // Add a payment method processing fee.
  $payment->setLineItem(\Drupal::service('plugin.manager.payment.line_item')->createInstance('payment_basic', array(
    'name' => 'foo_fee',
    'amount' => 5.50,
  )));
}

/**
 * Executes right before the payment context is resumed.
 *
 * @see \Drupal\payment\Plugin\payment\method\Base::resume()
 *
 * @param \Drupal\payment\Plugin\Core\Entity\PaymentInterface $payment
 */
function hook_payment_pre_resume(PaymentInterface $payment) {
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
 * Alter the payment form.
 *
 * Because the payment form is not always used through drupal_get_form(), you
 * should use this hook, rather than hook_form_alter() or
 * hook_form_FORM_ID_alter() to make changes to the payment form.
 *
 * @param array $elements
 *   The array of form elements that are part of the payment form. Note that
 *   the top-level array is NOT a form.
 * @param array $form_state
 * @param array $submit
 *   An array with the names of form submit callbacks that should be called upon form submission.
 *
 * @return NULL
 */
function hook_payment_form_alter(array &$elements, array &$form_state, array &$submit) {}