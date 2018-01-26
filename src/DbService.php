<?php
/**
 * @file
 * contains \Drupal\vape\DbService
 */

namespace Drupal\vape;

use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxyInterface;

class DbService {
  
  /**
   * DB connection
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;
  
  /**
   * @var string
   */
  protected $tableName = 'vape';
  
  /**
   * DbService constructor.
   *
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(Connection $connection){
    $this->database = $connection;
  }
  
  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $user
   * @param string                                     $token
   */
  public function setAccessToken(AccountProxyInterface $user, string $token){
    if (!$this->isRevoked($token)) {
      $this->database
        ->insert($this->tableName)
        ->fields(
          [
            'uid'         => $user->id(),
            'accessToken' => $token,
            'revoked'     => 0,
            'created'     => time(),
          ]
        )->execute();
    }
  }
  
  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $user
   *
   * @return bool
   */
  public function getAccessToken(AccountProxyInterface $user){
    $result = $this->database
      ->select($this->tableName, 'v')
      ->fields('v', ['accessToken'])
      ->condition('uid', $user->id())
      ->condition('revoked', 0)
      ->execute();
    
    return !empty($result->fetchAll());
  }
  
  /**
   * @param string $token
   *
   * @return bool
   */
  public function isRevoked(string $token){
    $result = $this->database
      ->select($this->tableName, 'v')
      ->fields('v', ['revoked'])
      ->condition('accessToken', $token)
      ->execute();
    
    return !empty($result->fetchAll());
  }
  
  /**
   *  deletes all revoked items
   */
  public function delRevoked(){
    $this->database
      ->delete($this->tableName)
      ->condition('revoked', 1)
      ->execute();
  }
}