<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8" />
    <title>Google Picker Example</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
  <link rel="stylesheet" href="css/drive.css">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <link href="js/bootstrap.bundle.min.js" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/842606c366.js" crossorigin="anonymous"></script>

    <script type="text/javascript">

    //Browser API key
    var developerKey = 'AIzaSyBifKJcjX0gEsggaFMU0zmnu7qJ_nmWZCQ';

    //Client ID
    var clientId = "191872964217-mqeb0dakacjbk01ta296q5h0f7mp8eno.apps.googleusercontent.com"

    //Project number
    var appId = "191872964217";

    //Scope to use to access user's Drive items
    var scope = ['https://www.googleapis.com/auth/drive.file'];

    //Define variables
    var pickerApiLoaded = false;
    var oauthToken;
    var userData=null;

    //Load Google Picker
    function loadPicker() {
      gapi.load('auth', {'callback': onAuthApiLoad});
      gapi.load('picker', {'callback': onPickerApiLoad});
    }

    //Authentication
    function onAuthApiLoad() {
      window.gapi.auth.authorize(
          {
            'client_id': clientId,
            'scope': scope,
            'immediate': false
          },
          handleAuthResult);
    }

    //Load the Picker
    function onPickerApiLoad() {
      pickerApiLoaded = true;
      createPicker();
    }

    //Animation for future object
    function sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
   }

    //Get user information
    async function handleAuthResult(authResult) {
      if (authResult && !authResult.error) {
        oauthToken = authResult.access_token;
        
        userData = await fetch('https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token='+authResult.access_token).then(function(response){return response.json()});
        console.log(userData);
        document.getElementById('userprofilePic').src=userData.picture;
        
        //Display user details
        $('#userprofilePic').show(500);
        document.getElementById('userName').innerHTML=userData.email;
        document.getElementById('userEmail').innerHTML=userData.family_name;

        await sleep(500);
        $('#userData').fadeIn(500);

        createPicker();
      }
    }

    //Create and render Picker object
    function createPicker() {
      if (pickerApiLoaded && oauthToken) {
        var view = new google.picker.View(google.picker.ViewId.DOCS);
        view.setMimeTypes("video/mp4,video/mov,video/mpeg-1,video/mpeg-2,video/mpeg4,video/mpg,video/avi,video/wmv,video/flv,video/3gpp,video/webm,video/prores,video/hevec,video/cineform,video/dnxhr");
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

    //Picker callback
    function pickerCallback(data) {
      if (data.action == google.picker.Action.PICKED) {
        var fileId = data.docs[0];
        console.log(fileId);

        //Show hidden steps
        var x = document.getElementById("hiddenShow");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }

        var x = document.getElementById("hiddenShowYou");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
        
        //Set values
        document.getElementById('fileName').innerHTML="File Name: " + fileId.name;
        document.getElementById('fileID').innerHTML="File ID: " + fileId.id;
        document.getElementById('fileType').innerHTML="Video Type: " + fileId.mimeType;
        document.getElementById('fileSize').innerHTML="Size: " + fileId.sizeBytes + " Bytes";
        document.getElementById('fileUrl').innerHTML="File URL: " + fileId.url;
        document.getElementById('loadDriveBtn').innerHTML="Choose another file";
      }
    }
    </script>


  </head>
  <body>
    <div id="result"></div>
    <div class="row mt-3"></div>
    <div class="row mt-3"></div>
    <div class="container">
        <div class="container d-flex align-items-center pt-sm pb-sm text-center">
            <div class="col px-0 text-center">
                <h1 class="my-0 text-truncate h1-resp shad display-4">Drive2Tube</h1>
                <h2 class="my-0 h2-resp text-muted">Upload your Google Drive videos directly to Youtube without a hazzle!</h2>
                <h3 class="h3">Click on the button below to get started!</h3>
            </div>
        </div>

        <div class="row mt-3"></div>
        <div class="row mt-3"></div>

        <div class="row">
            <img class="user-profile" src="" style="height:auto; display:none;" id="userprofilePic">
            <div style="display:none" id="userData">
                <h4>Welcome</h4>
                <h4 class="col-12" id="userEmail"></h4>
                <h6 class="col-12" id="userName"></h6>
            </div>
        </div>

        <div class="col-md-12 text-center"> 
            <!--Button to initiate Drive access -->
            <button class="btn btn-success" id="loadDriveBtn" onclick="showPickerDialog()"><i class="fab fa-google-drive"></i>Load Drive file</button>
        </div>

        <div class="row mt-3"></div>
        <div id="hiddenShow" style="display:none" class="card">
            <div class="card-header">
            <h4><strong>Step 1: Select file</strong></h4>
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <i class="far fa-arrow-alt-circle-right"></i><p id="fileName"></p>
                    <p id="fileID"></p>
                    <p id="fileType"></p>
                    <p id="fileSize"></p>
                    <p id="fileUrl"></p>
                </blockquote>
            </div>
        </div>

        <div id="hiddenShowYou" style="display:none" class="card">
            <div class="row mt-3"></div>
            <div class="card-header">
            <h4><strong>Step 2: Select Youtube</strong></h4>
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    
                </blockquote>
            </div>
        </div>
        
    </div>


    <!-- The Google API Loader script. -->
    <script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
    
    <script>

    //When Drive button is pressed
    function showPickerDialog(){
        loadPicker()
    }
    </script>
    
  </body>
</html>