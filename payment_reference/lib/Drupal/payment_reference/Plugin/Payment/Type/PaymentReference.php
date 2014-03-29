<?php

/**
 * Contains \Drupal\payment_reference\Plugin\Payment\Type\PaymentReference.
 */

namespace Drupal\payment_reference\Plugin\Payment\Type;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\HttpKernel;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\payment\Plugin\Payment\Type\PaymentTypeBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The payment reference field payment type.
 *
 * @PaymentType(
 *   configuration_form = "\Drupal\payment_reference\Plugin\Payment\Type\PaymentReferenceConfigurationForm",
 *   id = "payment_reference",
 *   label = @Translation("Payment reference field")
 * )
 */
class PaymentReference extends PaymentTypeBase implements ContainerFactoryPluginInterface {

  /**
   * The field instance config storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $fieldInstanceConfigStorage;

  /**
   * The HTTP kernel.
   *
   * @var \Drupal\Core\HttpKernel
   */
  protected $httpKernel;

  /**
   * The request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * A URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\HttpKernel $http_kernel
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   * @param \Drupal\Core\Entity\EntityStorageInterface $field_instance_config_storage
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, HttpKernel $http_kernel, Request $request, ModuleHandlerInterface $module_handler , UrlGeneratorInterface $url_generator, EntityStorageInterface $field_instance_config_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $module_handler);
    $this->httpKernel = $http_kernel;
    $this->request = $request;
    $this->urlGenerator = $url_generator;
    $this->fieldInstanceConfigStorage = $field_instance_config_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_kernel'),
      $container->get('request'),
      $container->get('module_handler'),
      $container->get('url_generator'),
      $container->get('entity.manager')->getStorage('field_instance_config')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function resumeContext() {
    parent::resumeContext();

    $response = new RedirectResponse($this->urlGenerator->generateFromRoute('payment_reference.resume_context', array(
      'payment' => $this->getPayment()->id(),
    ), array(
      'absolute' => TRUE,
    )));
    $response->prepare($this->request)
      ->send();
    $this->httpKernel->terminate($this->request, $response);
    exit;
  }

  /**
   * {@inheritdoc}
   */
  public function paymentDescription($language_code = NULL) {
    $instance = $this->fieldInstanceConfigStorage->load($this->getFieldInstanceConfigId());

    return $instance->label($language_code);
  }

  /**
   * Sets the ID of the field instance config the payment was made for.
   *
   * @param string $field_instance_config_id
   *
   * @return static
   */
  public function setFieldInstanceConfigId($field_instance_config_id) {
    $this->getPayment()->set('payment_reference_field_instance', $field_instance_config_id);

    return $this;
  }

  /**
   * Gets the ID of the field instance config the payment was made for.
   *
   * @return string
   */
  public function getFieldInstanceConfigId() {
    $values =  $this->getPayment()->get('payment_reference_field_instance');

    return isset($values[0]) ? $values[0]->get('target_id')->getValue() : NULL;
  }
}