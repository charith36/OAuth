<?php

session_start();
if (!isset($_SESSION['id'])) {
    echo 'Error';
    exit;
}

if (!isset($_SESSION['youtube-token']) || $_SESSION['youtube-token'] == "") {
    require('../vendor/autoload.php');
    $client = new Google_Client();
    $client->setApplicationName('SSD Assignment');
    $client->setScopes([
        'https://www.googleapis.com/auth/youtube.upload',
    ]);
    $client->setAuthConfig('web-client.json');
    $client->setAccessType('offline');
    $authUrl = $client->createAuthUrl();
    header('location: ' . $authUrl);
    exit;
}


?>