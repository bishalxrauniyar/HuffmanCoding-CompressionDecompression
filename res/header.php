<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username_db = "root"; // Replace with your database username
$password_db = ""; // Replace with your database password
$dbname = "compression"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in, if not redirect them to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Store the username in a variable
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Compression Tool</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>

<body>

    <body>
        <nav class="navbar">
            <div class="navbar-brand">
                <a href="home.php" style="text-decoration:none; ">
                    <h1>File Compression Tool</h1>
                </a>
                <img src="assets/logo.png" alt="logo" width="50" height="50">

            </div>

            <div class=" navbar-links">
                <a href="info.php" target="_blank">About Compression Technique </a>
                <a href="pdf.php" target="_blank">PDF Compressor 💡 </a>
                <a href="history.php">File History </a>

            </div>

            <div class="greeting-navbar">
                <p>Welcome, <b><?php echo htmlspecialchars($username); ?>!</b></p>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
        </div>