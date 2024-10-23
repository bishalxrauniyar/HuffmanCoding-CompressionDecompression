<?php
// register.php

// Start session
session_start();

// Initialize variables
$error = "";
$success = "";

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
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);

    // Validate form data
    if (empty($user) || empty($pass) || empty($confirm_pass) || empty($phone) || empty($email) || empty($firstname) || empty($lastname)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($pass !== $confirm_pass) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $user, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            // Hash the password
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            // Insert new user data
            $stmt = $conn->prepare("INSERT INTO user (username, password, phone, email, firstname, lastname) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $user, $hashed_pass, $phone, $email, $firstname, $lastname);

            if ($stmt->execute()) {
                $success = "Registration successful. You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-image: url('assets/registerbg.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: flex-end;
            /* Aligns the form to the right */
            align-items: center;
            height: 100vh;
            overflow: hidden;
            padding-right: 150px;
            /* Add some padding to the right */
        }

        .register-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 50px 15px;
            /* Reduced padding */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
            max-width: 500px;
            /* Increased width slightly */
            width: 100%;
            height: auto;
            /* Allows the form to be shorter */
        }

        h2 {
            margin-bottom: 10px;
            /* Reduced margin */
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
            margin-top: 10px;
            /* Reduced margin */
        }

        .login-btn {
            color: #007BFF;
            text-decoration: none;
            margin-top: 10px;
            /* Reduced margin */
            transition: color 0.3s;
        }

        .login-btn:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2 class="text-center">Register</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter Username" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>
            <div class="form-group">
                <label for="phone"><i class="fas fa-phone"></i> Phone No</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter Phone No" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" required>
            </div>
            <div class="form-group">
                <label for="firstname"><i class="fas fa-user-circle"></i> First Name</label>
                <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Enter First Name" required>
            </div>
            <div class="form-group">
                <label for="lastname"><i class="fas fa-user-circle"></i> Last Name</label>
                <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Enter Last Name" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <?php
        if (!empty($error)) {
            echo "<div class='alert alert-danger' role='alert'>$error</div>";
        }
        if (!empty($success)) {
            echo "<div class='alert alert-success' role='alert'>$success</div>";
        }
        ?>

        <a class="login-btn" href="login.php">Already Registered? Login</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>