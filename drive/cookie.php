<?php

if (isset($_POST['submit'])) {
    if ($_POST['submit'] == "file-selected") {
        $id = $_POST['id'];

        if (empty($id)) {
            echo 'System error!';
            exit;
        }

        //setcookie("video", $id, time() + 36000);
        session_start();
        $_SESSION['id'] = $id;
        echo 'success';
        exit;
    }
}
