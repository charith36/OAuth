<?php

use Google\Service\YouTube;

error_reporting(E_ERROR | E_PARSE);

require('../vendor/autoload.php');
$client = new Google_Client();
$client->setApplicationName('SSD Assignment');
$client->setScopes([
    'https://www.googleapis.com/auth/youtube.readonly',
]);
$client->setAuthConfig('web-client.json');
// offline access will give you both an access and refresh token so that
// your app can refresh the access token without user interaction.
$client->setAccessType('offline');
$authCode = $_GET['code'];

// Exchange authorization code for an access token.
$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
$client->setAccessToken($accessToken);

// Define service object for making API requests.
$service = new YouTube($client);

$queryParams = [
    'regionCode' => 'LK'
];

$response = $service->videoCategories->listVideoCategories('snippet', $queryParams);
foreach ($response['items'] as $items) {
    echo 'Id: ' . $items['id'] . ' - ' . $items['snippet']['title'];
    //print_r($items['snippet']);
    echo '<br><br><br>';
}
