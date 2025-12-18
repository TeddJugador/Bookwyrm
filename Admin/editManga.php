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

    $retrieve = $conn->prepare("SELECT * FROM books WHERE book_id = ? ");
    $retrieve->bind_param("i",$book_id);
    $retrieve->execute();
    $result = $retrieve->get_result();
    if ($result->num_rows > 0) {
        $manga = $result->fetch_assoc();
    } else {
        header("Location: content.php?error=Manga+not+found");
        exit();
    }

    //get number of chapters
    $chapterCount = $conn->prepare("SELECT COUNT(*) as chapter_count FROM chapters WHERE book_id = ?");
    $chapterCount->bind_param("i", $book_id);
    $chapterCount->execute();
    $chapterResult = $chapterCount->get_result();
    $chapterData = $chapterResult->fetch_assoc();
    $totalChapters = $chapterData['chapter_count'];
?>

<?php //sends the edited info to the database
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate and sanitize inputs
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $book_id = (int)($_GET['book_id'] ?? 0);

        // Basic validation
        if (empty($title) || empty($author) || empty($genre) || empty($description) || empty($status) || !$book_id) {
            echo "<script>alert('Please fill in all fields correctly.');</script>";
        } else {
            // Update manga details in the database
            $update = $conn->prepare("UPDATE books SET title = ?, author = ?, genres = ?, description = ?, status = ? WHERE book_id = ?");
            if (!$update) {
                echo "<script>alert('Prepare failed: " . addslashes($conn->error) . "');</script>";
            } else {
                $update->bind_param("sssssi", $title, $author, $genre, $description, $status, $book_id);
                if ($update->execute()) {
                    header("Location: content.php?success=Manga+updated+successfully");
                    exit();
                } else {
                    echo "<script>alert('Error updating manga details: " . addslashes($update->error) . "');</script>";
                }
            }
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="../CSS/test.css">
        <link rel="stylesheet" href="../CSS/addManga.css">
    </head>
    <body>
        <nav class="nav-bar">

            <section class="nav-logo">
                <img src="../images/konLogo.png" alt="BookWyrm logo">
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
                </li>
            </ul>
            <div class="bottom-links">
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </div>
            </ul>
        </nav>

        <main>
            <section class="header" >
            <div>
                <h1>Edit Manga Details</h1>
            </div>

            <div class="user-info">
                <img src="../images/pochita.jpg" alt="Admin Profile Picture" class="profile-pic">
                <p>Hello, <?= $_SESSION['firstname'] ?>!</p>
            </div>
         </section>

         <!-- Edit Manga Form -->
        <form id="add-manga-form" class="add-manga-form" autocomplete="off" action="editManga.php?book_id=<?= $book_id ?>" method="POST">
            <div>
                <label for="manga-title">Title</label>
                <input type="text" id="manga-title" name="title" value="<?php echo htmlspecialchars($manga['title']); ?>">
            </div>
            <div>
                <label for="manga-author">Author</label>
                <input type="text" id="manga-author" name="author" value="<?php echo htmlspecialchars($manga['author']); ?>">
            </div>
            <div>
                <label for="manga-genre">Genre</label>
                <input type="text" id="manga-genre" name="genre" value="<?php echo htmlspecialchars($manga['genres']); ?>">
            </div>
            <div>
                <label for="manga-description">Description</label>
                <textarea id="manga-description" name="description"><?php echo htmlspecialchars($manga['description']); ?></textarea>
            </div>
            <div>
                <label for="manga-status">Status</label>
                <select id="manga-status" name="status">
                <option value="">Select Status</option>
                <option value="Ongoing" <?php echo ($manga['status'] === 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                <option value="Completed" <?php echo ($manga['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="Hiatus" <?php echo ($manga['status'] === 'Hiatus') ? 'selected' : ''; ?>>Hiatus</option>
                <option value="Dropped" <?php echo ($manga['status'] === 'Dropped') ? 'selected' : ''; ?>>Dropped</option>
                </select>
            </div>
            <div>
                <label for="manga-chapters">Chapters</label>
                <input type="text" id="manga-chapters" name="chapters" value="<?php echo htmlspecialchars($totalChapters); ?>" disabled>
            </div>
            <div>
                <label for="manga-cover">Cover Image</label>
                <input type="file" id="manga-cover" name="cover" accept="image/*">
            </div>
            <button type="submit" class="add-btn">Edit Manga</button>
            <a href="../book.php?id=<?= $book_id ?>" class="add-btn" id="view">View Manga</a>
        </form>

        </main>
    </body>
</html>

