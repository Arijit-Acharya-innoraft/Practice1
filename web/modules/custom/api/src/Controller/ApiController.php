<?php

namespace Drupal\api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * This class implements the api exposure.
 */
class ApiController extends ControllerBase {

  /**
   * The EntityTypeManger service.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityManager;

  /**
   * Constructs a new EntityTypeManager .
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_manager
   *   An object that implements \EntityTypeManager.
   */
  public function __construct(EntityTypeManager $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * This function exposes api response.
   */
  public function build() {
    $users_storage = $this->entityManager->getStorage('user');
    $query = $users_storage->getQuery();
    $uids = $query
      ->accessCheck(FALSE)
      ->condition('status', '1')
      ->condition('roles', 'students')
      ->execute();
    $users = $users_storage->loadMultiple($uids);
    $current_date = date("Y-m-d");
    foreach ($users as $user) {
      if ((strtotime($user->field_passing_year->value)) - (strtotime($current_date)) >= 0) {
        $build[] = [
          'uid' => $user->uid->value,
          $data[] = [
            'name' => $user->name->value,
            'mail' => $user->mail->value,
            'stream' => $user->field_stream->value,
            'mobile' => $user->field_mobile_no->value,
            'joining_year' => $user->field_joining_year->value,
            'passing_year' => $user->field_passing_year->value,
          ],
        ];
      }

    }
    return new JsonResponse($build);
  }

  /**
   * It builds json response based on parameters.
   *
   * @param string $parameter
   *   Stores the parameter from the url.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Returns the json response.
   */
  public function buildOnParameter($parameter) {
    // Storing the value in the 0th index of the array.
    $store[0] = $parameter;
    // Checking if the parameter entered is stream or joining date.
    // Storing the appropriate field value in the 1st index of the array.
    if (count(explode("-", $parameter)) > 1) {
      $store[1] = 'field_joining_year';
    }
    else {
      $store[1] = 'field_stream';
    }

    $users_storage = $this->entityManager->getStorage('user');
    $query = $users_storage->getQuery();
    $uids = $query
      ->accessCheck(FALSE)
      ->condition('status', '1')
      ->condition('roles', 'students')
      ->condition($store[1], $store[0])
      ->execute();
    $users = $users_storage->loadMultiple($uids);
    $current_date = date("Y-m-d");
    foreach ($users as $user) {
      if ((strtotime($user->field_passing_year->value)) - (strtotime($current_date)) >= 0) {
        $build[] = [
          'uid' => $user->uid->value,
          $data[] = [
            'name' => $user->name->value,
            'mail' => $user->mail->value,
            'stream' => $user->field_stream->value,
            'mobile' => $user->field_mobile_no->value,
            'joining_year' => $user->field_joining_year->value,
            'passing_year' => $user->field_passing_year->value,
          ],
        ];
      }
    }
    return new JsonResponse($build);
  }

  /**
   * It builds the json response based on two parameters.
   *
   * @param mixed $parameter1
   *   Stores the value of the first parameter of the url.
   * @param mixed $parameter2
   *   Stores the value of the second parameter of the url.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Returns the json response.
   */
  public function buildOnDoubleParameter($parameter1, $parameter2) {

    $joining_date_parameter = [];
    $stream_parameter = [];

    // Checking which parameter is stream and which is joining date.
    // 0th index stores the value in both the array.
    // 1th index stores the field name in both the array.
    if (count(explode("-", $parameter1)) > 1) {
      $joining_date_parameter[0] = $parameter1;
      $joining_date_parameter[1] = "field_joining_year";
      $stream_parameter[0] = $parameter2;
      $stream_parameter[1] = "field_stream";
    }
    else {
      $joining_date_parameter[0] = $parameter2;
      $joining_date_parameter[1] = "field_joining_year";
      $stream_parameter[0] = $parameter1;
      $stream_parameter[1] = "field_stream";
    }

    $users_storage = $this->entityManager->getStorage('user');
    $query = $users_storage->getQuery();
    $uids = $query
      ->accessCheck(FALSE)
      ->condition('status', '1')
      ->condition('roles', 'students')
      ->condition($joining_date_parameter[1], $joining_date_parameter[0])
      ->condition($stream_parameter[1], $stream_parameter[0])
      ->execute();
    $users = $users_storage->loadMultiple($uids);
    $current_date = date("Y-m-d");
    $build = [];
    foreach ($users as $user) {
      if ((strtotime($user->field_passing_year->value)) - (strtotime($current_date)) >= 0) {
        $build[] = [
          'uid' => $user->uid->value,
          $data[] = [
            'name' => $user->name->value,
            'mail' => $user->mail->value,
            'stream' => $user->field_stream->value,
            'mobile' => $user->field_mobile_no->value,
            'joining_year' => $user->field_joining_year->value,
            'passing_year' => $user->field_passing_year->value,
          ],
        ];
      }
    }
    return new JsonResponse($build);
  }

}
