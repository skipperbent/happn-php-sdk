<?php
require '../vendor/autoload.php';

$happn = new \Pecee\Http\Service\Happn();

var_dump($happn->getUserId());