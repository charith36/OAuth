<?php

use Google\Service\YouTube;
use Google\Service\YouTube\Video;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\VideoStatus;

session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['youtube-token'])) {
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
}

$id = $_SESSION['id'];
$accessToken_youtube = $_SESSION['youtube-token'];

if (isset($_POST['submit'])) {

    // Download
    if ($_POST['submit'] == "load") {
        $url = "https://drive.google.com/uc?id=" . $id . "&export=download";
        $dir = '../temp/';
        $location = $dir . $id . '.mp4';
        file_put_contents($location, fopen($url, 'r'));
        echo 'success';
        exit;
    }

    // Upload
    if ($_POST['submit'] == "upload") {
        $dir = '../temp/';
        $videoPath = $dir . $id . '.mp4';

        // Get POST data
        $title = $_POST['title'];
        $description = $_POST['description'];
        $privacyStatus = $_POST['visibility'];

        error_reporting(E_ERROR | E_PARSE);

        require('../vendor/autoload.php');
        $client = new Google_Client();
        $client->setApplicationName('SSD Assignment');
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.upload',
        ]);
        $client->setAuthConfig('web-client.json');
        $youtube = new YouTube($client);
        $client->setAccessType('offline');
        $client->setAccessToken($accessToken_youtube);

        try {
            $categoryId = "22";

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

            $client->setDefer(true);

            $insertRequest = $youtube->videos->insert("status,snippet", $video);

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
            $client->setDefer(false);

            // Delete the file
            unlink($videoPath);

            echo 'success';
        } catch (Google_Service_Exception $e) {
            echo "Error type 1: " . $e;
        } catch (Google_Exception $e) {
            echo "Error type 2: " . $e;
        }
    }
}
