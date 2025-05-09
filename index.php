<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    foreach ($_SESSION['users'] as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            $_SESSION['logged_in_user'] = $user;
            header("Location: profile.php");
            exit();
        }
    }
    echo "<p style='color:red; font-weight:bold;'>Invalid email or password.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Background gradient */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .login-container {
            max-width: 450px;
            margin: 100px auto;
            background-color: #ffffff; /* White background for the form */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2575fc;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Gradient background */
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            margin-top: 20px;
            font-size: 16px;
            transition: 0.3s;
        }

        button:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb); /* Reverse gradient on hover */
        }

        p {
            text-align: center;
            margin-top: 15px;
        }

        a {
            text-decoration: none;
            color: #2575fc;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1>Login to Challange</h1>

    <form method="POST">
        <label>Email:
            <input type="email" name="email" required>
        </label>

        <label>Password:
            <input type="password" name="password" required>
        </label>

        <button type="submit">Login</button>

        <p><a href="signup.php">Don't have an account? Signup here</a></p>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($email) && !empty($password)) {
        echo "<p class='error-message'>Invalid email or password.</p>";
    }
    ?>
</div>

</body>
</html>
