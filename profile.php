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
    <h2>Challenge Mode</h2>
    <form method="GET" style="text-align:center;">
        <input type="hidden" name="view" value="challenge">
        <label>Select Your Image:
            <select name="my_image" required>
                <?php foreach ($_SESSION['images'] as $image): ?>
                    <?php if ($image['email'] === $user['email']): ?>
                        <option value="<?= $image['path'] ?>"><?= htmlspecialchars($image['title']) ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Select Opponent Image:
            <select name="opponent_image" required>
                <?php foreach ($_SESSION['images'] as $image): ?>
                    <?php if ($image['email'] !== $user['email']): ?>
                        <option value="<?= $image['path'] ?>"><?= htmlspecialchars($image['title']) ?> by <?= htmlspecialchars($image['name']) ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Start Challenge</button>
    </form>

    <?php
    $my_img = $_GET['my_image'] ?? null;
    $op_img = $_GET['opponent_image'] ?? null;
    if ($my_img && $op_img):
    ?>
        <div class="gallery">
            <?php foreach ([$my_img, $op_img] as $img_path): ?>
                <?php
                    $id = md5($img_path);
                    $image = null;
                    foreach ($_SESSION['images'] as $img) {
                        if ($img['path'] === $img_path) {
                            $image = $img;
                            break;
                        }
                    }
                    if (!$image) continue;
                    $liked = isset($_SESSION['likes'][$id][$user['email']]);
                    $likeCount = isset($_SESSION['likes'][$id]) ? count($_SESSION['likes'][$id]) : 0;
                ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($img_path) ?>">
                    <strong><?= htmlspecialchars($image['title']) ?></strong><br>
                    <em><?= htmlspecialchars($image['description']) ?></em><br>
                    <small>By: <?= htmlspecialchars($image['name']) ?></small><br><br>
                    <form method="POST" action="like.php">
                        <input type="hidden" name="image_id" value="<?= $id ?>">
                        <button type="submit"><?= $liked ? "❤️" : "🤍" ?> <?= $likeCount ?></button>
                    </form>
                    <form method="POST" action="comment.php">
                        <input type="hidden" name="image_id" value="<?= $id ?>">
                        <input type="text" name="comment" placeholder="Write a comment">
                        <button type="submit">Post</button>
                    </form>
                    <div class="comment-box">
                        <strong>Comments:</strong>
                        <?php
                        if (isset($_SESSION['comments'][$id])):
                            foreach ($_SESSION['comments'][$id] as $comment):
                                echo "<div class='comment'><em>" . htmlspecialchars($comment['user']) . "</em>: " . htmlspecialchars($comment['text']) . "</div>";
                            endforeach;
                        else:
                            echo "<div style='color:#999;'>No comments yet.</div>";
                        endif;
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (!$challenge_mode): ?>
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
