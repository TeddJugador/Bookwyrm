<?php
    session_start();
    if ($_SESSION['role'] !== 'Reader') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';


    // Fetch and display success or error messages
    $success = $_GET['success'] ?? '';
    $error = $_GET['error'] ?? '';
    if ($success) {
        echo "<script>alert('Success: " . htmlspecialchars($success) . "');</script>";
    }
    if ($error) {
        echo "<script>alert('Error: " . htmlspecialchars($error) . "');</script>";
    }



    //handle the form
    
?>


