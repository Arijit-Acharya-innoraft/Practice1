<?php

namespace Drupal\login_otp\Services;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;

/**
 *
 */
class OtpService {

  protected $database;

  /**
   *
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   *
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   *
   */
  public function fetchOtp($uid) {
    $query = $this->database->select('otp_table', 'ot')
      ->fields('ot', ['otp', 'created'])
      ->condition('uid', $uid);
    $result = $query->execute();
    return ($result->fetchAll());
  }

}
