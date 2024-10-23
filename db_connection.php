// Purpose: Establishes a connection to the database
<?php

$servername = "localhost";
$username = "root"; // 
$password = ""; // 
$dbname = "compression";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
