<?php
    session_start();
    if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../login.php");
        exit();
    }

    require '../includes/connection.php';
    require 'AdminFunctions.php';

    // Display success or error messages
    $success = $_GET['success'] ?? null;
    $error = $_GET['error'] ?? null;
    if ($success) {
        echo "<script>alert('Success: " . htmlspecialchars($success) . "');</script>";
    }
    if ($error) {
        echo "<script>alert('Error: " . htmlspecialchars($error) . "');</script>";
    }

    
    // Fetch admin details
    $admin = $_SESSION['username'];
    $sql = $conn->prepare("SELECT password, username FROM users WHERE username = ?");
    $sql->bind_param("s", $admin);
    $sql->execute();
    $result = $sql->get_result();
    $admin = $result->fetch_assoc();

    $setPassword = $admin['password'];  //hashed password from DB

    if (isset($_POST['current-password'], $_POST['new-password'], $_POST['confirm-password'], $_POST['change-password'])) {
        $current_password = $_POST['current-password'];
        $new_password = $_POST['new-password'];
        $confirm_password = $_POST['confirm-password'];

        // Verify current password
        if (!password_verify($current_password, $setPassword)) {
            header("Location: changePassword.php?error=current+password+is+incorrect");
            exit();
        }

        // Check if new password matches and meets criteria
        if ($new_password !== $confirm_password) {
            header("Location: changePassword.php?error=new+passwords+do+not+match");
            exit();
        } elseif (strlen($new_password) < 8) {
            header("Location: changePassword.php?error=new+password+must+be+at+least+8+characters");
            exit();
        } elseif ($new_password === $current_password) {
            header("Location: changePassword.php?error=new+password+cannot+be+the+same+as+the+current+password");
            exit();
        } elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password) || !preg_match('/[\W]/', $new_password)) {
            header("Location: changePassword.php?error=new+password+must+include+uppercase,+lowercase,+number,+and+special+character");
            exit();
        }

        // Update password in the database
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $new_password, $admin['username']);
        
        if ($stmt->execute()) {
            header("Location: changePassword.php?success=password+changed+successfully");
            exit();
        } else {
            header("Location: changePassword.php?error=failed+to+change+password");
            exit();
        }
    }
 ?>   

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/konLogo.png">
    <link rel="stylesheet" href="../CSS/test.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Settings</title>
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
                <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>  
                <li><a href="viewLogs.php"><i class="fas fa-laptop"></i> View Logs</a></li>         
                <li class="dropdown">
                    <a href="" class="active"><i class="fas fa-cog"></i> Settings</a>
                    <ul class="dropdown-content">
                        <li><a href="settings.php">Edit My Details</a></li>
                        <li><a href="changePassword.php">Change My Password</a></li>
                    </ul>
                </li>
        </ul>
        <div class="bottom-links">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <main>
         <section class="header" >
            <div>
                <h1>Change My Password</h1>
            </div>

            <div class="user-info">
                <img src="../images/pochita.jpg" alt="Admin Profile Picture" class="profile-pic">
                <p>Hello, Kyle!</p>
            </div>
         </section>

         <section class="change-password">
            <form id="add-manga-form" class="add-manga-form" autocomplete="off" method="POST" action="changePassword.php">
                <div>
                    <label for="current-password">Current Password</label>
                    <input type="password" id="current-password" name="current-password" required>
                </div>
                <div>
                    <label for="new-password">New Password</label>
                    <input type="password" id="new-password" name="new-password" required>
                </div>
                <div>
                    <label for="confirm-password">Confirm New Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
                <button type="submit" class="add-btn" name="change-password">Change Password</button>
            </form>
         </section>
    </main>

</body>
</html>