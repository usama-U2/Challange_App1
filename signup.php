<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['users'][] = [
        'name' => $_POST['name'],
        'father_name' => $_POST['father_name'],
        'location' => $_POST['location'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
    ];
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Background gradient */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .signup-container {
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

        input[type="text"], input[type="email"], input[type="password"] {
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
            color: #2575fc; /* Blue link color */
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

<div class="signup-container">
    <h1>Sign Up</h1>

    <form method="POST">
        <label>Name:
            <input type="text" name="name" required>
        </label>

        <label>Father's Name:
            <input type="text" name="father_name" required>
        </label>

        <label>Location:
            <input type="text" name="location" required>
        </label>

        <label>Email:
            <input type="email" name="email" required>
        </label>

        <label>Password:
            <input type="password" name="password" required>
        </label>

        <button type="submit">Sign Up</button>

        <p><a href="index.php">Already have an account? Login here</a></p>
    </form>
</div>

</body>
</html>
