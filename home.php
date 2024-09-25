<?php
// Start session
session_start();

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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to File Compression Tool</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.html" class="navbar-brand">
                <img src="assets/txt-logo.png" alt="logo" width="30" height="30">
                File Compression Tool!
            </a>
            <a href="info.html" target="_blank" class="nav-link">About Compression Technique 💡</a>
        </div>

        <!-- Greeting container outside the .container to push it to the right -->
        <div class="greeting-navbar">
            <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
        </div>
    </nav>

    <main class="container">
        <section class="step" id="step1">
            <h2>Step 1: Upload Your File (.txt Only)</h2>
            <form id="fileform">
                <input type="file" id="uploadfile" accept=".txt">
                <button type="button" id="submitbtn" class="btn">Upload</button>
            </form>
        </section>

        <section class="step" id="step2">
            <h2>Step 2: Choose an Action</h2>
            <button type="button" id="encode" class="btn action-btn">Compress</button>
            <button type="button" id="decode" class="btn action-btn">Decompress</button>
        </section>

        <section id="feedback" class="step">
            <h2>Sit Back and Relax</h2>
            <button class="btn" onclick="location.reload()">Reload</button>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 File Compression Tool</p>
    </footer>
</body>

</html>