<?php

//Include Configuration File
include('config.php');

$login_button = '';

//This $_GET["code"] variable value received after user has login into their Google Account redirct to PHP script then this variable value has been received
if(isset($_GET["code"]))
{
 //It will Attempt to exchange a code for an valid authentication token.
 $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

 //This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
 if(!isset($token['error']))
 {
  //Set the access token used for requests
  $google_client->setAccessToken($token['access_token']);

  //Store "access_token" value in $_SESSION variable for future use.
  $_SESSION['access_token'] = $token['access_token'];
  $access_token_variable = $_SESSION['access_token'];

  //Create Object of Google Service OAuth 2 class
  $google_service = new Google_Service_Oauth2($google_client);
  // $google_service_drive = new Google_Service_Drive($google_client);

  //Get user data from google
  $data = $google_service->userinfo->get();
  // $files = $service->files->listFiles($parameters);

  //Below you can find Get profile data and store into $_SESSION variable
  if(!empty($data['given_name']))
  {
   $_SESSION['user_first_name'] = $data['given_name'];
  }

  if(!empty($data['family_name']))
  {
   $_SESSION['user_last_name'] = $data['family_name'];
  }

  if(!empty($data['email']))
  {
   $_SESSION['user_email_address'] = $data['email'];
  }
  }

  if(!empty($data['picture']))
  {
   $_SESSION['user_image'] = $data['picture'];
  }
}


//This is for check user has login into system by using Google account, if User not login into system then it will execute if block of code and make code for display Login link for Login using Google account.
if(!isset($_SESSION['access_token']))
{
 //Create a URL to obtain user authorization
 $login_button = '<a href="'.$google_client->createAuthUrl().'"><img src="assets/Sign-in-with-google.png" /></a>';
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SSD Ass 2</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="js/bootstrap.bundle.min.js" rel="stylesheet" />

    <script type="text/javascript">

    // The Browser API key obtained from the Google API Console.
    // Replace with your own Browser API key, or your own key.
    var developerKey = 'AIzaSyBifKJcjX0gEsggaFMU0zmnu7qJ_nmWZCQ';

    // The Client ID obtained from the Google API Console. Replace with your own Client ID.
    var clientId = "191872964217-mqeb0dakacjbk01ta296q5h0f7mp8eno.apps.googleusercontent.com"

    // Replace with your own project number from console.developers.google.com.
    // See "Project number" under "IAM & Admin" > "Settings"
    var appId = "191872964217";

    // Scope to use to access user's Drive items.
    var scope = ['https://www.googleapis.com/auth/drive.file'];


    var pickerApiLoaded = false;
    var oauthToken;

    // Use the Google API Loader script to load the google.picker script.
    function loadPicker() {
      gapi.load('auth', {'callback': onAuthApiLoad});
      gapi.load('picker', {'callback': onPickerApiLoad});
    }

    function onAuthApiLoad() {
      window.gapi.auth.authorize(
          {
            'client_id': clientId,
            'scope': scope,
            'immediate': false
          },
          handleAuthResult);
    }

    function onPickerApiLoad() {
      pickerApiLoaded = true;
      createPicker();
    }

    function handleAuthResult(authResult) {
      if (authResult && !authResult.error) {
        oauthToken = authResult.access_token;
        createPicker();
      }
    }

    // Create and render a Picker object for searching images.
    function createPicker() {
      if (pickerApiLoaded && oauthToken) {
        var access_token_variable = "<?php echo $access_token_variable; ?>" ;
        alert('Value is : ' + access_token_variable);
        var view = new google.picker.View(google.picker.ViewId.DOCS);
        view.setMimeTypes("image/png,image/jpeg,image/jpg");
        var picker = new google.picker.PickerBuilder()
            .enableFeature(google.picker.Feature.NAV_HIDDEN)
            .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
            .setAppId(appId)
            .setOAuthToken(access_token_variable)
            .addView(view)
            .addView(new google.picker.DocsUploadView())
            .setDeveloperKey(developerKey)
            .setCallback(pickerCallback)
            .build();
         picker.setVisible(true);
      }
    }

    // A simple callback implementation.
    function pickerCallback(data) {
      if (data.action == google.picker.Action.PICKED) {
        var fileId = data.docs[0].id;
        alert('The user selected: ' + fileId);
      }
    }
    </script>
  </head>
  <body>

    <div class="container">
      <br />
      <h2 align="center">PHP Login using Google Account</h2>
      <br />
      <div class="panel panel-default">
        <?php
        if($login_button == '')
        {
          echo '<div class="panel-heading">Welcome User</div><div class="panel-body">';
          echo '<img src="'.$_SESSION["user_image"].'" class="img-responsive img-circle img-thumbnail" />';
          echo '<h3><b>Name :</b> '.$_SESSION['user_first_name'].' '.$_SESSION['user_last_name'].'</h3>';
          echo '<h3><b>Email :</b> '.$_SESSION['user_email_address'].'</h3>';
          echo '<h3><a href="logout.php">Logout</h3></div>';
        }
        else
        {
          echo '<div align="center">'.$login_button . '</div>';
        }
        ?>
      </div>
    </div>


    <div id="result"></div>
    <button onclick="showPickerDialog()">Show Picker Dialog</button>

    <!-- The Google API Loader script. -->
    <script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
    <script>
    function showPickerDialog(){
        loadPicker()
    }
    </script>
  </body>
</html>