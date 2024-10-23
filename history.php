<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$servername = "localhost";
$username_db = "root"; // Replace with your database username
$password_db = ""; // Replace with your database password
$dbname = "compression"; // Replace with your database name

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$result = $conn->query("SELECT * FROM file_history WHERE username = '$username'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <style>
        /* General Styling */
        body,
        html {
            background-image: url('assets/history.jpg');
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f7f9fb;
            /* Light background color */
            color: #333;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #ff6347;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            margin: 0;
            font-size: 1.8em;
            font-family: 'Montserrat', sans-serif;
            /* Use Montserrat for headings */
        }

        .navbar-links {
            display: flex;
            gap: 20px;
        }

        .navbar-links a {
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s, transform 0.3s;
            position: relative;
        }

        .navbar-links a::before {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #FFD700;
            /* Gold underline effect */
            transition: width 0.3s ease;
        }

        .navbar-links a:hover::before {
            width: 100%;
            /* Expand underline on hover */
        }

        .navbar-links a:hover {
            color: #FFD700;
            /* Gold text color on hover */
            background-color: rgba(255, 255, 255, 0.2);
            /* Slight background highlight */
            transform: translateY(-2px);
            /* Slight lift effect */
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            /* Subtle text shadow */
        }

        /* Greeting Navbar (Right Side) */
        .greeting-navbar {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .greeting-navbar p {
            margin: 0;
            color: white;
            font-weight: bold;
            font-family: 'Roboto', sans-serif;
        }

        .greeting-navbar a {
            color: white;
            text-decoration: none;
            background-color: #333;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .greeting-navbar a:hover {
            background-color: #FFD700;
            color: #333;
        }

        /* Container Styling */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 1.8em;
            color: #2c3e50;
            font-family: 'Montserrat', sans-serif;
            /* Use Montserrat for heading */
            margin-bottom: 20px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        table td {
            background-color: #fff;
        }

        table tr:hover {
            background-color: #f1f1f1;
            /* Row hover effect */
        }

        a {
            color: #ff6347;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        a:hover {
            color: #ff4500;
            /* Darker red on hover */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-links {
                flex-direction: column;
                gap: 10px;
            }

            .container {
                width: 90%;
                padding: 15px;
            }

            table th,
            table td {
                padding: 10px;
            }

            .greeting-navbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar section -->
    <div class="navbar">
        <div class="navbar-brand">
            <h1>Compression Tool</h1>
        </div>
        <div class="navbar-links">
            <a href="home.php">Home</a>
            <a href="history.php">History</a>
        </div>
        <div class="greeting-navbar">
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main content section -->
    <div class="container">
        <h2>Your Upload History</h2>
        <table>
            <tr>
                <th>Original File</th>
                <th>Compressed File</th>
                <th>Decompressed File</th>
                <th>Upload Time</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['original_file']); ?></td>
                    <td>
                        <?php if ($row['compressed_file']): ?>
                            <a href="uploads/<?php echo htmlspecialchars($row['compressed_file']); ?>" download><?php echo htmlspecialchars($row['compressed_file']); ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['decompressed_file']): ?>
                            <a href="uploads/<?php echo htmlspecialchars($row['decompressed_file']); ?>" download><?php echo htmlspecialchars($row['decompressed_file']); ?></a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['upload_time']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>