<?php

session_start();
include './config.php';
include './src/Facebook.php';
$fb = new Facebook($config);
$code = filter_input(INPUT_GET, 'code', FILTER_DEFAULT);
$token = $fb->get_access_token($code);
if (isset($token['access_token']))
{
    $_SESSION['access_token'] = $token['access_token'];
    header('Location: user.php');
}
