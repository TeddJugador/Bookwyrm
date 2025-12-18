<?php
    session_start();
    if (!(isset($_SESSION['username']))) {
        header("Location: ../loginForm.php");
        exit();
    }
    include '../includes/connection.php';
    $success = $_GET['success'] ?? '';
    $error = $_GET['error'] ?? '';
    if ($success) {
        echo "<script>alert('Success: " . htmlspecialchars($success) . "');</script>";
    }
    if ($error) {
        echo "<script>alert('Error: " . htmlspecialchars($error) . "');</script>";
    }

    $username = $_SESSION['username'];
    
    // Get user stats for BookWyrm
    $sql = "SELECT u.*,
            COUNT(DISTINCT CASE WHEN l.status = 'reading' THEN l.book_id END) AS reading_count,
            COUNT(DISTINCT CASE WHEN l.status = 'completed' THEN l.book_id END) AS completed_count,
            COUNT(DISTINCT l.book_id) AS total_books,
            COUNT(DISTINCT r.review_id) AS reviews_count,
            COALESCE(AVG(r.rating), 0) AS avg_rating
            FROM users u
            LEFT JOIN libraries l ON u.username = l.username
            LEFT JOIN reviews r ON u.username = r.username
            WHERE u.username = '$username'
            GROUP BY u.username";
    
    $result = $conn->query($sql);
    $profile = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookWyrm - My Profile</title>
    <link rel="stylesheet" href= "Profile.css">
    <link rel="stylesheet" href="../CSS/nav.css">
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
        <!-- Page Title -->
        <div class="section-title">
            <h2>My Profile</h2>
            <p>Manage your account and reading preferences</p>
        </div>

        <!-- Profile Layout -->
        <div class="profile-container">
            <!-- Profile Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user fa-3x"></i>
                    </div>
                    <h3><?= htmlspecialchars($profile['username']); ?></h3>
                    <p>Reader since <?= date('F Y', strtotime($profile['join_date'] ?? 'now')); ?></p>
                </div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-label">Currently Reading</span>
                        <span class="stat-value"><?= number_format($profile['reading_count'] ?? 0); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Books Completed</span>
                        <span class="stat-value"><?= number_format($profile['completed_count'] ?? 0); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total in Library</span>
                        <span class="stat-value"><?= number_format($profile['total_books'] ?? 0); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Reviews Written</span>
                        <span class="stat-value"><?= number_format($profile['reviews_count'] ?? 0); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Avg Rating Given</span>
                        <span class="stat-value"><?= number_format($profile['avg_rating'] ?? 0, 1); ?>/5</span>
                    </div>
                </div>

                <a href="../includes/logout.php"><button class="btn btn-outline" style="width: 100%;">Logout</button></a>
            </div>

            <!-- Profile Content -->
            <div class="profile-content">
                <div class="profile-tabs">
                    <button class="tab-button active" data-tab="personal">Personal Info</button>
                    <button class="tab-button" data-tab="preferences">Reading Preferences</button>
                    <button class="tab-button" data-tab="password">Password</button>
                </div>

                <!-- Personal Info Tab -->
                <div class="tab-content active" id="personal-tab">
                    <form id="personalInfoForm" action="update_profile.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" id="username" value="<?= htmlspecialchars($profile['username']); ?>" readonly>
                                <p class="form-note">Username cannot be changed</p>
                            </div>
                            <div class="form-group">
                                <label for="displayName">Display Name</label>
                                <input type="text" name="display_name" id="displayName" value="<?= htmlspecialchars($profile['display_name'] ?? $profile['username']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($profile['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" rows="4" placeholder="Tell us about your reading preferences..."><?= htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" name="updateProfile" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>

                <!-- Reading Preferences Tab -->
                <div class="tab-content" id="preferences-tab">
                    <form id="preferencesForm" action="update_preferences.php" method="POST">
                        <div class="form-group">
                            <label>Favorite Genres:</label>
                            <p><?= htmlspecialchars($profile['preferences']);?></p>
                            <div class="genre-checkboxes">
                                <?php
                                // $genres = ['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports', 'Supernatural'];
                                // $user_genres = isset($profile['preferences']) ? explode(',', $profile['preferred_genres']) : [];
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
                        </div>

                        <div class="form-group">
                            <label for="reading_goal">Monthly Reading Goal</label>
                            <input type="number" name="reading_goal" id="reading_goal" min="1" max="50" 
                                   value="<?= $profile['reading_goal'] ?? 5; ?>">
                            <p class="form-note">Number of books you aim to read per month</p>
                        </div>

                        <button type="submit" name="updatePreferences" class="btn btn-primary">Save Preferences</button>
                    </form>
                </div>

                <!-- Password Tab -->
                <div class="tab-content" id="password-tab">
                    <form id="passwordForm" action="../includes/update_password.php" method="POST">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" name="current_password" id="currentPassword" required>
                        </div>

                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" name="new_password" id="newPassword" required>
                            <p class="form-note">Must be at least 8 characters with a mix of letters, numbers, and symbols.</p>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirmPassword" required>
                        </div>

                        <button type="submit" name="changePassword" class="btn btn-primary">Update Password</button>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="danger-zone">
                    <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                    <p>Once you delete your account, all your reading progress and reviews will be permanently lost.</p>
                    
                    <div style="display: flex; gap: 1rem;">
                        <a href="../includes/delete_account.php?id=<?= $_SESSION['username']; ?>">
                            <button class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                                Delete Account
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "includes/footer.php"; ?>
   

    <script src ='../JS/update_preferences.js' ></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked button and corresponding content
                    this.classList.add('active');
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });

        });
    </script>

</body>
</html>