<?php
    session_start();
    if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';

?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Add Manga</title>
        <link rel="stylesheet" href="../CSS/addManga.css">
        <link rel="stylesheet" href="../CSS/test.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <nav class="nav-bar">

            <section class="nav-logo">
                <img src="../images/konLogo.png" alt="BookWyrm logo">
                <h2>BookWyrm</h2>
            </section>

            <ul class="nav-links">
                 <li><a href="AdminDash.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
                    <li><a href="content.php" class="active"><i class="fas fa-book"></i> Manage Library</a></li>  
                    <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>      
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
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>

        <main>
         <section class="header" >
            <div>
                <h1>Add New Manga</h1>
            </div>
         </section>

         <form  action = "store_manga.php" method="POST" enctype="multipart/form-data" class="manga-form">
             <div>
                 <label for="title">Title</label>
                 <input type="text" id="title" name="title" required>
             </div>
             <div>
                 <label for="author">Author</label>
                 <input type="text" id="author" name="author" required>
             </div>
             <div>
                 <label for="genres">Genres (comma-separated)</label>
                 <input type="text" id="genres" name="genres">
             </div>
             <div>
                 <label for="description">Description</label>
                 <textarea id="description" name="description"></textarea>
             </div>
             <div>
                 <label for="manga-rating">Rating (0 to 5)</label>
                 <input type="number" id="manga-rating" name="manga-rating" min="0" max="5" step="0.1" value="0">
             </div>
            <div>
                <label for="manga-status">Status</label>
                <select id="manga-status" name="manga-status" required>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                    <option value="Hiatus">Hiatus</option>
                    <option value="Dropped">Dropped</option>
                </select>
            </div>
            <div>
                <label for="manga-cover">Cover Image</label>
                <input type="file" id="manga-cover" name="manga-cover" accept="image/*">
            </div>
            <input type ='submit' class ='add-btn' name = 'add_manga' value = 'Add Manga'>
    </body>

</html>