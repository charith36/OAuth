
<?php

//config.php

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('191872964217-mqeb0dakacjbk01ta296q5h0f7mp8eno.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('VZi3TuqVm87ou_gVrD3YUa-k');

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri('http://localhost/SSDs/index.php');

//
$google_client->addScope('email');

$google_client->addScope('profile');
$google_client->addScope(Google_Service_Drive::DRIVE_READONLY);

// $google_client->setScopes(Google_Service_Drive::DRIVE);

//start session on web page
session_start();

?>
