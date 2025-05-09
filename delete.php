<?php
session_start();

if (!isset($_SESSION['logged_in_user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['logged_in_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = $_POST['image_path'] ?? '';

    foreach ($_SESSION['images'] as $index => $image) {
        if ($image['path'] === $imagePath && $image['email'] === $user['email']) {
            // Remove image from session
            unset($_SESSION['images'][$index]);

            // Optional: remove physical file (only if you allow it)
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            break;
        }
    }

    // Re-index array
    $_SESSION['images'] = array_values($_SESSION['images']);
}

header("Location: dashboard.php?view=myuploads");
exit();
