<?php
// login.php

// Start session
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "compression";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $user, $pass);

    // Execute statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Set session variables
        $_SESSION['username'] = $user;
        // Redirect to home.php
        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            display: flex;
            /* Use Flexbox */
            justify-content: center;
            /* Center horizontally */
            align-items: center;
            /* Center vertically */
            height: 100vh;
            /* Full viewport height */
            overflow: hidden;
            /* Prevents scrolling */
        }

        body::before {
            content: "";
            position: fixed;
            /* Position it fixed to cover the whole viewport */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('assets/wallpaper.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            filter: blur(8px);
            /* Adjust the blur amount as needed */
            z-index: -1;
            /* Send the pseudo-element behind the login container */
        }

        .login-container {
            position: relative;
            /* Allows positioning relative to the parent */
            background: linear-gradient(145deg, #f1f1f1, #e3e3e3);
            /* Metallic gradient */
            border-radius: 10px;
            box-shadow: 20px 20px 60px #d1d1d1,
                -20px -20px 60px #ffffff;
            /* Metallic effect */
            padding: 30px;
            width: 350px;
            text-align: center;
            transition: all 0.3s ease;
            /* Smooth transition */
        }

        .login-container:hover {
            box-shadow: 30px 30px 80px #b0b0b0,
                -30px -30px 80px #ffffff;
            /* Enhance shadow on hover */
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
            /* Darker text color for contrast */
            font-family: 'Arial', sans-serif;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s ease;
            /* Smooth transition for border color */
            font-size: 16px;
            /* Increased font size */
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007BFF;
            /* Blue border on focus */
            outline: none;
            /* Remove default outline */
        }

        input[type="submit"] {
            background: linear-gradient(145deg, #4CAF50, #2e7d32);
            /* Green gradient */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            /* Increased font size */
            transition: background 0.3s ease, transform 0.2s;
            /* Smooth transition */
        }

        input[type="submit"]:hover {
            background: linear-gradient(145deg, #45a049, #388e3c);
            /* Darker green on hover */
            transform: translateY(-2px);
            /* Slight lift on hover */
        }

        .register-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: linear-gradient(145deg, #007BFF, #0056b3);
            /* Blue gradient */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease, transform 0.2s;
            /* Smooth transition */
        }

        .register-btn:hover {
            background: linear-gradient(145deg, #0056b3, #003d7a);
            /* Darker blue on hover */
            transform: translateY(-2px);
            /* Slight lift on hover */
        }

        p {
            color: red;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login Page</h2>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>

        <?php
        if (isset($error)) {
            echo "<p>$error</p>";
        }
        ?>

        <a class="register-btn" href="register.php">Register Now</a>
    </div>
</body>

</html>