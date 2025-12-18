<?php

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    // Check if username exists
    function usernameExists($conn, $username) {
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    // Check if email exists
    function emailExists($conn, $email) {
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    //check if manga already exists.
    function mangaExists($conn, $title) {
        $stmt = $conn->prepare("SELECT title FROM books WHERE book_name = ?");
        $stmt->bind_param("s", $title);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Adding new users
    if (isset($_POST['add_user'])) {
        // Handle adding user
        $userData = [
            'username' => test_input($_POST['username']),
            'email' => test_input($_POST['email']),
            'password' => password_hash(test_input($_POST['password']), PASSWORD_DEFAULT),
            'first_name' => test_input($_POST['first_name']),
            'last_name' => test_input($_POST['last_name']),
            'role' => test_input($_POST['role'])
        ];
        addNewUser($conn, $userData);
    }

    // Adding new manga
    if (isset($_POST['add_manga'])) {
        // Handle adding manga
        $mangaData = [
            'title' => $_POST['title'],
            'genres' => $_POST['genres'],
            'status' => $_POST['status'],
            'description' => $_POST['description'],
            'rating' => 0, // Default rating
            'author' => $_POST['author'],
            'cover' => $_FILES['cover']['name']
        ];
        addNewManga($conn, $mangaData);
        // Create directory for manga chapters
        $mangaDir = 'Mangas/' . $mangaData['title'];
        if (!is_dir($mangaDir)) {
            mkdir($mangaDir, 0777, true);
        }
        $cover = $_FILES['cover'];
        $allowedTypes = ['image/jpeg', 'image/png'];
        $coverPath = 'images/Covers/' . basename($cover['name']);
        $uploadSuccess = move_uploaded_file($cover['tmp_name'], $coverPath);
        if (!$uploadSuccess) {
            header("Location: content.php?error=upload+error");
            exit();
        }
        header("Location: content.php?success=manga+added+successfully");
        exit();
    }

    if (isset($_POST['delete_review'])) {
        $review_id = $_POST['review_id'];
        $book_id = $_POST['book_id'];
        $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
        $stmt->bind_param("i", $review_id);
        if ($stmt->execute()) {
            header("Location: ../book.php?book_id=$book_id&success=review+deleted+successfully");
            exit();
        } else {
            header("Location: ../book.php?book_id=$book_id&error=failed+to+delete+review");
            exit();
        }
    }


    // Update Manga Details
    function updateManga($conn, $mangaData) {
        $sql = $conn->prepare("UPDATE books SET title = ?, genres = ?, description = ?, author = ? WHERE book_id = ?");
        $sql->bind_param(
            "ssssi",
            $mangaData['title'],
            $mangaData['genres'],
            $mangaData['description'],
            $mangaData['author'],
            $mangaData['book_id']
        );
        return $sql->execute();
    }

    // Add New User
    function addNewUser($conn, $userData) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, role, contact_number)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssss",
            $userData['username'],
            $userData['email'],
            $userData['password'],
            $userData['first_name'],
            $userData['last_name'],
            $userData['role'],
            $userData['contact_number'],
            //removed $userData['active'] -> Kyle
        );
        return $stmt->execute();
    }

    // Admin functions
    // Get Active users
    function getActiveUsers($conn) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE deleted = 0");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch all books
    function getAllBooks($conn) {
        $stmt = $conn->prepare("SELECT * FROM books");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch the top rated books
    function getTopBooks($conn) {
        $topBooks = $conn->prepare("SELECT title, author, rating
                                FROM books
                                ORDER BY rating DESC
                                LIMIT 5");
        $topBooks->execute();
        $topBooksResult = $topBooks->get_result();
        return $topBooksResult->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch the most trending books
    function getTrendingBooks($conn) {
        $topBooks = $conn->prepare("SELECT title, author, COUNT(*) AS add_count
                                FROM libraries l
                                JOIN books b ON l.book_id = b.book_id
                                GROUP BY l.book_id
                                ORDER BY add_count DESC
                                LIMIT 5");
        $topBooks->execute();
        $topBooksResult = $topBooks->get_result();
        $topBooksData = $topBooksResult->fetch_all(MYSQLI_ASSOC);
    }

    // Get the top book or set to 'N/A' if none found
    $topBook = $topBooksData[0] ?? 'N/A';

    // Create a new manga
    function addNewManga($conn, $mangaData) {
        $stmt = $conn->prepare("INSERT INTO books (title, genres, description, rating, author, cover)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssdis",
            $mangaData['title'],
            $mangaData['genres'],
            $mangaData['description'],
            $mangaData['rating'],
            $mangaData['author'],
            $mangaData['cover']
        );
        $stmt->execute();
        if ($stmt->error) {
            header("Location: content.php?error=database+error");
            exit();
        }
    }

    //total number of uploads
    function getTotalUploads($conn) {
        $recent = $conn->prepare("SELECT COUNT(book_id) AS total_uploads FROM books");
        $recent->execute();
        return $recent->get_result()->fetch_assoc();
    }


?>