<?php
session_start();
if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../loginForm.php");
    exit();
}
require '../includes/connection.php';

//get the manga info from the form
$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$genres = $_POST['genres'] ?? '';
$description = $_POST['description'] ?? '';
$status = $_POST['manga-status'] ?? '';
$rating = $_POST['manga-rating'] ?? 0;
$coverName = ''; // default empty, will set if image uploaded

//deal with the image upload
if (isset($_FILES['manga-cover']) && $_FILES['manga-cover']['error'] == 0) {
    $cover = $_FILES['manga-cover']['tmp_name'];
    $uploadDir = '../images/Covers/';
    $coverName = basename($_FILES['manga-cover']['name']);
    $targetFile = $uploadDir.$coverName;
    if (!move_uploaded_file($cover, $targetFile)) {
        echo "<script>alert('Error uploading cover image. Please try again.');</script>";
    }
    else{
        echo "<script>alert('Cover image uploaded successfully.');</script>";
    }
}
else{
    echo "<script>alert('No cover image uploaded or there was an upload error.');</script>";
}
?>