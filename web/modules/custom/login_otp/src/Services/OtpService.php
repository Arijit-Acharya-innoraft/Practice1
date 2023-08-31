<?php

namespace Drupal\login_otp\Services;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;

/**
 * A service class to check if the particular user has logged in before.
 */
class OtpService {

  /**
   * The database instance.
   *
   * @var Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection service.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * A method to fetch the data of a userin the otp table.
   *
   * @param int $uid
   *   The user ID for which OTP data should be fetched.
   *
   * @return array
   *   An array containing the fetched OTP data.
   */
  public function fetchOtp($uid) {
    $query = $this->database->select('otp_table', 'ot')
      ->fields('ot', ['otp', 'created'])
      ->condition('uid', $uid);
    $result = $query->execute();
    return ($result->fetchAll());
  }

}
