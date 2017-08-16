# Facebook-Library
===============

If you are using Facebook for marketing and engaging your prospective and current clients it makes sense to introduce your FB fans to what may be happening on our Twitter profile. It allows our FB fans to see what other different types of conversations your business/brand is having on Twitter without them having to visit Twitter itself. They can retweet, follow you, and even favourite your tweets, right from the comfort of Facebook page itself.

Facebook-Library
Version: 1.0
Author: [Abdul Baquee](http://www.webgrapple.com/)  
Twitter: [@abdulbaquee85](http://www.twitter.com/abdulbaquee85)

Usage
===============
This application requires rest api v2.10

```<?php
$config['app_id'] = 'your app id';
$config['app_secret'] = 'your app secret';
$config['call_back_url'] = 'your callback url';
$config['default_graph_version'] = 'v2.10';
$config['scope'] = 'comma seperated extended permission';
$fb = new Facebook($config);
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
