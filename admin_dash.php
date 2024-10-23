<?php
// admin_dash.php

session_start();

// Ensure only admins can access this page
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "compression";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add user
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO user (username, password, email, phone, firstname, lastname, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $password, $email, $phone, $firstname, $lastname, $role);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dash.php");
}

// Update user
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE user SET username=?, password=?, email=?, phone=?, firstname=?, lastname=?, role=? WHERE id=?");
        $stmt->bind_param("sssssssi", $username, $password, $email, $phone, $firstname, $lastname, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE user SET username=?, email=?, phone=?, firstname=?, lastname=?, role=? WHERE id=?");
        $stmt->bind_param("ssssssi", $username, $email, $phone, $firstname, $lastname, $role, $id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: admin_dash.php");
}

// Delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dash.php");
}

// Search functionality
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

$sql = "SELECT * FROM user WHERE username LIKE ? OR email LIKE ? OR firstname LIKE ? OR lastname LIKE ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$users = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom right, #fff3e0, #ffcc80);
            /* Light orange to darker orange gradient */
        }

        .wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom right, #ff5722, #ff9800);
            /* Dark orange to lighter orange gradient */
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar a {
            color: white;
            padding: 15px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: rgba(255, 152, 0, 0.8);
            /* Lighten the hover effect */
            color: white;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .table-wrapper {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .card {
            background-color: #f9e5d8;
            /* Light background for cards */
        }

        .modal-header {
            background-color: #ff5722;
            /* Sidebar color */
            color: white;
        }

        .modal-footer .btn {
            background-color: #ff9800;
            /* Lighter orange for buttons */
            border: none;
        }

        .modal-footer .btn:hover {
            background-color: #fb8c00;
            /* Darker orange on hover */
        }
    </style>


</head>

<body>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2 class="text-center">Admin Panel</h2>
            <a href="admin_dash.php">Dashboard</a>
            <a href="admin_dash.php">Users</a>
            <a href="logout.php">Logout</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h1 class="mb-4">User Management</h1>

            <!-- Search Form -->
            <form method="post" class="mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search users..." name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </div>
            </form>

            <!-- User Table -->
            <div class="table-wrapper">
                <h3 class="mb-3">All Users</h3>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['firstname']; ?></td>
                                <td><?php echo $row['lastname']; ?></td>
                                <td><?php echo $row['role']; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal" data-target="#editModal"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-username="<?php echo $row['username']; ?>"
                                        data-email="<?php echo $row['email']; ?>"
                                        data-phone="<?php echo $row['phone']; ?>"
                                        data-firstname="<?php echo $row['firstname']; ?>"
                                        data-lastname="<?php echo $row['lastname']; ?>"
                                        data-role="<?php echo $row['role']; ?>">Edit</button>
                                    <a href="admin_dash.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add New User Button -->
            <br>
            <div class="text-right">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add New User</button>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" class="form-control" name="firstname" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" class="form-control" name="lastname" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label for="edit-username">Username</label>
                            <input type="text" class="form-control" name="username" id="edit-username" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-password">Password (leave blank to keep the same)</label>
                            <input type="password" class="form-control" name="password" id="edit-password">
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Email</label>
                            <input type="email" class="form-control" name="email" id="edit-email" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-phone">Phone</label>
                            <input type="text" class="form-control" name="phone" id="edit-phone" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-firstname">First Name</label>
                            <input type="text" class="form-control" name="firstname" id="edit-firstname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-lastname">Last Name</label>
                            <input type="text" class="form-control" name="lastname" id="edit-lastname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-role">Role</label>
                            <select class="form-control" name="role" id="edit-role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Populate edit modal with user data
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id');
            var username = button.data('username');
            var email = button.data('email');
            var phone = button.data('phone');
            var firstname = button.data('firstname');
            var lastname = button.data('lastname');
            var role = button.data('role');

            var modal = $(this);
            modal.find('#edit-id').val(id);
            modal.find('#edit-username').val(username);
            modal.find('#edit-email').val(email);
            modal.find('#edit-phone').val(phone);
            modal.find('#edit-firstname').val(firstname);
            modal.find('#edit-lastname').val(lastname);
            modal.find('#edit-role').val(role);
        });
    </script>
</body>

</html>