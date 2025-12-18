<?php
    session_start();
    if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';
    require 'AdminFunctions.php';

    if (isset($_GET['success'])) {
        $success = $_GET['success'];
        echo "<script>alert('Success: " . htmlspecialchars($success) . "');</script>";
    }
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        echo "<script>alert('Error: " . htmlspecialchars($error) . "');</script>";
    }

    // Handle form submission
    if (isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['first_name'], $_POST['last_name'], $_POST['role'], $_POST['phone'], $_POST['add-user'])) {
        $userData = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'role' => $_POST['role'],
            'contact_number' => $_POST['phone']
        ];

        if (usernameExists($conn, $userData['username'])) {  //check if username is taken
            header("Location: add.php?error=username+already+taken");
            exit();
        }

        if (emailExists($conn, $userData['email'])) {   //check if email is taken
            header("Location: add.php?error=email+already+taken");
            exit();
        }

        if (!preg_match('/^[0-9+\s-]+$/', $userData['contact_number'])) { // Validate contact number format
            header("Location: add.php?error=invalid+contact+number+format");
            exit();
        }

        if (strlen($_POST['password']) < 8) { // Validate password 
            header("Location: add.php?error=password+must+be+at+least+8+characters");
            exit();
        } elseif (!preg_match('/[A-Z]/', $_POST['password']) || !preg_match('/[a-z]/', $_POST['password']) || !preg_match('/[0-9]/', $_POST['password']) || !preg_match('/[\W]/', $_POST['password'])) {
            header("Location: add.php?error=password+must+include+uppercase,+lowercase,+number,+and+special+character");
            exit();
        }

        if (filter_var($userData['email'], FILTER_VALIDATE_EMAIL) === false) {
            header("Location: add.php?error=invalid+email+format");
            exit();
        }

        //successful addition
        if (addNewUser($conn, $userData)) {
            header("Location: users.php?success=user+added+successfully");
            exit();
        } else {
            header("Location: add.php?error=failed+to+add+user");
            exit();
        }
    }
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Add User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!--icons-->
    <link rel="stylesheet" href="../CSS/addManga.css"> 
    <link rel="stylesheet" href="../CSS/test.css"> 
    <link rel="icon" type="image/x-icon" href="../images/konLogo.png">
</head>
<body>
     <nav class="nav-bar">
        <section class="nav-logo">
            <img src="../images/konLogo.png" alt="BookWyrm logo">
            <h2>BookWyrm</h2>
        </section>

        <ul class="nav-links">
            <li><a href="AdminDash.php" ><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
                <li><a href="content.php"><i class="fas fa-book"></i> Manage Library</a></li>  
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> Manage Users</a></li>     
                <li><a href="viewLogs.php"><i class="fas fa-laptop"></i> View Logs</a></li>      
                <li class="dropdown">
                    <a href="#"><i class="fas fa-cog"></i> Settings</a>
                    <ul class="dropdown-content">
                        <li><a href="settings.php">Edit My Details</a></li>
                        <li><a href="changePassword.php">Change My Password</a></li>
                    </ul>
                </li>
        </ul>
        <div class="bottom-links">
            <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        
    </nav>

    <main>
        <section class="header" >
            <div>
                <h1>Add New User</h1>
            </div>

            <div class="user-info">
                <img src="../images/pochita.jpg" alt="Admin Profile Picture" class="profile-pic">
                <p>Hello, <?= $_SESSION['firstname'] ?>!</p>
            </div>
        </section>

        <form class="add-manga-form" action="add.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone">
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div>
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>

            <div>
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>

            <div>
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="Reader">Reader</option>
                    <option value="Admin">Admin</option>    <!-- Changed 'admin' to 'Admin' to match session check -->
                </select>
            </div>

            <input type="submit" value="Add User" class="add-btn" name="add-user">
        </form>
    </main>
</body>
</html>
