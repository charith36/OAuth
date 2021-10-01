<?php

use Google\Service\YouTube;
use Google\Service\YouTube\Video;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\VideoStatus;

error_reporting(E_ERROR | E_PARSE);

require('../vendor/autoload.php');
$client = new Google_Client();
$client->setApplicationName('SSD Assignment');
$client->setScopes([
    //'https://www.googleapis.com/auth/youtube.readonly',
    'https://www.googleapis.com/auth/youtube.upload',
]);
$client->setAuthConfig('web-client.json');

$youtube = new YouTube($client);

// offline access will give you both an access and refresh token so that
// your app can refresh the access token without user interaction.
$client->setAccessType('offline');
$authCode = $_GET['code'];

// Exchange authorization code for an access token.
$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
$client->setAccessToken($accessToken);

/*$response = $service->videoCategories->listVideoCategories('snippet', $queryParams);
foreach ($response['items'] as $items) {
    echo 'Id: ' . $items['id'] . ' - ' . $items['snippet']['title'];
    //print_r($items['snippet']);
    echo '<br><br><br>';
}*/

try {
    $videoPath = "Test Video.mp4";
    $title = "Test title 1";
    $description = "Test description 1...";
    $tags = array('tag1', 'tag2');
    $categoryId = "22";
    $privacyStatus = "private";

    $snippet = new VideoSnippet();
    $snippet->setTitle($title);
    $snippet->setDescription($description);
    $snippet->setTags($tags);
    $snippet->setCategoryId($categoryId);

    $status = new VideoStatus();
    $status->privacyStatus = $privacyStatus;

    $video = new Video();
    $video->setSnippet($snippet);
    $video->setStatus($status);

    $chunkSizeBytes = 1 * 1024 * 1024;

    // Setting the defer flag to true tells the client to return a request which can be called
    // with ->execute(); instead of making the API call immediately.
    $client->setDefer(true);

    // Create a request for the API's videos.insert method to create and upload the video.
    $insertRequest = $youtube->videos->insert("status,snippet", $video);

    // Create a MediaFileUpload object for resumable uploads.
    $media = new Google_Http_MediaFileUpload(
        $client,
        $insertRequest,
        'video/*',
        null,
        true,
        $chunkSizeBytes
    );
    $media->setFileSize(filesize($videoPath));

    // Read the media file and upload it chunk by chunk.
    $status = false;
    $handle = fopen($videoPath, "rb");
    while (!$status && !feof($handle)) {
        $chunk = fread($handle, $chunkSizeBytes);
        $status = $media->nextChunk($chunk);
    }
    fclose($handle);

    // If you want to make other calls after the file upload, set setDefer back to false
    $client->setDefer(false);

    echo 'Video uploaded';
} catch (Google_Service_Exception $e) {
    echo "Error type 1: " . $e;
} catch (Google_Exception $e) {
    echo "Error type 2: " . $e;
}
