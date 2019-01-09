<?php

namespace Utils;

use Doctrine\ORM\EntityManager;
use entities\Role;
use entities\Rule;
use entities\User;

date_default_timezone_set('UTC');

class Utils
{
  /**
   * @Object : example entiti class
   * @Array : array of incledes names eqiule settert or getters
   * $role : array access keys for entities and his fields
   */
  static function parseArrayKeysForRole($Obj, $arraySetters, $role = []) {
    $className = get_class($Obj);
    return array_filter($arraySetters,function($value, $key){
        return true;
    }, ARRAY_FILTER_USE_BOTH);
  }

  /**
   * @Object : object class of entities
   * @Array : array of key includes names equie setters Object 
   * @return : Object
   * (example Object->setName(value) === Array [ 'name' => value])
   */
  static function callEntitiObjectSettersFromArray($Obj, $arr) {
    Utils::parseArrayKeysForRole($Obj, $arr);
    foreach ($arr as $key => $value) {
      
      $supposeMethod = 'set'.ucfirst($key);
      if(method_exists($Obj, $supposeMethod)) {
        call_user_func(array($Obj, $supposeMethod, $value));
      }
    }
    return $Obj;
  }

    /**
     * @param $incomingObject  array ...[ 'User' => [ 'phone' => '345', 'email' => 'aa@aa.net']]
     * @param $accessRights  array with paths and rights ...[ ['User/phone', 3], ['User/email', 0] ]
     * @param $mask  number rights ... 3
     * @param  $deep boolean, default clearing only self and daughters object, if true - clean up at self level
     * @return array  clear incoming object ...[ 'User' => [ 'phone' => '345']]
     */
  static function checkRights($incomingObject, $accessRights, $mask, $deep = false) {
      function recurse($entitiRes, $access, $acceptMask, $deep) {

          $entitiPath = explode('/', $access[0]);
          $entitiCurrentFolder = array_shift($entitiPath);
          $privilege = $access[1];

          if ($entitiCurrentFolder && is_array($entitiRes)) {

              $res = [];
              foreach ($entitiRes as $key =>$subEntitiRes) {

                  if ( $entitiCurrentFolder ===  $key || $entitiCurrentFolder === '*') {
                      $newAccess = [ implode('/',$entitiPath)  , $privilege];

                      if ($deep && count($entitiPath) == 1 && (in_array($entitiPath[count($entitiPath) -1], $entitiRes ))){
                          $returnedData = $entitiRes;
                          $res = $returnedData ;
                      } else {

                          $returnedData = recurse($subEntitiRes, $newAccess, $acceptMask, $deep);
                          if ($returnedData) $res[$key] = $returnedData ;
                      }


                  } else {
                      //разкоментировать если политика "разрешено все что не запрещено"
                      //if (!array_key_exists($key, $res)) $res[$key] = $subEntitiRes;
                  }
              }

              return (is_array($res) && count($res) > 0) ? $res : null;

          } else if (count($entitiPath) > 0 && !is_array($entitiRes)) {
              //правило есть а данных нет
              return null;

          } else if (count($entitiPath) === 0) {
              //точка назначения пути, проверка билетов)
              return (($entitiCurrentFolder == '*' || $entitiCurrentFolder == $entitiRes)
                  && (($privilege & $acceptMask) === $privilege)) ? $entitiRes : null;

          } else {
              //нет такого пути
              return null;
          }
      }

      $result = [];
      foreach ( $accessRights as $key => $access) {
          $preRes = recurse($incomingObject, $access, $mask, $deep);
          $result = array_merge_recursive($result, is_array($preRes) ? $preRes : []);
      }
      return $result;
  }

    /**
     * @param EntityManager $EM
     * @param string $login
     *
     * @return array
     */
  public static function getUserAccessList(EntityManager $EM, $login)
  {
      $user = $EM->getRepository('entities\User')->findOneBy([ 'login' => $login ]);

      if (!empty($user) && $user instanceof User) {
          $roles = $user->getRoles();

          $accessList = [];

          $roles->map(function (Role $role) use (&$accessList){
             $rules = $role->getRules();

             $rules->map(function (Rule $rule) use (&$accessList) {

                 $ruleForChecker = [$rule->getRulePath(), $rule->getPermission()];

                 array_push($accessList,$ruleForChecker );

             });
          });

          return $accessList;
      }

      return [];

  }

  public static function checkToken($token) {

      if (!empty($token) && $token !== null && $token !== 'NULL') {
          if (isset($_SESSION['life'])) {
              if (!empty($_SESSION['life']['die_time']) && $_SESSION['life']['die_time'] > date('U')) {
                  if ($_SESSION['life']['key_of_life'] === $token) return true;
              }
              unset($_SESSION['life']);
          }
      }

      return false;
  }

  public static function getMySelf($token) {

      if (self::checkToken($token) ) return 'roma';//&& !empty($_SESSION['life']['user_login'])) return $_SESSION['life']['user_login'];

      return null;
  }

  public static function getToken() {
    $hours = 0;
    $minutes = 5;
    $seconds = 0;

    $nowPlusLifeTime = mktime(
        date("H") + $hours,
        date("i") + $minutes,
        date("s") + $seconds,
        date("m"),
        date("d"),
        date("Y")
    );

    $token = bin2hex(random_bytes(512));

    return [
        'token' => $token,
        'die_time' => $nowPlusLifeTime
    ];
  }
}