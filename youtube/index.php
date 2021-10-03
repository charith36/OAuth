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

if (!isset($_SESSION['drive-token'])) {
    header('location: http://localhost/ssd/drive/');
}

$accessToken = $_SESSION['drive-token'];

use Google\Service\Drive;

error_reporting(E_ERROR | E_PARSE);

require('../vendor/autoload.php');
$client = new Google_Client();
$client->setApplicationName('SSD Assignment');
$client->setScopes([
    'https://www.googleapis.com/auth/drive.readonly',
]);
$client->setAuthConfig('drive-client.json');

$drive = new Drive($client);


$client->setAccessType('offline');

// Exchange authorization code for an access token.
$client->setAccessToken($accessToken);

$id = $_SESSION["id"];

$optParams = array(
    'fields' => 'id,name,hasThumbnail,thumbnailLink,createdTime,size,videoMediaMetadata',
);

$results = $drive->files->get($id, $optParams);


?>