<?php
session_start();

if (!isset($_SESSION['logged_in_user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_id = $_POST['image_id'] ?? '';
    $comment_text = trim($_POST['comment'] ?? '');

    if ($image_id !== '' && $comment_text !== '') {
        // Sanitize input
        $comment_text = htmlspecialchars($comment_text, ENT_QUOTES, 'UTF-8');
        $user_email = $_SESSION['logged_in_user']['email'];
        $user_name = $_SESSION['logged_in_user']['name'];

        // Initialize comments array if needed
        if (!isset($_SESSION['comments'][$image_id])) {
            $_SESSION['comments'][$image_id] = [];
        }

        // Add comment
        $_SESSION['comments'][$image_id][] = [
            'user' => $user_name,
            'email' => $user_email,
            'text' => $comment_text
        ];
    }
}

// Redirect back to previous page
$ref = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php?view=challenge';
header("Location: " . $ref);
exit();
