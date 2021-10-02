<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" contnt="width=device-width, initial-scale=1.0">
<title>SSD Ass 2</title>
<!-- <link href="/static/css/style.css" rel="stylesheet"> -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

<body>

<div id="g_id_onload"
     data-client_id="191872964217-v20hu046iifmhp91vsqr6c9au2p7d51n.apps.googleusercontent.com"
     data-callback="handleCredentialResponse">
</div>
<script>
  function handleCredentialResponse(response) {
     // decodeJwtResponse() is a custom function defined by you
     // to decode the credential response.
     const responsePayload = decodeJwtResponse(response.credential);

     console.log("ID: " + responsePayload.sub);
     console.log('Full Name: ' + responsePayload.name);
     console.log('Given Name: ' + responsePayload.given_name);
     console.log('Family Name: ' + responsePayload.family_name);
     console.log("Image URL: " + responsePayload.picture);
     console.log("Email: " + responsePayload.email);

     window.alert("The value is:");
     window.alert(responsePayload.name);
  }
</script>
<?php
echo "I am logged in!";

?>

</body>
</html>