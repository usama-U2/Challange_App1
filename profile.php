<?php
session_start();

if (!isset($_SESSION['logged_in_user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['logged_in_user'];
$view = $_GET['view'] ?? 'myuploads';

if (!isset($_SESSION['likes'])) $_SESSION['likes'] = [];
if (!isset($_SESSION['comments'])) $_SESSION['comments'] = [];
if (!isset($_SESSION['images'])) $_SESSION['images'] = [];

$challenge_mode = ($view === 'challenge');

// Handle image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image']) && isset($_POST['image_path'])) {
    $deletePath = $_POST['image_path'];
    // Remove image from session
    foreach ($_SESSION['images'] as $key => $img) {
        if ($img['path'] === $deletePath && $img['email'] === $user['email']) {
            unset($_SESSION['images'][$key]);

            // Remove likes and comments
            $img_id = md5($deletePath);
            unset($_SESSION['likes'][$img_id]);
            unset($_SESSION['comments'][$img_id]);

            // Optionally delete file from server
            if (file_exists($deletePath)) {
                unlink($deletePath);
            }
            break;
        }
    }
    // Re-index array
    $_SESSION['images'] = array_values($_SESSION['images']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            color: #333;
        }
        h1, h2 {
            text-align: center;
            color: #2c3e50;
        }
        form, .card {
            background: #ffffff;
            padding: 20px;
            margin: 20px auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: black;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            width: 80%;
            margin: 15px auto;
            display: block;
        }
        button:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
            transform: scale(1.05);
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }
        .card {
            width: 250px;
            text-align: center;
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .comment-box {
            text-align: left;
            margin-top: 10px;
        }
        .comment {
            background: #f0f0f0;
            padding: 6px;
            border-radius: 6px;
            margin: 4px 0;
        }
    </style>
</head>
<body>

<h1>Welcome, <?php echo htmlspecialchars($user['name']); ?></h1>

<h2>Upload Image</h2>
<form method="POST" enctype="multipart/form-data" action="upload.php">
    <label>Image:
        <input type="file" name="image" required>
    </label>
    <label>Title:
        <input type="text" name="title" required>
    </label>
    <label>Description:
        <textarea name="description" required></textarea>
    </label>
    <button type="submit">Upload</button>
</form>

<div class="nav-btns">
    <a href="?view=myuploads"><button>My Profile</button></a>
    <a href="?view=location"><button>Home</button></a>
    <a href="?view=challenge"><button>Challenge</button></a>
    <a href="index.php"><button>Logout</button></a>
</div>

<hr>

<?php if ($challenge_mode): ?>
    <!-- Challenge Mode Section (unchanged) -->
    <!-- ... -->
<?php else: ?>
    <div class="gallery">
        <?php
        $hasImages = false;
        if ($view === 'location') {
            echo "<h2 style='width:100%; text-align:center;'>Home</h2>";
            foreach ($_SESSION['images'] as $image) {
                if ($image['location'] === $user['location']) {
                    $hasImages = true;
                    $id = md5($image['path']);
                    $liked = isset($_SESSION['likes'][$id][$user['email']]);
                    $likeCount = isset($_SESSION['likes'][$id]) ? count($_SESSION['likes'][$id]) : 0;

                    echo "<div class='card'>";
                    echo "<img src='" . htmlspecialchars($image['path']) . "'>";
                    echo "<strong>" . htmlspecialchars($image['title']) . "</strong><br>";
                    echo "<em>" . htmlspecialchars($image['description']) . "</em><br>";
                    echo "<small>By: " . htmlspecialchars($image['name']) . "</small><br><br>";
                    echo "<form method='POST' action='like.php'>";
                    echo "<input type='hidden' name='image_id' value='$id'>";
                    echo "<button type='submit'>" . ($liked ? "❤️" : "🤍") . " $likeCount</button>";
                    echo "</form>";
                    echo "</div>";
                }
            }
            if (!$hasImages) echo "<p>No images found from your location.</p>";
        } else {
            echo "<h2 style='width:100%; text-align:center;'>Your Uploaded Images</h2>";
            foreach ($_SESSION['images'] as $image) {
                if ($image['email'] === $user['email']) {
                    $hasImages = true;
                    echo "<div class='card'>";
                    echo "<img src='" . htmlspecialchars($image['path']) . "'>";
                    echo "<strong>" . htmlspecialchars($image['title']) . "</strong><br>";
                    echo "<em>" . htmlspecialchars($image['description']) . "</em><br>";

                    // Delete form
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='delete_image' value='1'>";
                    echo "<input type='hidden' name='image_path' value='" . htmlspecialchars($image['path']) . "'>";
                    echo "<button type='submit' style='background:#f44336; color:white;'>Delete</button>";
                    echo "</form>";

                    echo "</div>";
                }
            }
            if (!$hasImages) echo "<p>You have not uploaded any images yet.</p>";
        }
        ?>
    </div>
<?php endif; ?>

</body>
</html>
