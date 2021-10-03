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

// offline access will give you both an access and refresh token so that
// your app can refresh the access token without user interaction.
$client->setAccessType('offline');

// Exchange authorization code for an access token.
$client->setAccessToken($accessToken);

$id = $_SESSION["id"];

$optParams = array(
    'fields' => 'id,name,hasThumbnail,thumbnailLink,createdTime,size,videoMediaMetadata',
);

$results = $drive->files->get($id, $optParams);

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
    <title>Upload Your Video</title>

    <!-- Load Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

    <!-- SweetALert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="">
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
        <h5 class="display-5">Upload Your Video to YouTube</h5>
        <hr>
        <div class="row pt-3">
            <div class="col-sm-12 col-md-5 col-lg-3">
                <?php
                echo '<div class="card">';
                if ($results['hasThumbnail']) {
                    echo '<img src="' . $results['thumbnailLink'] . '" class="card-img-top" alt="Video thumbnail">';
                }
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $results['name'] . '</h5>';
                echo '<p class="card-text">';
                $bytes = $results['size'];
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
                $date = new DateTime(date($results['createdTime']));
                echo '<b>Created:</b> ' . $date->format('F jS, Y g:i A') . '<br>';
                $millis = $results['videoMediaMetadata']['durationMillis'];
                $duration = getDuration($millis);
                echo '<b>Duration:</b> ' . $duration . '<br>';
                echo '<b>Resolution:</b> ' . $results['videoMediaMetadata']['width'] . 'x' . $results['videoMediaMetadata']['height'] . '<br>';
                echo '</p>';
                echo '</div>';
                echo '</div>';
                ?>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-7 mx-5">
                <div class="mb-3">
                    <label for="video-title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="video-title" placeholder="Enter video title here..." value="<?php echo $results['name']; ?>">
                </div>
                <div class="mb-3">
                    <label for="video-description" class="form-label">Description</label>
                    <textarea id="video-description" rows="10" class="form-control" placeholder="Enter video description here..."></textarea>
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Visibility</label>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" id="video-visibility-public" name="video-visibility" value="public">
                        <label for="video-visibility-public" class="form-check-label">Public</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" id="video-visibility-private" name="video-visibility" value="private">
                        <label for="video-visibility-private" class="form-check-label">Private</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" id="video-visibility-unlisted" name="video-visibility" value="unlisted">
                        <label for="video-visibility-public" class="form-check-label">Unlisted</label>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-sm-12 col-md-6">
                        <a class="btn btn-secondary w-100" href="http://localhost/ssd/">Cancel</a>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <button class="btn btn-primary w-100" id="submit-button">Upload</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="progress-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Uploading...</h5>
                </div>
                <div class="modal-body">
                    <p>Please wait</p>
                    <p id="text-1"></p>
                    <p id="text-2"></p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%" id="prog-bar"></div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#submit-button").click(function() {
                var title = $("#video-title").val();
                var description = $("#video-description").val();
                var visibility = $('input[name="video-visibility"]:checked').val();

                $("#progress-modal").modal('show');

                $.post("upload-script.php", {
                    submit: 'load'
                }, function(data, status) {
                    if (status == "success") {
                        if (data == "success") {
                            $("#prog-bar").addClass('bg-success');
                            $("#text-1").text('Video loaded successfully!');
                            $.post("upload-script.php", {
                                title: title,
                                description: description,
                                visibility: visibility,
                                submit: 'upload'
                            }, function(data, status) {
                                if (status == "success") {
                                    if (data == "success") {
                                        Swal.fire(
                                            'Success!',
                                            'Check your YouTube channel now!',
                                            'success'
                                        ).then(function() {
                                            window.location.href = "http://localhost/ssd/";
                                        });
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
                                        'Cannot login!\nPlease try again shortly!',
                                        'error'
                                    );
                                }
                            });
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
                            'Cannot login!\nPlease try again shortly!',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
</body>

</html>