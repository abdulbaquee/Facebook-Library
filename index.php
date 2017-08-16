<?php
include './config.php';
include './src/Facebook.php';
$fb = new Facebook($config);
?>
<a href="<?php echo $fb->login(); ?>">Login with Facebook</a>
