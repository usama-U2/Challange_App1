<?php
session_start();

if (!isset($_SESSION['logged_in_user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['logged_in_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $image = $_FILES['image'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $uploadsDir = "uploads/";
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    $fileName = uniqid() . "_" . basename($image["name"]);
    $targetPath = $uploadsDir . $fileName;

    if (move_uploaded_file($image["tmp_name"], $targetPath)) {
        $imageData = [
            'path' => $targetPath,
            'title' => $title,
            'description' => $description,
            'location' => $user['location'],
            'email' => $user['email'],
            'name' => $user['name']
        ];

        // Save to session
        $_SESSION['images'][] = $imageData;

        header("Location: profile.php?view=myuploads");
        exit();
    } else {
        echo "Error uploading file.";
    }
}
?>
