<?php

session_start();
include './config.php';
include './src/Facebook.php';
$fb = new Facebook($config);
if (!isset($_SESSION['access_token']))
{
    header('Location: index.php');
}
$token = $_SESSION['access_token'];
$request_data = array(
    'access_token' => $token,
    'fields' => array('id', 'name')
);
echo "<pre>";
print_r($request_data);