<?php
    session_start();
    if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';

    if (isset($_GET['error'])) {
        echo "<script>alert('" . htmlspecialchars($_GET['error']) . "');</script>";
    }

    if (isset($_GET['success'])) {
        echo "<script>alert('" . htmlspecialchars($_GET['success']) . "');</script>";
    }

?>

<?php //ssends the new manga info to the database
   if (isset($_POST['add_manga'])) {
       // Retrieve form inputs
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'];
        $genres = $_POST['genres'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['manga-status'];
        $rating = $_POST['manga-rating'];
        $coverName = ''; // default empty, will set if image uploaded

        //Store image
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

        //check if the manga already exists
        if(mangaExists($conn, $title)) {
            header("Location: addManga.php?error=Error+manga+already+exists");
        } else{
            $sql = "INSERT INTO books (title, author, genres, description, status, cover, rating) VALUES (?, ?, ?, ?, ?, ?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssd", $title, $author, $genres, $description, $status, $coverName,$rating);

            if ($stmt->execute()) {
                header("Location: content.php?success=Manga+added+successfully");
            } else {
                header("Location: content.php?error=Error+adding+manga");
            }
        }
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Add Manga</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="../CSS/test.css">
        <link rel="stylesheet" href="../CSS/addManga.css">
    </head>
    <body>
        <nav class="nav-bar">
            <section class="nav-logo">
                <img src="http://cs3-dev.ict.ru.ac.za/Practicals/4C2/CS3-Library/images/konLogo.png" alt="BookWyrm logo">
                <h2>BookWyrm</h2>
            </section>

            <ul class="nav-links">
                <li><a href="AdminDash.php" ><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
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

            <div class="user-info">
                <img src="../images/pochita.jpg" alt="Admin Profile Picture" class="profile-pic">
                <p>Hello, <?= $_SESSION['firstname'] ?>!</p>
            </div>
         </section>

         <!-- Add Manga Form -->
        <form class="add-manga-form"  method="POST" action="addManga.php" enctype="multipart/form-data">
            <div>
                <label for="manga-title">Title</label>
                <input type="text" id="manga-title" name="title" required>
            </div>
            <div>
                <label for="manga-author">Author</label>
                <input type="text" id="manga-author" name="author" required>
            </div>
            <div>
                <label for="manga-genre">Genres</label>
                <input type="text" id="manga-genre" name="genres" required>
            </div>
            <div>
                <label for="manga-rating">Rating</label>
                <input type="number" id="manga-rating" name="manga-rating" min="0" step="0.1" max="5" required>
            </div>
            <div>
                <label for="manga-description">Description</label>
                <textarea id="manga-description" name="description"  required></textarea>
            </div>
            <div>
                <label for="manga-status">Status</label>
                <select id="manga-status" name="manga-status" required>
                    <option value="">Select Status</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                    <option value="Hiatus">Hiatus</option>
                    <option value="Dropped">Dropped</option>
                </select>
            </div>
            <div>
                <label for="manga-cover">Cover Image</label>
                <input type="file" id="manga-cover" name="manga-cover">
            </div>
            <input type ='submit' class ='add-btn' name = 'add_manga' value = 'Add Manga'>
            <!-- <button type="submit" class="add-btn" name="add_manga">Add Manga</button> -->
        </form>

        </main>
    </body>
</html>

