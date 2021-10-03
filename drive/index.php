<?php

session_start();

if (!isset($_SESSION['drive-token']) || $_SESSION['drive-token'] == "") {
    require('../vendor/autoload.php');
    $client = new Google_Client();
    $client->setApplicationName('SSD Assignment');
    $client->setScopes([
        'https://www.googleapis.com/auth/drive.readonly',
    ]);
    $client->setAuthConfig('drive-client.json');
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
$client->setAuthConfig('drive-client.json');

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

function getDuration($milliseconds)
{
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;
    $format = '%u:%02u:%02u';
    $duration = sprintf($format, $hours, $minutes, $seconds);
    return rtrim($duration, '0');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drive2Tube</title>

    <!-- Load Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

    <!-- SweetALert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="http://localhost/ssd/">Drive2Tube</a>
            <div class="d-flex">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link mr-3" aria-current="page" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="p-3">
        <h5 class="display-5">Select a video file</h5>
        <hr>
        <p class="lead">Number of videos: <?php echo count($results->getFiles()); ?></p>
        <div class="row mt-3">
            <?php
            foreach ($results['files'] as $item) {
                echo '<div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 mb-3">';
                echo '<div class="card">';
                if ($item['hasThumbnail']) {
                    echo '<img src="' . $item['thumbnailLink'] . '" class="card-img-top" alt="Video thumbnail">';
                }
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $item['name'] . '</h5>';
                echo '<p class="card-text">';
                $bytes = $item['size'];
                if ($bytes >= 1073741824) {
                    $bytes = number_format($bytes / 1073741824, 2) . ' GB';
                } elseif ($bytes >= 1048576) {
                    $bytes = number_format($bytes / 1048576, 2) . ' MB';
                } elseif ($bytes >= 1024) {
                    $bytes = number_format($bytes / 1024, 2) . ' KB';
                } elseif ($bytes > 1) {
                    $bytes = $bytes . ' bytes';
                } elseif ($bytes == 1) {
                    $bytes = $bytes . ' byte';
                } else {
                    $bytes = '0 bytes';
                }
                echo '<b>Size:</b> ' . $bytes . '<br>';
                $date = new DateTime(date($item['createdTime']));
                echo '<b>Created:</b> ' . $date->format('F jS, Y g:i A') . '<br>';
                $millis = $item['videoMediaMetadata']['durationMillis'];
                $duration = getDuration($millis);
                echo '<b>Duration:</b> ' . $duration . '<br>';
                echo '<b>Resolution:</b> ' . $item['videoMediaMetadata']['width'] . 'x' . $item['videoMediaMetadata']['height'] . '<br>';
                echo '</p>';
                echo '</div>';
                echo '<div class="card-footer">';
                echo '<a class="btn btn-primary w-100" href="' . $item['webViewLink'] . '" target="_blank">Watch Video</a>';
                echo '<button class="btn btn-danger mt-3 w-100 youtube-button" data-id="' . $item['id'] . '">Add to YouTube</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".youtube-button").click(function() {
                var id = $(this).attr('data-id');

                $.post("cookie.php", {
                    id: id,
                    submit: 'file-selected'
                }, function(data, status) {
                    if (status == "success") {
                        if (data == "success") {
                            window.location.href = "../youtube/";
                        } else {
                            Swal.fire(
                                'Error!',
                                data,
                                'error'
                            );
                        }
                    } else {
                        Swal.fire(
                            'Error!',
                            'System error!',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
</body>

</html>