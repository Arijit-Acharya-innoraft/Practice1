<?php

namespace Drupal\login_otp\Form;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\login_otp\Services\OtpService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class OtpValidation extends FormBase {

  /**
   * @var [type]
   */
  protected $routeMatch;
  /**
   * @var [type]
   */
  protected $otpService;
  /**
   * @var [type]
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(RouteMatchInterface $routeMatch, OtpService $otp_service, EntityTypeManager $entity_type_manager) {
    $this->routeMatch = $routeMatch;
    $this->otpService = $otp_service;
    $this->entityManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('login_otp.my_service'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'otp_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['otp'] = [
      '#type' => 'number',
      '#title' => 'Otp',
      '#description' => $this->t('Enter the OTP sent via email'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Otp'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $uid = $this->routeMatch->getParameter('uid');
    $status = $this->checkOtp($form_state->getValue('otp'), $uid);
    if ($status != "Otp Matched!") {
      $form_state->setErrorByName('otp', $status);
    }
    else {
      $user = $this->entityManager->getStorage('user')->load($uid);
      user_login_finalize($user);
      $form_state->setRedirect('user.page');
      \Drupal::messenger()->addMessage($status);
    }
  }

  /**
   *
   */
  public function checkOtp($otp, $uid) {

    $stores = $this->otpService->fetchOtp($uid);
    $stored_otp = $stores[0]->otp;
    $creation_time = $stores[0]->created;
    $current_time = time();
    if ($current_time - $creation_time > 120) {
      return "OTP Time Out! Try Again.";
    }
    else {
      if ($otp != $stored_otp) {
        return "Otp mismatch! Try again.";
      }

      return "Otp Matched!";
    }
  }

}
