
<?php
    session_start();
    if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';
    require 'AdminFunctions.php';

    //get book id from url
    $book_id = $_GET['book_id'] ?? null;

    if (!$book_id) {
        header("Location: content.php?error=Invalid+Manga+ID");
        exit();
    }

    // Delete manga from database
    $delete = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $delete->bind_param("i", $book_id);
    if ($delete->execute()) {
        header("Location: content.php?success=Manga+deleted+successfully");
    } else {
        header("Location: content.php?error=Error+deleting+manga");
    }
    exit();
?>