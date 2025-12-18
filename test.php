<?php

    include 'includes/connection.php';
    session_start();

    // Fetch user library data from the database
    $username = $_SESSION['username'];
    $current_query = "SELECT b.*, l.current_chapter, c.file_path 
                      FROM libraries l
                      JOIN books b ON l.book_id = b.book_id
                      JOIN chapters c ON b.book_id = c.book_id
                      WHERE username = '$username' AND c.chapter_num = l.current_chapter AND l.status = 'reading'";
    $read_later_query = "SELECT b.*, l.current_chapter
                         FROM libraries l
                         JOIN books b ON l.book_id = b.book_id
                       --   JOIN chapters c ON b.book_id = c.book_id
                         WHERE username = '$username' AND l.status = 'read later'";
    $reading_result = $conn->query($current_query);
    $read_later_result = $conn->query($read_later_query);
    // var_dump($read_later_result->fetch_assoc());

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="CSS/nav.css"> <!-- nav bar -->
    <style>
        :root{
            --primaryColor: #B03A2D;
            --secondaryColor: #D58936;
            --primaryGrey: #C1C6C9;
            --primaryBlack: #383232;
            --primaryWhite: #F0F3F5;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1, h2 {
            color: var(--primaryBlack);
        }
        .library-section {
            margin-top: 20px;
            display: flex;
            flex-direction: row;
            gap: 50px;
        }

        .books {
            display: flex;
            flex-direction: column;
            gap: 15px;
            background-color: var(--primaryGrey);
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            min-width: 300px;
            /* max-height: 500px; */
        }

        .library-item {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            width: 300px;
            /* max-height: 500px; */
        }
        .library-item h3 {
            margin: 0 0 10px 0;
        }
        .library-item p {
            margin: 5px 0;
        }
        .library-item a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: var(--secondaryColor);
        }
        .library-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    <h1>User Dashboard</h1>
    <p>Welcome to your dashboard!</p>
    <p>Here you can view your activity.</p>
    <section class="library-section">
        <div class="books">
        <h2>Currently Reading</h2>
            <?php
                if ($reading_result->num_rows > 0) {
                    while ($item = $reading_result->fetch_assoc()) {
                        $cover = "images/covers/{$item['cover']}";
                        $book_id = $item['book_id'];
                        $title = str_replace('/\s+/', '_', $item['title']);
                        $author = $item['author'];
                        $current_chapter = $item['current_chapter'];
                        $chapter_file = $item['file_path'];
                        echo "<div class='library-item'>";
                        echo "<img src='{$cover}' alt='{$title}' width='300' height='auto'>";
                        echo "<h3>" . htmlspecialchars($item['title']) . "</h3>";
                        echo "<h4>" . htmlspecialchars($item['author']) . "</h4>";
                        echo "<p>Currently on chapter " . htmlspecialchars($item['current_chapter']) . "</p>";
                        echo "<a href='book.php?id={$book_id}'>View Book</a><br>";
                        echo "<a href='{$chapter_file}'>Read</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Your library is empty.</p>";
                }
            ?>
        </div>
        <div class="books">
        <h2>Read Later</h2>
            <?php
                if ($read_later_result->num_rows > 0) {
                    while ($item = $read_later_result->fetch_assoc()) {
                        $cover = "images/covers/{$item['cover']}";
                        $book_id = $item['book_id'];
                        $title = str_replace('/\s+/', '_', $item['title']);
                        $author = $item['author'];
                        $current_chapter = $item['current_chapter'];
                        echo "<div class='library-item'>";
                        echo "<img src='{$cover}' alt='{$title}' width='300' height='auto'>";
                        echo "<h3>" . htmlspecialchars($item['title']) . "</h3>";
                        echo "<h4>" . htmlspecialchars($item['author']) . "</h4>";
                        echo "<a href='book.php?id={$book_id}'>View Book</a><br>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Your read later list is empty.</p>";
                }
            ?>
    </section>
</body>
</html>