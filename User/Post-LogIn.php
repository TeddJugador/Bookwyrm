<?php
require "../includes/connection.php";

session_start();
$username = $_SESSION['username'];
$firstname = $_SESSION['firstname'];

// Books in progress
$reading_query = "SELECT COUNT(*) AS count FROM libraries WHERE username = '$username' AND status = 'Reading'";
$reading_result = $conn->query($reading_query);
$reading_count = $reading_result->fetch_assoc()['count'] ?? 0;

// Total books in library (all statuses)
$library_query = "SELECT COUNT(*) AS count FROM libraries WHERE username = '$username'";
$library_result = $conn->query($library_query);
$library_count = $library_result->fetch_assoc()['count'] ?? 0;

// Reviews written
$reviews_query = "SELECT COUNT(*) AS count FROM reviews WHERE username = '$username'";
$reviews_result = $conn->query($reviews_query);
$reviews_count = $reviews_result->fetch_assoc()['count'] ?? 0;

// Recently read manga - get from libraries with book details
$recent_query = " SELECT 
        b.book_id, 
        b.title, 
        b.cover, 
        b.author, 
        l.current_chapter, 
        (SELECT COUNT(*) FROM chapters c WHERE c.book_id = b.book_id) AS total_chapters,
        l.status
    FROM libraries l
    JOIN books b ON l.book_id = b.book_id
    WHERE l.username = '$username'
    
";
$recent_manga = $conn->query($recent_query);

// Count completed books
$completed_query = "
    SELECT COUNT(*) AS count 
    FROM libraries 
    WHERE status = 'Completed'
";

$completed_result = $conn->query($completed_query);
$completed_count = $completed_result->fetch_assoc()['count'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookWyrm -User Dashboard</title>
    <link rel="stylesheet" href="../CSS/Post-Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-and-name">
                <div class="logo">
                    <img src="../images/konLogo.png" alt="BookWyrm Logo" class="logo-img" id="navbar-logo" loading="lazy">
                </div>
                <div class="navbar-brand">BookWyrm Library</div>
            </div>
            
            <ul class="navbar-nav">
                <li><a href="../home.php">Home</a></li>
                <li><a href="../library.php">Library</a></li>
                <li><a href="Profile.php">Profile</a></li>
                <li><a href="../logout.php" class="logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Welcome Banner -->
        <section class="welcome-banner">
            <div class="welcome-header">
                <h2>Welcome back, <?= $firstname ?>!</h2>
                <p>Here's a quick overview of your reading activity and personalized stats.</p>
            </div>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-value"><?php echo $reading_count; ?></div>
                    <div class="stat-label">Reading Now</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <div class="stat-value"><?php echo $library_count; ?></div>
                    <div class="stat-label">In My Library</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-value"><?php echo $reviews_count; ?></div>
                    <div class="stat-label">Reviews Written</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-value">
                       <?php echo $completed_count; ?>
                    </div>
                    <div class="stat-label">Completed Books</div>
                </div>
            </div>
        </section>

        <!-- Quick Links Section -->
        <section class="section">
            <div class="section-title">
                <h2>Quick Actions</h2>
                <p>Jump right back into your reading journey</p>
            </div>
            
            <div class="quick-links">
                <a href="../library.php" class="quick-link">
                    <div class="quick-link-card">
                        <div class="quick-link-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3>Browse Library</h3>
                        <p>Explore our full collection of manga by genre or author.</p>
                    </div>
                </a>

                <a href="library.php?filter=favorites" class="quick-link">
                    <div class="quick-link-card">
                        <div class="quick-link-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>My Favorites</h3>
                        <p>View and manage your<br> favorite manga list.</p>
                    </div>
                </a>

                <!-- <a href="library.php?filter=recommended" class="quick-link">
                    <div class="quick-link-card">
                        <div class="quick-link-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Recommended Reads</h3>
                        <p>See personalized manga recommendations for you.</p>
                    </div>
                </a> -->

                <a href="profile.php" class="quick-link">
                    <div class="quick-link-card">
                        <div class="quick-link-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3>Edit Profile</h3>
                        <p>Update your account information and preferences.</p>
                    </div>
                </a>
            </div>
        </section>

        <!-- Continue Reading Section -->
        <section class="section">
            <div class="section-title">
                <h2>Your Library</h2>
                <p>Pick up where you left off</p>
            </div>
            
            <div class="manga-grid">
                <?php if ($recent_manga && $recent_manga->num_rows > 0): ?>
                    <?php while($manga = $recent_manga->fetch_assoc()): ?>
                        <?php
                        $progress = 0;
                        if ($manga['total_chapters'] > 0) {
                            $progress = ($manga['current_chapter'] / $manga['total_chapters']) * 100;
                        }
                        ?>
                        <div class="manga-card">
                            <div class="manga-cover">
                                <img src="../images/covers/<?php echo htmlspecialchars($manga['cover'] ?? 'default.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($manga['title']); ?>"
                                     onerror="this.src='../images/covers/default.jpg'">
                                <?php if ($progress > 0): ?>
                                <div class="progress-overlay">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                                    </div>
                                    <span class="progress-text"><?php echo floor($progress); ?>%</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($manga['title']); ?></h3>
                            <p class="manga-author">by <?php echo htmlspecialchars($manga['author'] ?? 'Unknown'); ?></p>
                            <?php 
                                if ($manga['status'] === 'read later') {
                                    echo "<p class=\"manga-chapters\">Read Later</p>";
                                } else if ($manga['status'] === 'reading') {
                                    echo "<p class=\"manga-chapters\">Ch. {$manga['current_chapter']}";
                                    if ($manga['total_chapters'] > 0) {
                                        echo "/" . $manga['total_chapters'];
                                    }
                                }
                            ?>
                            </p>
                            <a href="../book.php?id=<?php echo $manga['book_id']; ?>&chapter=<?php echo $manga['current_chapter'] + 1; ?>" 
                               class="continue-btn">
                               <?php echo $manga['status'] === 'reading' ? 'Continue' : 'Start Reading'; ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-book-open fa-3x"></i>
                        <h3>No Manga in Library</h3>
                        <p>Start adding manga to your library to see them here!</p>
                        <a href="../library.php" class="btn btn-primary">Explore Library</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </div>

    <!-- Footer -->
     <?php include "../includes/footer.php"; ?>

    <script src="../JS/nav.js"></script>

    <script>
        // Simple animation for stat cards
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>