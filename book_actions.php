<?php

    // Include the database connection
    require_once 'includes/connection.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $action = $_GET['action'] ?? null;
    $book_id = $_GET['book_id'] ?? null;
    $status = $_GET['status'] ?? null;

    // If user is adding to library
    if ($action === 'add' && $book_id && isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        if (isBookInLibrary($conn, $username, $book_id)) {
            header("Location: book.php?id=$book_id&error=Book+already+in+library");
            exit();
        }
        if (addBookToLibrary($conn, $username, $book_id, htmlspecialchars($status))) {
            header("Location: book.php?id=$book_id&success=Book+added+to+library");
            exit();
        } else {
            header("Location: book.php?id=$book_id&error=Failed+to+add+book");
            exit();
        }
    }

    // If user is removing from library
    if ($action === 'remove' && $book_id && isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        if (!isBookInLibrary($conn, $username, $book_id)) {
            header("Location: book.php?id=$book_id&error=Book+not+in+library");
            exit();
        }
        if (removeBookFromLibrary($conn, $username, $book_id)) {
            header("Location: book.php?id=$book_id&success=Book+removed+from+library");
            exit();
        } else {
            header("Location: book.php?id=$book_id&error=Failed+to+remove+book");
            exit();
        }
    }

    // If user is updating book status
    if ($action === 'update' && $book_id && $status && isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        if (updateBookStatus($conn, $username, $book_id, htmlspecialchars($status))) {
            header("Location: book.php?id=$book_id");
            exit();
        } else {
            header("Location: book.php?id=$book_id&error=Failed+to+update+status");
            exit();
        }
    }

    // If user is finishing a chapter
    if ($action === 'finish' && $book_id && isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        if (finishChapter($conn, $username, $book_id)) {
            header("Location: book.php?id=$book_id");
            exit();
        } else {
            header("Location: book.php?id=$book_id&error=Failed+to+finish+chapter");
            exit();
        }
    }

    // If user is restarting a book
    if ($action === 'restart' && $book_id && isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        if (restartBook($conn, $username, $book_id)) {
            header("Location: book.php?id=$book_id");
            exit();
        } else {
            header("Location: book.php?id=$book_id&error=Failed+to+restart+book");
            exit();
        }
    }


    // Functions for book actions

    // Check if a book is in the user's library
    function isBookInLibrary($conn, $username, $book_id) {
        $stmt = $conn->prepare("SELECT * FROM libraries WHERE username = ? AND book_id = ?");
        $stmt->bind_param("si", $username, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Add a book to the user's library
    function addBookToLibrary($conn, $username, $book_id, $status) {
        $stmt = $conn->prepare("INSERT INTO libraries (username, book_id, status, current_chapter) VALUES (?, ?, ?, ?)");
        // $status = 'reading'; // default status
        if ($status === 'read later') {
            $current_chapter = 0;
        } else {
            $current_chapter = 1; // default chapter
        }
        $stmt->bind_param("sssi", $username, $book_id, $status, $current_chapter);
        return $stmt->execute();
    }

    // Remove a book from the user's library
    function removeBookFromLibrary($conn, $username, $book_id) {
        $stmt = $conn->prepare("DELETE FROM libraries WHERE username = ? AND book_id = ?");
        $stmt->bind_param("si", $username, $book_id);
        return $stmt->execute();
    }

    // Get the user's current chapter for a book
    function getCurrentChapter($conn, $username, $book_id) {
        $stmt = $conn->prepare("SELECT current_chapter FROM libraries WHERE username = ? AND book_id = ?");
        $stmt->bind_param("si", $username, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null; // Book not found in library
        }
        $row = $result->fetch_assoc();
        return $row['current_chapter'];
    }

    // a book's status in the user's library
    function getBookStatus($conn, $username, $book_id) {
        $stmt = $conn->prepare("SELECT status FROM libraries WHERE username = ? AND book_id = ?");
        $stmt->bind_param("si", $username, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null; // Book not found in library
        }
        $row = $result->fetch_assoc();
        return $row['status'];
    }

    // Update a book's status in the user's library
    function updateBookStatus($conn, $username, $book_id, $new_status) {
        $stmt = $conn->prepare("UPDATE libraries SET status = ? WHERE username = ? AND book_id = ?");
        $stmt->bind_param("ssi", $new_status, $username, $book_id);
        return $stmt->execute();
    }

    function restartBook($conn, $username, $book_id) {
        // Fetch current status
        $stmt = $conn->prepare("SELECT status FROM libraries WHERE username = ? AND book_id = ?");
        $stmt->bind_param("si", $username, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return false; // Book not found in library
        }
        $row = $result->fetch_assoc();
        if ($row['status'] === 'completed') {
            // Update status to 'reading' and current_chapter to 1
            $update_stmt = $conn->prepare("UPDATE libraries SET status = 'reading', current_chapter = 1 WHERE username = ? AND book_id = ?");
            $update_stmt->bind_param("si", $username, $book_id);
            return $update_stmt->execute();
        }
        return false; // Status was not 'complete'
    }

    function finishChapter($conn, $username, $book_id) {
        // Get current chapter
        $stmt = $conn->prepare("SELECT current_chapter FROM libraries WHERE username = ? AND book_id = ?");
        $stmt->bind_param("si", $username, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return false; // Book not found in library
        }
        $row = $result->fetch_assoc();
        $current_chapter = $row['current_chapter'];
    
        // Get total chapters for the book
        $stmt = $conn->prepare("SELECT COUNT(*) AS total_chapters FROM chapters WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $chapters_result = $stmt->get_result();
        $chapters = $chapters_result->fetch_assoc();
        $total_chapters = $chapters['total_chapters'];
    
        if ($current_chapter >= $total_chapters) {
            // Mark book as complete
            $update_stmt = $conn->prepare("UPDATE libraries SET status = 'completed' WHERE username = ? AND book_id = ?");
            $update_stmt->bind_param("si", $username, $book_id);
            $update_stmt->execute();
            return true;
        }
    
        // Increment chapter
        $new_chapter = $current_chapter + 1;
        $update_stmt = $conn->prepare("UPDATE libraries SET current_chapter = ? WHERE username = ? AND book_id = ?");
        $update_stmt->bind_param("isi", $new_chapter, $username, $book_id);
        return $update_stmt->execute();
    }

    // Get chapters for a book
    function getChapters($conn, $book_id) {
        $stmt = $conn->prepare("SELECT * FROM chapters WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        return $stmt->get_result();
    }

?>