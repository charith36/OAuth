<?php

if (isset($_GET['code'])) {
    $authCode = $_GET['code'];
    
    //Google client
    require('../vendor/autoload.php');
    $client = new Google_Client();
    $client->setApplicationName('SSD Assignment');
    $client->setScopes([
        'https://www.googleapis.com/auth/drive.readonly',
    ]);
    $client->setAuthConfig('drive-client.json');
    $client->setAccessType('offline');
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    
    //start session
    session_start();
    $_SESSION['drive-token'] = $accessToken;
    header('location: http://localhost/ssd/drive/');
}
