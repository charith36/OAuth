<?php

if (isset($_GET['code'])) {
$authCode = $_GET['code'];

 require('../vendor/autoload.php');
$client = new Google_Client();
$client->setApplicationName('SSD Assignment');
$client->setScopes([
'https://www.googleapis.com/auth/drive.readonly',
]);
$client->setAuthConfig('web-client.json');
$client->setAccessType('offline');
$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

 session_start();
$_SESSION['drive-token'] = $accessToken;
header('location: http://localhost/ssd/drive/');
}

session_start();

if (!isset($_SESSION['drive-token']) || $_SESSION['drive-token'] == "") {
require('../vendor/autoload.php');
$client = new Google_Client();
$client->setApplicationName('SSD Assignment');
$client->setScopes([
'https://www.googleapis.com/auth/drive.readonly',
]);
$client->setAuthConfig('web-client.json');
$client->setPrompt('select_account consent');
$authUrl = $client->createAuthUrl();
header('location: ' . $authUrl);
exit;
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
$client->setAuthConfig('web-client.json');

$drive = new Drive($client);

// offline access will give you both an access and refresh token so that
// your app can refresh the access token without user interaction.
$client->setAccessType('offline');

// Exchange authorization code for an access token.
$client->setAccessToken($accessToken);

$optParams = array(
'pageSize' => 100,
'fields' => 'nextPageToken, files(id, name, description, properties, spaces, webContentLink, webViewLink, iconLink, hasThumbnail, thumbnailVersion, thumbnailLink, createdTime, modifiedTime, ownedByMe, capabilities, permissions, permissionIds, originalFilename, fullFileExtension, fileExtension, size, contentHints, videoMediaMetadata, exportLinks)',
'q' => "mimeType='video/mp4'"
);

$results = $drive->files->listFiles($optParams);
print_r(results);