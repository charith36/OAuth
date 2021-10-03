<?php

if (isset($_GET['code'])) {
    $authCode = $_GET['code'];

    require('../vendor/autoload.php');
    $client = new Google_Client();
    $client->setApplicationName('SSD Assignment');
    $client->setScopes([
        'https://www.googleapis.com/auth/youtube.upload',
    ]);
  
}
