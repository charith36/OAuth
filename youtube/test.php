<?php

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
$authUrl = $client->createAuthUrl();
echo '
<h6>Click here</h6>
<a href="' . $authUrl . '" target="_blank">Click this link</a>';
