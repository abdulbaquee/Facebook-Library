<?php
include './config.php';
include './src/Facebook.php';
$fb = new Facebook($config);
$token = $fb->get_app_access_token();
?>
<a href="<?php echo $fb->login(); ?>">Login with Facebook</a>
