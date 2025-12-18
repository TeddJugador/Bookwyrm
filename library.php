<?php
require "includes/connection.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookWrym Library Catalog</title>
    <link rel="icon" href="images/konLogo.png">
    <link rel="stylesheet" href="CSS/nav.css">
    <link rel="stylesheet" href="CSS/lib.css">
</head>
<body>

<!-- Navigation -->
<?php 
    if(session_status() === PHP_SESSION_NONE) session_start();

        ?>
        <nav class="navbar" id="main-navbar">
    <div class="nav-container">
        <div class="logo-and-name">
            <a href="#hero" class="logo">
                <img src="images/konLogo.png" alt="BookWyrm Logo" class="logo-img" id="navbar-logo" loading="lazy">
            </a>
            <span class="navbar-brand">BookWyrm Library</span>
        </div>

        <!-- Navigation Links -->
        <ul class="navbar-nav" id="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="library.php">Library</a></li>
            <li><a href="aboutus.php">About Us</a></li>
            <li><a href="home.php#contact">Contact Us</a></li>
            <!-- Login link duplicated here for mobile convenience -->
            <li class="mobile-only-link"><a href="#" class="btn btn-outline" style="border:none; background: var(--secondary-color); color: var(--primary-color);">Login</a></li>
        </ul>

        <!-- Auth Buttons (Desktop Only) -->
         <?php
            if (isset($_SESSION['username'])) {
                echo '<div class="auth-buttons desktop-only">
                        <a href="User/Post-Login.php" class="btn btn-outline">Dashboard</a>
                      </div>';
            }
            else{
                echo '<div class="auth-buttons desktop-only">
                        <a href="redirect.php?action=l" class="btn btn-outline">Login</a>
                      </div>';
            }
         ?>
        
        <!-- Hamburger Button -->
        <div class="burger" id="burger-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>


