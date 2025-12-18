<?php

    // Function to generate a unique ID for authors
    function generateID($firstName, $lastName) {
        return strtolower(substr($firstName, 0, 1) . $lastName . rand(100, 999));
    }

    require 'connection.php';
    session_start();

    // Add a book to a library
    if (isset($_GET['add'])) {
        $stmt = "SELECT * FROM libraries WHERE book_id = $book AND user_id = $user";
        $result = $conn->query($stmt);
        if ($result->num_rows > 0) {
            echo "<script>alert('This book is already in your library');</script>";
            exit();
        }
        switch ($_GET['add']) {
            case 'now':
                $stmt = $conn->prepare("INSERT INTO libraries VALUES (?,?,?,?)");
                $stmt->bind_param("sssi", $book, $user, 'Ongoing', '1');
                $stmt->execute();
                break;
            case 'later':
                $stmt = $conn->prepare("INSERT INTO libraries VALUES (?,?,?,?)");
                $stmt->bind_param("sssi", $book, $user, 'Read Later', '1');
                $stmt->execute();
                break;
        }
    }

    // Admin Stuff
    // 

    // Add a new manga
    // Check if the form is submitted
    if (isset($_POST['add_manga'])) {
        $title = $_POST['title'];
        $author = explode(" ", $_POST['author']);
        $description = $_POST['description'];
        $cover = $_FILES['cover'];
        $genres = $_POST['genres'];
        $rating = 0; // Default rating

        // Validate inputs
        if (empty($title) || empty($author) || empty($description)) {
            die("All fields are required.");
        }

        $allowedTypes = ['image/jpeg', 'image/png'];
        if (in_array($cover['type'], $allowedTypes) === false) {
            die("Only JPG and PNG files are allowed for the cover image.");
        }
        $coverPath = 'covers/' . basename($cover['name']);
        if (move_uploaded_file($cover['tmp_name'], $coverPath) === false) {
            die("Failed to upload cover image.");
        }

        $firstName = $author[0];
        $lastName = $author[1] ?? '';

        $getAuthor = $conn->prepare("SELECT * FROM authors WHERE first_name = ? AND last_name = ?");
        $getAuthor->bind_param("ss", $firstName, $lastName);
        $getAuthor->execute();
        $authorResult = $getAuthor->get_result();

        // Check if author exists
        if ($authorResult->num_rows > 0) {
            $authorData = $authorResult->fetch_assoc();
            $authorId = $authorData['author_id'];
        } else {
            // Check if id exists
            do {
                $authorId = generateID($firstName, $lastName);
                $checkId = $conn->prepare("SELECT * FROM authors WHERE author_id = ?");
                $checkId->bind_param("s", $authorId);
                $checkId->execute();
                $idResult = $checkId->get_result();
            } while ($idResult->num_rows > 0);
        }

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO books (title, genres, description, rating, author_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $title, $genres, $description, $rating, $authorId);

        // Execute the statement
        if ($stmt->execute()) {
            echo "New manga added successfully.";
        } else {
            echo "Database error: " . $conn->error;
        }

        $stmt->close();
    }

    // Fetch number of users
    $userCount = $conn->prepare("SELECT COUNT(*) FROM users");
    $userCount->execute();
    $userCountResult = $userCount->get_result();
    $userCountData = $userCountResult->fetch_assoc();

    // Fetch the most trending books
    $topBooks = $conn->prepare("SELECT title, author, COUNT(*) AS add_count
                               FROM library l
                               JOIN books b ON l.book_id = b.book_id
                               GROUP BY l.book_id
                               ORDER BY add_count DESC
                               LIMIT 5");
    $topBooks->execute();
    $topBooksResult = $topBooks->get_result();
    $topBooksData = $topBooksResult->fetch_all(MYSQLI_ASSOC);

    // Get the top book or set to 'N/A' if none found
    $topBook = $topBooksData[0] ?? 'N/A';


    // Fetch the most active users
    $activeUsers = $conn->prepare("SELECT username, COUNT(*) AS add_count
                                  FROM libraries
                                  GROUP BY username
                                  ORDER BY add_count DESC
                                  LIMIT 5");
    $activeUsers->execute();
    $activeUsersResult = $activeUsers->get_result();
    $activeUsersData = $activeUsersResult->fetch_all(MYSQLI_ASSOC);

    // Fetch author's books
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $authorBooks = $conn->prepare("SELECT book_id, title, author
                                      FROM books b
                                      JOIN users u ON b.author = u.username
                                      WHERE u.username = ?
                                      ORDER BY b.title DESC");
        $authorBooks->bind_param("s", $username);
        $authorBooks->execute();
        $authorBooksResult = $authorBooks->get_result();
        $authorBooksData = $authorBooksResult->fetch_all(MYSQLI_ASSOC);
    } else {
        $authorBooksData = [];
    }

    // Upload a chpapter as pdf
    if (isset($_POST['upload'])) {
        $title = $_POST['title'];
        $bookId = $_POST['book_id'];
        $chapterNumber = $_POST['chapter_number'];
        $pdfFile = $_FILES['pdf_file'];

        // Validate inputs
        if (empty($title) || empty($bookId) || empty($chapterNumber) || $pdfFile['error'] !== UPLOAD_ERR_OK) {
            die("Invalid input.");
        }

        // Validate file type (basic check)
        $allowedTypes = ['application/pdf'];
        if (!in_array($pdfFile['type'], $allowedTypes)) {
            die("Only PDF files are allowed.");
        }

        // Move uploaded file to a designated directory
        $uploadDir = "uploads/$title/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filePath = $uploadDir . basename($pdfFile['name']);
        if (move_uploaded_file($pdfFile['tmp_name'], $filePath)) {
            // Insert chapter record into database
            $insertChapter = $conn->prepare("INSERT INTO chapters (book_id, chapter_number, file_path)
                                            VALUES (?, ?, ?)");
            $insertChapter->bind_param("iis", $bookId, $chapterNumber, $filePath);
            if ($insertChapter->execute()) {
                echo "Chapter uploaded successfully.";
            } else {
                echo "Database error: " . $conn->error;
            }
        } else {
            echo "Failed to upload file.";
        }
    }

    // 
    // Reader Stuff
    // 
    // Fetch user's read later books
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $userBooks = $conn->prepare("SELECT b.title, b.author
                                    FROM library l
                                    JOIN books b ON l.book_id = b.book_id
                                    WHERE l.username = ? AND l.status = 'read later'
                                    ORDER BY l.added_at DESC");
        $userBooks->bind_param("s", $username);
        $userBooks->execute();
        $userBooksResult = $userBooks->get_result();
        $userBooksData = $userBooksResult->fetch_all(MYSQLI_ASSOC);
    } else {
        $userBooksData = [];
    }

    // Fetch user's currently reading books
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $currentBooks = $conn->prepare("SELECT b.title, b.author, l.current_chapter
                                       FROM library l
                                       JOIN books b ON l.book_id = b.book_id
                                       WHERE l.username = ? AND l.status = 'currently reading'
                                       ORDER BY l.added_at DESC");
        $currentBooks->bind_param("s", $username);
        $currentBooks->execute();
        $currentBooksResult = $currentBooks->get_result();
        $currentBooksData = $currentBooksResult->fetch_all(MYSQLI_ASSOC);
    } else {
        $currentBooksData = [];
    }

    // Fetch user's finished books
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $finishedBooks = $conn->prepare("SELECT b.title, b.author
                                        FROM library l
                                        JOIN books b ON l.book_id = b.book_id
                                        WHERE l.username = ? AND l.status = 'finished'
                                        ORDER BY l.finished_at DESC");
        $finishedBooks->bind_param("s", $username);
        $finishedBooks->execute();
        $finishedBooksResult = $finishedBooks->get_result();
        $finishedBooksData = $finishedBooksResult->fetch_all(MYSQLI_ASSOC);
    } else {
        $finishedBooksData = [];
    }

    // Finishing a chapter
    if (isset($_POST['finish_chapter'])) {
        $bookId = $_POST['book_id'];
        $chapterNumber = $_POST['chapter_number'];
        $username = $_SESSION['username'];

        // Update the current chapter in the library
        $updateChapter = $conn->prepare("UPDATE library
                                         SET current_chapter = ?
                                         WHERE username = ? AND book_id = ?");
        $updateChapter->bind_param("isi", $chapterNumber, $username, $bookId);
        if ($updateChapter->execute()) {
            echo "Chapter updated successfully.";
        } else {
            echo "Database error: " . $conn->error;
        }
    }


    $conn->close();
?>