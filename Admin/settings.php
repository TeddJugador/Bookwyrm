<?php 
    session_start();
    if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }

    include '../includes/connection.php';
    include 'AdminFunctions.php';

    if (isset($_GET['success'])) {
        $success = $_GET['success'];
        echo "<script>alert('Success: " . htmlspecialchars($success) . "');</script>";
    }
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        echo "<script>alert('Error: " . htmlspecialchars($error) . "');</script>";
    }

    $admin = $_SESSION['username'];

    // Fetch admin details
    $sql = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $sql->bind_param("s", $admin);
    $sql->execute();
    $result = $sql->get_result();
    $admin = $result->fetch_assoc();

    if (isset($_POST['first-name'], $_POST['last-name'], $_POST['username'], $_POST['email'], $_POST['contact-number'], $_POST['update-admin'])) {
        $first_name = $_POST['first-name'];
        $last_name = $_POST['last-name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact-number'];

        // Validate and sanitize input
        $first_name = filter_var($first_name, FILTER_SANITIZE_STRING);
        $last_name = filter_var($last_name, FILTER_SANITIZE_STRING);
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $contact_number = filter_var($contact_number, FILTER_SANITIZE_STRING);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {   // Validate email format
            header("Location: settings.php?error=invalid+email+format");
            exit();
        } elseif (!preg_match('/^[0-9+\s-]+$/', $contact_number)) { // Validate contact number format
            header("Location: settings.php?error=invalid+contact+number+format");
            exit();
        } elseif ($username !== $admin['username'] && usernameExists($conn, $username)) {   // Check if username is taken
            header("Location: settings.php?error=username+already+taken");
            exit();
        } elseif ($email !== $admin['email'] && emailExists($conn, $email)) {   // Check if email is taken
            header("Location: settings.php?error=email+already+taken");
            exit();
        }

        // Update admin details
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, contact_number = ? WHERE username = ?");
        $stmt->bind_param("ssssss", $first_name, $last_name, $username, $email, $contact_number, $admin['username']);
        
        if ($stmt->execute()) {
            // Update session username if changed
            $_SESSION['username'] = $username;
            header("Location: users.php?success=details+updated+successfully");
            exit();
        } else {
            header("Location: settings.php?error=failed+to+update+details");
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
            <button id="color-toggle"><img src="../images/konLogo.png" alt="BookWyrm logo"></button>
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
                <h1>Edit My Details</h1>
            </div>

            <div class="user-info">
                <img src="../images/pochita.jpg" alt="Admin Profile Picture" class="profile-pic">
                <p>Hello, Kyle!</p>
            </div>
         </section>



        <form id="add-manga-form" class="add-manga-form" autocomplete="off" method="POST" action="settings.php">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $admin['username']; ?>" disabled>
            </div>
            <div>
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first-name" value="<?php echo $admin['first_name']; ?>" required>
            </div>
            <div>
                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last-name" value="<?php echo $admin['last_name']; ?>" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
            </div>
            <div>
                <label for="contact-number">Contact Number</label>
                <input type="text" id="contact-number" name="contact-number" value="<?php echo $admin['contact_number']; ?>" required>
            </div>
            <button type="submit" class="add-btn" name="update-admin">Update Admin</button>
        </form>
    </main>

</body>
</html>