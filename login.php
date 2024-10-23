<?php
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
    $stmt = $conn->prepare("SELECT password, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);

    // Execute statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($pass, $row['password'])) {
            // Set session variables
            $_SESSION['username'] = $user;
            $_SESSION['role'] = $row['role']; // Store role in session
            // Regenerate session ID
            session_regenerate_id(true);

            // Redirect based on role
            if ($row['role'] == 'admin') {
                header("Location: admin_dash.php"); // Redirect admin to admin dashboard
            } else {
                header("Location: home.php"); // Redirect user to home
            }
            exit();
        } else {
            $error = "Invalid username or password";
        }
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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-image: url('assets/wallpaper1.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 40px 30px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .login-container:hover {
            transform: scale(1.02);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007BFF;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .alert {
            margin-top: 20px;
        }

        .register-btn {
            color: #007BFF;
            text-decoration: none;
            margin-top: 15px;
            transition: color 0.3s;
        }

        .register-btn:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2 class="text-center">Login to Your Account</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter Username" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <?php
        if (isset($error)) {
            echo "<div class='alert alert-danger' role='alert'>$error</div>";
        }
        ?>

        <a class="register-btn" href="register.php">Don't have an account? Register Now</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>