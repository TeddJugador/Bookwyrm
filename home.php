<?php 
        //require '../CS3-Library/includes/connection.php';

        require 'includes/connection.php';
        require 'Admin/AdminFunctions.php';
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
<?php

        // Fetch trending manga (limit 12 for the slider)
        $TrendingQuery = "SELECT book_id, title, author, cover FROM books LIMIT 12";
        $TrendingResult = $conn->query($TrendingQuery);

        // Recommended Manga (5 random completed or highly-rated)
        $recommendedQuery = "SELECT * FROM books ORDER BY rating DESC LIMIT 5";
        // Fetch top-rated or popular books
        
        
        $recommendedResult = $conn->query($recommendedQuery);

        // Latest Manga (most recent 8)
        $latestQuery = "SELECT * FROM books ORDER BY book_id DESC LIMIT 12";
        $latestResult = $conn->query($latestQuery);

        // Handle messages from contact form or other actions
        //$error = $_GET['error'] ?? '';

        //$success = $_GET['success'] ?? '';
        if (isset($_GET['success'])) {
            echo '<script>alert("' . htmlspecialchars($_GET['success']) . '");</script>';
        }
        if (isset($_GET['error'])) {
            echo '<script>alert("Error: ' . htmlspecialchars($_GET['error']) . '");</script>';
        }

    ?> <!--MOVED THIS CODE HERE ---KYLE-->

<?php
    //form handler for contact us form
    
    //include 'signup.php';

    if (isset($_POST['send-message'])) {
        $name = test_input($conn->real_escape_string($_POST['name']));
        //$lastname = test_input($conn->real_escape_string($_POST['lastname']));
        $email = test_input($conn->real_escape_string($_POST['email']));
        $message = $conn->real_escape_string($_POST['message']);

        if (empty($name) || empty($email) || empty($message)) {//makes sure no fields are empty
            header("Location: home.php?&error=all+fields+are+required");
            exit();
        }

        if (!preg_match("/^[a-zA-Z'\s-]+$/", $name)) {//maeks sure name only has letters
            header("Location: home.php?error=names+can+only+contain+letters,+apostrophes,+and+hyphens");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {//validates email
            header("Location: home.php?error=invalid+email+format");
            exit();
        }

        $insert = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $name, $email, $message);
        $insert->execute();

        header("Location: home.php?success=message+sent");
        exit();

    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/nav.css">
    <link rel="stylesheet" href="CSS/home.css">
    <title>BookWyrm - Home</title>
    <link rel="icon" href="images/konLogo.png">
</head>
<body>
    

    <!--Hero Section-->
    <section id="hero">
        <div class="hero-content">
            <h1>BookWyrm Library</h1>
            <p>
                Welcome to our online manga library!<br>
                Find, read, and review manga series online or download to read offline.<br>
                Create your own library with favorites, or save to your readlist for later.
            </p>
        </div>
    </section>

    <!-- Trending Manga Section -->
    <section class="section" id="trending">
        <div class="container">
            <div class="section-title">
                <h2>Trending Manga</h2>
            </div>
            <div class="trending-slider">
                <div class="slider-container" id="trending-slider">
                    <?php 
                        if ($TrendingResult->num_rows > 0) {
                            while($row = $TrendingResult->fetch_assoc()) {
                                $coverPath = 'images/Covers/' . $row['cover'];
                                
                                // Use default if cover is empty or file doesn't exist
                                if (empty($row['cover']) || !file_exists($coverPath)) {
                                    $coverPath = 'images/Covers/default-cover.jpeg'; // default cover
                                }
                                echo "
                                    <div class='manga-card'>
                                        <img src=\"{$coverPath}\" alt=\"{$row['title']}\">
                                        <h3>{$row['title']}</h3>
                                        <p>{$row['author']}</p>
                                    </div>
            
                                ";

                            }
                        } else {
                            echo "<p>No manga found.</p>";
                        }
                    ?>
                </div>
                <div class="slider-controls">
                    <div class="slider-btn prev-btn" data-target="trending-slider">←</div>
                    <div class="slider-btn next-btn" data-target="trending-slider">→</div>
                </div>
            </div>
        </div>
    </section>


    <!-- Recommended Manga Section -->
    <section class="section recommended">
        <div class="container">
            <div class="section-title">
                <h2>Recommended Manga</h2>
            </div>
            <div class="recommended-slider" id="recommended-slider-container">
                <?php while ($manga = $recommendedResult->fetch_assoc()): ?>
                    <?php 
                        $coverPath = 'images/Covers/' . $manga['cover'];
                        if (!file_exists($coverPath) || empty($manga['cover'])) {
                            $coverPath = 'images/Covers/default-cover.jpeg'; // path to your default image
                        }
                    ?>
    
                    <div class="recommended-slide" style="background-image: url('<?= $coverPath ?>');">
                        <div class="slide-content">
                            <h3><?= $manga['title'] ?></h3>
                            <p><?= substr($manga['description'], 0, 200) ?>...</p>
                            <a href="book.php?id=<?= $manga['book_id'] ?>" class="btn" data-manga-id="<?= $manga['book_id'] ?>">Read Now</a>
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


    <!-- Latest Manga Section -->


    <section class="section" id="latest">
        <div class="container">
            <div class="section-title">
                <h2>Latest Releases</h2>
            </div>
            <div class="latest-grid" id="latest-grid">
                <?php while ($manga = $latestResult->fetch_assoc()): ?>
                    <?php 
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
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section contact" id="contact">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--secondary-color);">Contact Us</h2>
            </div>
            <form class="contact-form" action="home.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" class="form-control" name="message" required></textarea>
                </div>
                <input type="submit" class="btn btn-outline" value="Send Message" name="send-message">
                <p id="contact-message" style="color: var(--secondary-color); margin-top: 15px; text-align: center; display: none;">Message Sent! (Simulated)</p>
            </form>
        </div>
    </section>

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
    <script src="JS/BookWyrm.js"></script>

</body>
</html>
