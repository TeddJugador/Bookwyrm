<?php
    require_once 'includes/connection.php';
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: loginForm.php?error=login+to+submit+a+review");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $book_id = $_POST['book_id'];
        $username = $_SESSION['username'];
        $comment = $conn->real_escape_string($_POST['comment']);
        $rating = intval($_POST['rating-value']);

        // Validate rating
        if ($rating < 1 || $rating > 5) {
            header("Location: book.php?id=$book_id&error=invalid+rating");
            exit();
        }

        // Insert review into database
        $insertQuery = "INSERT INTO reviews (book_id, user, comment, rating, review_date) 
                        VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("issi", $book_id, $username, $comment, $rating);

        if ($stmt->execute()) {
            header("Location: book.php?id=$book_id&message=review+submitted+successfully");
            exit();
        } else {
            header("Location: book.php?id=$book_id&error=failed+to+submit+review");
            exit();
        }
        $stmt->close();
        $conn->close();
    } else {
        header("Location: home.php");
        exit();
    }

?>