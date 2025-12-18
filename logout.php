<?php
    require_once("includes/connection.php");
    session_start();
    $log = $_SESSION['log_id'];
    $update = "UPDATE userlogs SET session_end = now() WHERE log_id = $log";
    $conn->query($update);

    session_unset();
    session_destroy();

    header("Location: home.php");
    exit;
    $conn->close();

?>