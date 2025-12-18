
<?php
    session_start();
    if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';
    require 'AdminFunctions.php';

    //get user id 
    $user_id = $_GET['id'] ?? null;

    if (!$user_id) {
        header("Location: users.php?error=Invalid+User+ID");
        exit();
    }

    if ($user_id === $_SESSION['username']) {
        header("Location: users.php?error=Cannot+delete+your+own+account");
        exit();
    }

    // Delete user from database
    $delete = $conn->prepare("UPDATE users SET deleted = '1' WHERE username = ?");
    $delete->bind_param("s", $user_id);
    if ($delete->execute()) {
        header("Location: users.php?success=User+deleted+successfully");
    } else {
        header("Location: users.php?error=Error+deleting+user");
    }
    exit();
?>