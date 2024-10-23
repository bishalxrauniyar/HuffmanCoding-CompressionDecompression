<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

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
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="home.php" style="text-decoration:none;">
                <h1>File Compression Tool</h1>
            </a>
            <img src="assets/logo.png" alt="logo" width="50" height="50">
        </div>

        <div class="navbar-links">
            <a href="info.php" target="_blank">About Compression Technique</a>
            <a href="pdf.php" target="_blank">PDF Compressor 💡</a>
            <a href="history.php">File History</a>
        </div>

        <div class="greeting-navbar">
            <p>Welcome, <b><?php echo htmlspecialchars($username); ?>!</b></p>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="step">
            <form id="uploadForm" action="upload_to_db.php" method="post" enctype="multipart/form-data">
                <h2>File Upload</h2>
                <input type="file" name="file" required>
                <select name="action_type" required>
                    <option value="">Select Action</option>
                    <option value="compress">Compress</option>
                    <option value="decompress">Decompress</option>
                </select>
                <button type="submit" class="btn">Upload</button>
            </form>
        </div>

        <div class="step">
            <h2>Your Uploads</h2>
            <p>You can view your upload history <a href="history.php">here</a>.</p>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Compression Tool. All rights reserved.</p>
    </footer>
</body>

</html>