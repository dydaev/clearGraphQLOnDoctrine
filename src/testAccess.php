<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 18.12.18
 * Time: 13:19
 */

require __DIR__ . '/../vendor/autoload.php';

use Utils\Utils;

$entitiResult = [
    A => [ D => 'D', E => [ F => 'F']],
    B => [ G => [ J => 'J'], K => []],
    V => ['X' => ['W' => ['A' => 'A', 'B' => 'B']], 'Y'],
    C => ['C', 'L'],
    'User' => [ 'phone' => '345', 'email' => 'aa@aa.net']
];

$accessArr = [
    1 => ['A/E', 2],
    4 => ['A/D', 2],
    3 => ['V/X/W', 2],
    2 => ['B/G/J', 0   ],
    5 => ['User/phone', 3],
    6 => ['User/email', 0]
];

$result = Utils::checkRights($entitiResult, $accessArr, 3);

echo '<pre>';
print_r($result);
echo '</pre>';