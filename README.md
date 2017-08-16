# Facebook-Library
===============

Simplified version of Facebook sdk

Facebook-Library
Version: 1.0
Author: [Abdul Baquee](http://www.webgrapple.com/)  
Twitter: [@abdulbaquee85](http://www.twitter.com/abdulbaquee85)

Usage
===============
This application requires rest api v2.10
```
config.php

$config['app_id'] = 'your app id';
$config['app_secret'] = 'your app secret';
$config['call_back_url'] = 'your callback url';
$config['default_graph_version'] = 'v2.10';
$config['scope'] = 'comma seperated extended permission';
```

```
callback.php

session_start();
include './config.php';
include './src/Facebook.php';
$fb = new Facebook($config);
$code = filter_input(INPUT_GET, 'code', FILTER_DEFAULT);
if (empty($code))
{
    $_SESSION['access_token'] = $token['access_token'];
    header('Location: index.php');
}
$token = $fb->get_access_token($code);
if (isset($token['access_token']))
{
    $_SESSION['access_token'] = $token['access_token'];
    header('Location: index.php');
}
```

```
user.php

$token = $_SESSION['access_token'];
$request_data = array(
    'access_token' => $token,
    'fields' => array('id', 'name', 'email')
);
$user = $fb->get('me', $request_data);
echo "<pre>";
print_r($user);
```

Updates
===============

