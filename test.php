<?php
session_start();
$arr = [];
$_SESSION['first'] = 'hello';
$_SESSION['second'] = 'second';
$arr['first'] = $_SESSION['first'];
$arr['second'] = $_SESSION['second'];
// print_r($arr);
print_r($_SESSION);