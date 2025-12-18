<?php
    require '../includes/connection.php';
    session_start();
    $book_id = $_GET['id'];
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
            </ul>
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
        <form class="add-manga-form"  method="POST" action="addChapter.php?id=<?= $book_id ?>" enctype="multipart/form-data">
            <div>
                <label for="manga-title">Title</label>
                <input type="text" id="manga-title" name="title" required>
            </div>
            <div>
                <label for="manga-author">Chapter Number</label>
                <input type="number" id="manga-author" name="chapter" required>
            </div>
            <input type ='submit' class ='add-btn' name = 'add_chapter' value = 'Add Chapter'>
        </form>

        </main>
    </body>
</html>

<?php
    $book_id = $_GET['id'];

    if (isset($_POST['add_chapter'])) {
        // Check if book exists
        $checkBook = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
        $checkBook->bind_param("i", $book_id);
        $checkBook->execute();
        $bookResult = $checkBook->get_result();
        if ($bookResult->num_rows === 0) {
            header("Location: content.php?error=book+not+found");
            exit();
        }
        $book = $bookResult->fetch_assoc();
        $mangaDir = 'Chapters/' . $book['title'];
        if (!is_dir($mangaDir)) {
            mkdir($mangaDir, 0777, true);
        }
        $chapter_num = $_POST['chapter'];
        $file_path = "Chapter/{$book['title']}/{$book['title']}_Chapter_{$chapter_num}";
        $stmt = $conn->prepare("INSERT INTO chapters (book_id, chapter_num, file_path)
                               VALUES (?, ?, ?)");
        $stmt->bind_param(
            "iis",
            $book_id,
            $chapter_num,
            $file_path
        );
        if ($stmt->execute()) {
            echo "<script>alert('Chapter added successfully')</script>";
        }
    }
?>