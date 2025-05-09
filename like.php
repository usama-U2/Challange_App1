<?php
session_start();

if (!isset($_SESSION['logged_in_user']) || !isset($_POST['image_id'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['logged_in_user'];
$imageId = $_POST['image_id'];
$email = $user['email'];

if (!isset($_SESSION['likes'][$imageId])) {
    $_SESSION['likes'][$imageId] = [];
}

// Toggle like
if (isset($_SESSION['likes'][$imageId][$email])) {
    unset($_SESSION['likes'][$imageId][$email]);
} else {
    $_SESSION['likes'][$imageId][$email] = true;
}

header("Location: profile.php?view=location");
exit();
