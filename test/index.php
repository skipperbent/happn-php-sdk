<?php
require '../vendor/autoload.php';

$token = null;
$happn = new \Pecee\Http\Service\Happn($token);
var_dump($happn->getDistance('9233536265'));