<main>
    <!-- Recommended Section -->
    <?php
        // Fetch top-rated or popular books
        $recommendedQuery = "SELECT * FROM books ORDER BY rating DESC LIMIT 5";
        // Fetch top-rated or popular books
        
        
        $recommendedResult = $conn->query($recommendedQuery);

    ?>

    <?php if ($recommendedResult && $recommendedResult->num_rows > 0): ?>
    <section class="section recommended">
        <div class="container">
            <div class="section-title">
                <h2>Recommended Manga</h2>
            </div>

            <div class="recommended-slider" id="recommended-slider-container">
                <?php while ($manga = $recommendedResult->fetch_assoc()): ?>
                    <?php 
                        $coverPath = 'images/Covers/' . $manga['cover'];
                        if (empty($manga['cover']) || !file_exists($coverPath)) {
                            $coverPath = 'images/Covers/default-cover.jpeg';
                        }
                    ?>
                    <div class="recommended-slide" style="background-image: url('<?= $coverPath ?>');">
                        <div class="slide-content">
                            <h3><?= htmlspecialchars($manga['title']) ?></h3>
                            <p><?= htmlspecialchars(substr($manga['description'], 0, 200)) ?>...</p>
                            <a href="book.php?id=<?= $manga['book_id'] ?>" class="btn">Read Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>

                <div class="slider-controls">
                    <div class="slider-btn recommended-prev" data-target="recommended-slider-container" style="left: 10px;">❮</div>
                    <div class="slider-btn recommended-next" data-target="recommended-slider-container" style="right: 10px;">❯</div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

   <!--Search and Filter Section-->
   <section id="search-container">
        <form method="GET" class="search-form">
            <input type="text" id = "manga-search" name="search" placeholder="Search by title or author" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <div id="genre-filter-container">
                    <?php 
                        //get the genres from the database
                        $genreQuery = "SELECT DISTINCT genre FROM (SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(genres, ',', numbers.n), ',', -1)) AS genre
                                       FROM books
                                       JOIN (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 
                                             UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers
                                       ON CHAR_LENGTH(genres) - CHAR_LENGTH(REPLACE(genres, ',', '')) >= numbers.n - 1
                                      ) AS genre_list
                        WHERE genre <> '' ORDER BY genre ASC";
                        
                        $genreResult = $conn->query($genreQuery);
                        if ($genreResult->num_rows > 0) {
                            while ($row = $genreResult->fetch_assoc()) {
                                $genre = $row['genre'];
                                $selectedGenres = isset($_GET['genres']) && is_array($_GET['genres']) ? $_GET['genres'] : [];
                                $isChecked = in_array($genre, $selectedGenres, true) ? 'checked' : '';
                                echo '<button type = "button" id="'.htmlspecialchars($genre).'-btn" action="updateButton()" class="genre-btn '.($isChecked ? 'genre-btn-active' : '').'">
                                        <input type="checkbox" name="genres[]" value="'.htmlspecialchars($genre).'" '.$isChecked.'>
                                        '.htmlspecialchars($genre).'
                                      </button>';

                            }
                        } else {
                            echo "No genres found.";
                        }
                    ?>
            </div>

            <div id="controls-row">
                <button class="apply-filters-btn" type="submit" name = "submit">Apply Filters</button>
                <button class = "clear-filters-btn" type="submit" name = "reset" id="reset-btn">Clear Filters</button>
                <!-- <input class="apply-filters-btn" type="submit" name ="submit" value="Apply Filters">
                <input class="clear-filters-btn" type="submit" name ="reset" id="reset-btn" value="Clear Filters"> -->
            </div>
            
        </form>

   </section>
    

    <!-- Manga Grid -->
    <section class="container">
        <div id="manga-grid">
            <?php 
                //Base SQL query
                $paginationSql = "SELECT COUNT(*) AS total FROM books WHERE book_id = book_id "; // For counting total records
                $sql = "SELECT * FROM books where book_id = book_id "; // Base query

                // Apply filters if any
                if (isset($_GET['submit'])) {
                    // Search filter
                    if (!empty($_GET['search'])) {
                        $search = $conn->real_escape_string($_GET['search']);
                        $sql .= " AND (title LIKE '%$search%' OR author LIKE '%$search%') ";
                        $paginationSql .= " AND (title LIKE '%$search%' OR author LIKE '%$search%') ";
                    }

                    // Genre filter
                    if (
                        isset($_GET['genres']) && is_array($_GET['genres']) && count($_GET['genres']) > 0) {
                        $escapedGenres = array_map(
                            [$conn, 'real_escape_string'],
                            $_GET['genres']
                        );

                        $genreConditions = array_map(function ($genre) {
                            return "genres LIKE '%$genre%'";
                        }, $escapedGenres);

                        $genreFilter = implode(' OR ', $genreConditions);

                        $sql .= " AND ($genreFilter) ";
                        $paginationSql .= " AND ($genreFilter) ";
                    }



                }

                if (isset($_GET['reset'])) {
                    // Reset filters
                    $sql = "SELECT * FROM books WHERE book_id = book_id ";
                    $paginationSql = "SELECT COUNT(*) AS total FROM books WHERE book_id = book_id ";
                    echo '<script>window.location.href = "library.php";</script>';
                    //exit();
                    // $sql = "SELECT * FROM books WHERE book_id = book_id ";
                    // $paginationSql = "SELECT COUNT(*) AS total FROM books WHERE book_id = book_id ";
                }

                //Pagination
                $limit = 10; // Number of entries to show in a page.
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                if($page == "" || $page < 1) $page1 = 1;
                
                $offset = ($page - 1) * $limit;

                //counting the total number of records
                //$totalQuery = "SELECT COUNT(*) AS total FROM books"; THERES ANOTHER QUERY ONTOP
                $totalResult = $conn->query($paginationSql);
                $totalRow = $totalResult->fetch_assoc();
                $totalRecords = $totalRow['total'];
                $totalPages = ceil($totalRecords / $limit);

                $sql .= " ORDER BY rating DESC, book_id LIMIT $offset, $limit";
                $result = $conn->query($sql);
            
                if ($result->num_rows > 0){

                     while ($manga = $result->fetch_assoc()){
                    
                        $coverPath = 'images/Covers/' . $manga['cover'];
                        if (empty($manga['cover']) || !file_exists($coverPath)) {
                            $coverPath = 'images/Covers/default-cover.jpeg'; // default image
                        }
                    ?>
                    <a href="book.php?id=<?= $manga['book_id'] ?>" class="manga-link">
                        <div class="manga-card">
                            <img src="<?= $coverPath ?>" alt="<?= $manga['title'] ?>">
                            <div class="manga-info">
                                <h3><?= $manga['title'] ?></h3>
                                <p><?= $manga['author'] ?></p>
                            </div>
                        </div>
                    </a>
                    
            <?php }
            }
                else{ ?>
                <div id="no-results">
                    <h2>No Manga found for this filter combination.</h2>
                </div>
            <?php }
                $conn->close();
            ?>
        </div>

        <!-- Pagination -->
         <div class="pagination">
            <div class="page-info">
                <?php if($totalPages == 0) $totalPages = 1;
                    echo "Showing page $page of $totalPages"; 
                ?>
            </div>
            <div class="pagination-controls">
                <?php 
                    if($page >1){
                        echo '<a href="library.php?page=1#manga-grid" class="page-btn">First</a>';
                        echo '<a href="library.php?page='.($page - 1).'#manga-grid" class="page-btn">Prev</a>';
                    }
                    $maxLinks = 5; // Maximum number of page links to show
                    $start = max(1, $page - floor($maxLinks / 2));
                    $end = min($totalPages, $start + $maxLinks - 1);

                    if($end - $start < $maxLinks - 1){
                        $start = max(1, $end - $maxLinks + 1);
                    }

                    for($i = $start; $i <= $end; $i++){
                        if($i == $page){
                            echo '<span class="page-btn-active">'.$i.'</span>';
                        } else {
                            echo '<a href="library.php?page='.$i.'#manga-grid" class="page-btn">'.$i.'</a>';
                        }
                    }

                    if($page < $totalPages){
                        echo '<a href="library.php?page='.($page + 1).'#manga-grid" class="page-btn">Next</a>';
                        echo '<a href="library.php?page='.$totalPages.'#manga-grid" class="page-btn">Last</a>';
                    }

                ?>
            </div>
            </div>
    </section>
</main>

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
                <a href="home.php">Home</a>
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
        <p>&copy; 2025 BookWyrm Library. All rights reserved.</p>
    </div>
</footer>

<script src="JS/nav.js"></script>
<script src="JS/sliders.js"></script>
<script src="JS/lib.js"></script>
</body>
</html>
