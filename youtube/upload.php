<?php

$drive_link = "google drive link";
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
</head>

<body class="p-3">
    <h5 class="display-5">Upload Your Video to YouTube</h5>
    <hr>
    <div class="row pt-3">
        <div class="col-sm-12 col-md-5 col-lg-3"></div>
        <div class="col-sm-12 col-md-7 col-lg-7 mx-5">
            <div class="mb-3">
                <label for="video-title" class="form-label">Title</label>
                <input type="text" class="form-control" id="video-title" placeholder="Enter video title here...">
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
            <div class="row mt-3 justify-content-md-end">
                <div class="col-sm-12 col-md-6">
                    <button class="btn btn-primary w-100" id="submit-button">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#submit-button").click(function() {
                var link = '<?php echo $drive_link; ?>';
                var title = $("#video-title").val();
                var description = $("#video-description").val();
                var visibility = $('input[name="video-visibility"]:checked').val();
                /*console.log("Link: " + link);
                console.log("Title: " + title);
                console.log("Description: " + description);
                console.log("Public: " + visibility);*/
                /*$.post("", {
                    link: link,
                    title: title,
                    description: description,
                    visibility: visibility,
                    submit: 'upload'
                }, function(data, status) {
                    if (status == "success") {
                        if (data == "success") {
                            Swal.fire(
                                'Welcome!',
                                '',
                                'success'
                            ).then(function() {
                                window.location.href = "./";
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
                });*/
            });
        });
    </script>
</body>

</html>