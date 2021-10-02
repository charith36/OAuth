<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8" />
    <title>Google Picker Example</title>
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

    var userData=null;

    // Use the Google API Loader script to load the google.picker script.
    function loadPicker() {
      gapi.load('auth', {'callback': onAuthApiLoad});
      gapi.load('picker', {'callback': onPickerApiLoad});
      //gapi.load('auth2', {'callback': onProfileLog});

    }

    function onProfileLog(){

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

    function sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
   }

    async function handleAuthResult(authResult) {
      if (authResult && !authResult.error) {
        oauthToken = authResult.access_token;
        //console.log(authResult);
        userData = await fetch('https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token='+authResult.access_token).then(function(response){return response.json()});
        console.log(userData);
        document.getElementById('userprofilePic').src=userData.picture;
        //document.getElementById('userprofilePic').style.display="block";
        $('#userprofilePic').show(500);
        document.getElementById('userName').innerHTML=userData.email;
        document.getElementById('userEmail').innerHTML=userData.family_name;
        await sleep(500);
        $('#userData').fadeIn(500);

        createPicker();
      }
    }

    // Create and render a Picker object for searching images.
    function createPicker() {
      if (pickerApiLoaded && oauthToken) {
        var view = new google.picker.View(google.picker.ViewId.DOCS);
        view.setMimeTypes("image/png,image/jpeg,image/jpg");
        var picker = new google.picker.PickerBuilder()
            .enableFeature(google.picker.Feature.NAV_HIDDEN)
            .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
            .setAppId(appId)
            .setOAuthToken(oauthToken)
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
        var fileId = data.docs[0];
        console.log(fileId.url);
        alert('The user selected: ' + fileId);
      }
    }
    </script>
  </head>
  <body>
    <div id="result"></div>
    <div class="container">
        <div class="row">
            <img class="col-6" src="" style="height:auto; display:none;" id="userprofilePic">
            <div class="col-6" style="display:none" id="userData">
                <h4 class="col-12" id="userName"></h4>
                <h6 class="col-12" id="userEmail"></h6>
            </div>
        </div>
</div>
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