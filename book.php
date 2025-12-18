<?php
    require_once 'includes/connection.php';
    
    session_start();    //this will need to be changed later

    include 'book_actions.php';
    if (!isset($_SESSION['username'])) {
        header("Location: loginForm.php?error=login+to+read+mangas&action=b");
        exit();
    }
    $book_id = $_GET['id'];
    $query = "SELECT * FROM books WHERE book_id = $book_id";
    $result = $conn->query($query);
    $book = $result->fetch_assoc();
    if (!$book) {
        echo "Book not found.";
        exit;
    }
    //get book details
    $title = $book['title'];
    $author = $book['author'];
    $description = $book['description'];
    $cover = $book['cover'];
    $status = $book['status'];
    $genres = explode(",", $book['genres']);
    $rating = $book['rating'];

    // Handle messages
    if (isset($_GET['error'])) {
        echo "<script>alert('" . htmlspecialchars($_GET['error']) . "');</script>";
    } elseif (isset($_GET['success'])) {
        echo "<script>alert('" . htmlspecialchars($_GET['success']) . "');</script>";
    }

    // Get average rating
    $rating_query = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE book_id = $book_id";
    $rating_result = $conn->query($rating_query);
    $avg_rating = $rating_result->fetch_assoc()['avg_rating'] ?? 'No ratings yet';
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookWyrm</title>
    <link rel="icon" href="images/konLogo.png">
    <link rel="stylesheet" href="CSS/book.css">
  </head>
  
  <body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-and-name">
                <div class="logo">
                    <img src="images/konLogo.png" alt="BookWyrm Logo" class="logo-img" id="navbar-logo" loading="lazy">
                </div>
                <div class="navbar-brand">BookWyrm Library</div>
            </div>
            
            <ul class="navbar-nav">
                <li><a href="home.php">Home</a></li>
                <li><a href="library.php">Library</a></li>
                <li><a href="User/Profile.php">Profile</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class = "main-content">
        <div id="main-container">
            <section class="book">
                <h1 class = "book-title"><?php echo $title; ?></h1>
                <h3>Mangaka: <?php echo $author; ?></h3>
                <!-- <h4 class="small-text" >Rating: <?php //number_format($avg_rating, 1) ?></h4> -->
                <h4 class="small-text" >Rating: <?php echo $rating ?></h4>
                <img class="cover-page" src ="images/Covers/<?php echo $cover; ?>" alt="<?php echo $title; ?> manga cover">
                <br>
                <?php
                  if (isBookInLibrary($conn, $_SESSION['username'], $book_id)) {
                      $current_chapter = getCurrentChapter($conn, $_SESSION['username'], $book_id);
                      $book_status = getBookStatus($conn, $_SESSION['username'], $book_id);
                      if ($book_status == 'read later') {
                        echo '<button class="book-button" onclick="location.href=\'book_actions.php?action=update&book_id=' . $book_id . '&status=reading\'">Start Reading</button>';
                      }
                      if ($book_status == 'reading') {
                        echo '<button class="book-button" onclick="location.href=\'book_actions.php?action=update&book_id=' . $book_id . '&status=read+later\'">Move to Read Later</button>';
                      }
                      if ($book_status == 'reading' && $current_chapter) {
                        echo '<button class="book-button" onclick="location.href=\'book_actions.php?action=update&book_id=' . $book_id . '&status=done\'">Mark as Read</button>';
                      }
                      if ($book_status == 'completed') {
                        echo '<button class="book-button" onclick="location.href=\'book_actions.php?action=restart&book_id=' . $book_id . '&status=reading\'">Read Again</button>';
                      }
                      echo '<button class="book-button" onclick="location.href=\'book_actions.php?action=remove&book_id=' . $book_id . '\'">Remove from Library</button>';
                      // echo '<button class="book-button" onclick="location.href=\'book_actions.php?book_id=' . $book_id . '&status=read\'">Mark as To Read</button>';
                  } else {
                      echo '<button class="book-button" onclick="location.href=\'book_actions.php?action=add&book_id=' . $book_id . '&status=reading\'">Add to Library</button>';
                      echo '<button class="book-button" onclick="location.href=\'book_actions.php?action=add&book_id=' . $book_id . '&status=read+later\'">Read Later</button>';
                  }
                ?>
                <!-- <button class="book-button">Read Now</button>
                <button class="book-button">Read Later</button> -->
            </section>
            <section class="description">
                <h1 class = "overview">Overview</h1>
                <h4 class="small-text" >Status: <?php echo $status; ?></h4>
                <h3>Genres:</h3>
                <ul id="genre-list">
                    <?php foreach ($genres as $genre): ?>
                        <li class="genre-label"><?php echo htmlspecialchars(trim($genre)); ?></li>
                    <?php endforeach; ?>
                </ul>
                <h4 class="small-text">Description</h4>
                <p><?php echo htmlspecialchars($description); ?></p>
                <br>
              <section class="chapter-section">
                <h3>Chapters</h3>
                    <?php 
                        if ($_SESSION['role'] == 'Admin') {
                            echo '<a href="#" class="">Add Chapter</a>';
                        }
                        $chapters = getChapters($conn, $book_id);
                        if ($chapters->num_rows > 0) {
                          // echo '<ul id="chapter-list">';  
                            while ($chapter = $chapters->fetch_assoc()) {
                              echo '<div class="chapter-item">';
                                echo "<a href=\"{$chapter['file_path']}\">Chapter " . htmlspecialchars($chapter['chapter_num']) . "</a>";
                                if (isBookInLibrary($conn, $_SESSION['username'], $book_id)) {
                                    $current_chapter = getCurrentChapter($conn, $_SESSION['username'], $book_id);
                                    $book_status = getBookStatus($conn, $_SESSION['username'], $book_id);
                                    if ($chapter['chapter_num'] == $current_chapter && $book_status == 'reading') {
                                        echo '<p>Current chapter</p>';
                                        echo '<button class="finish-button" onclick="location.href=\'book_actions.php?action=finish&book_id=' . $book_id . '\'">Finish Chapter</button>';
                                    } 
                                }
                              echo '</div>';
                            }
                          // echo '</ul>';
                        } else {
                            echo 'No chapters available.';
                        }
                    ?>
              </section>
            </section>
        </div>
    </div>
    
    <br><br>

    <section class="review-section">
      <h2 id = "review-title">Reviews</h2>
      <?php 
        // Fetch reviews from the database
        $review_query = "SELECT * FROM reviews WHERE book_id = $book_id";
        $review_result = $conn->query($review_query);
        if ($review_result->num_rows > 0) {
            while ($review = $review_result->fetch_assoc()) {
                echo '<section class="review">';
                echo '<h4>' . htmlspecialchars($review['user']) . ':</h4>';
                echo '<p class="rating-num">' . htmlspecialchars($review['rating']) . '/5 &#9733</p>';
                echo '<p>' . htmlspecialchars($review['comment']) . '</p>';
                if ($_SESSION['role'] == 'Admin') {
                    echo '<form method="POST" action="Adamin/AdminFunctions.php" onsubmit="return confirm(\'Are you sure you want to delete this review?\');">';
                    echo '<input type="hidden" name="review_id" value="' . $review['review_id'] . '">';
                    echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
                    echo '<button type="submit" name="delete_review" class="delete-button">Delete</button>';
                    echo '</form>';
                }
                echo '</section>';
            }
        } else {
            echo '<p>No reviews yet. Be the first to review this manga!</p>';
        }
      ?>


      <form id="review-form" method="POST" action="submit_review.php">
          <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
          <p class="small-text">Write your thoughts about this manga:</p>
          <textarea id = "review-text" rows="10" cols=100 name = "comment" required></textarea>
          <div class="ratings-wrapper">
            <p>Rate this Manga: </p>
            <input type="hidden" name="rating-value" id="rating-value" value="">
            <div class="ratings">
              <span id = "star-5" data-clicked='false' data-value="5">&#9733;</span>
              <span id = "star-4" data-clicked='false' data-value="4">&#9733;</span>
              <span id = "star-3" data-clicked='false' data-value="3">&#9733;</span>
              <span id = "star-2" data-clicked='false' data-value="2">&#9733;</span>
              <span id = "star-1" data-clicked='false' data-value="1">&#9733;</span>
            </div>
          </div>
          <input class ="post-button" type = "submit" value = "Post">
      </form>
    </section>
    <br><br>

    <!--Perhaps add this to the database-->
    <section id="glossary">
        <h2 class="glossary-title">Glossary</h2>
        <?php
            // Fetch glossary terms from the database
            $glossary_query = "SELECT * FROM glossary"; 
            $glossary_result = $conn->query($glossary_query);
            if ($glossary_result->num_rows > 0) {
                echo '<dl>';
                while ($term = $glossary_result->fetch_assoc()) {
                    echo '<dt>' . htmlspecialchars($term['term']) . ' -</dt>';
                    echo '<dd>' . htmlspecialchars($term['definition']) . '</dd>';
                }
                echo '</dl>';
            } else {
                echo '<p>No glossary terms available.</p>';
            }
        ?>
    </section>

     <!-- Footer -->
     <footer>
        <div class="container footer-content">
            <div class="footer-column">
                <div class="footer-logo-and-name">
                    <a href="#hero" class="logo">
                            <img src="images/konLogo.png" alt="Logo" class="footer-img">
                    </a>
                    <span class="footer-brand">BookWyrm Library</span>
                </div>
                <p style="margin-top: 10px; color: var(--neutral);">
                    The best place to find, read, and review your favorite manga series online.
                </p>
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <div class="footer-links">
                    <a href="home.php#hero">Home</a>
                    <a href="home.php#trending">Trending</a>
                    <a href="home.php#latest">Latest Releases</a>
                    <a href="home.php#contact">Contact Us</a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Legal</h3>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Disclaimer</a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Connect</h3>
                <div class="footer-links">
                    <a href="#">Twitter/X</a>
                    <a href="#">Instagram</a>
                    <a href="#">Discord</a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 BookWyrm Library. All rights reserved.</p>
        </div>
    </footer>
    <script src="JS/book.js"></script>
    <script src="JS/nav.js"></script>
    
  </body>
  
</html>