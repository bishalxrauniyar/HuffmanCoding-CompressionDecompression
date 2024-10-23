<?php
session_start();

// Database connection details
$servername = "localhost";
$username_db = "root"; // Replace with your database username
$password_db = ""; // Replace with your database password
$dbname = "compression"; // Replace with your database name

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if a file is uploaded
    if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        header("Location: home.php?error=No file uploaded.");
        exit();
    }

    // Check if the action type is set
    if (!isset($_POST['action_type'])) {
        header("Location: home.php?error=No action specified.");
        exit();
    }

    // Sanitize file name and action type
    $originalFile = basename($_FILES['file']['name']);
    $actionType = $_POST['action_type'];

    // Ensure the uploaded file is a valid type (optional: limit to certain types like .txt)
    $fileType = strtolower(pathinfo($originalFile, PATHINFO_EXTENSION));
    if ($fileType !== 'txt') {
        header("Location: home.php?error=Invalid file type. Only .txt files are allowed.");
        exit();
    }

    // Target directory and file path
    $target_dir = "uploads/";
    $target_file = $target_dir . $originalFile;

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        // Include the Codec class and create an instance
        require_once 'codec.php';
        $codec = new Codec();

        // Perform compression or decompression
        if ($actionType === 'compress') {
            $data = file_get_contents($target_file);
            list($encoded_data, $message) = $codec->encode($data);

            // Write compressed file
            $processedFile = "compressed_" . $originalFile;
            file_put_contents($target_dir . $processedFile, $encoded_data);

            // Insert into history
            insertFileHistory($conn, $_SESSION['username'], $originalFile, $processedFile, null);

            // Redirect back to home.php after success
            header("Location: home.php?success=File compressed successfully.");
            exit();
        } elseif ($actionType === 'decompress') {
            $data = file_get_contents($target_file);
            list($decoded_data, $message) = $codec->decode($data);

            // Write decompressed file
            $processedFile = "decompressed_" . $originalFile;
            file_put_contents($target_dir . $processedFile, $decoded_data);

            // Insert into history
            insertFileHistory($conn, $_SESSION['username'], $originalFile, null, $processedFile);

            // Redirect back to home.php after success
            header("Location: home.php?success=File decompressed successfully.");
            exit();
        } else {
            header("Location: home.php?error=Invalid action type.");
            exit();
        }
    } else {
        header("Location: home.php?error=Error uploading file.");
        exit();
    }
}

// Function to insert file history into the database
function insertFileHistory($conn, $username, $originalFile, $compressedFile, $decompressedFile)
{
    $stmt = $conn->prepare("INSERT INTO file_history (username, original_file, compressed_file, decompressed_file, upload_time) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $username, $originalFile, $compressedFile, $decompressedFile);
    $stmt->execute();
    $stmt->close();
}

// Close the database connection
$conn->close();
