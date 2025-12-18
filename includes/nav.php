<?php 
    if(session_status() === PHP_SESSION_NONE) session_start();
 ?>

<nav class="navbar" id="main-navbar">
    <div class="nav-container">
        <div class="logo-and-name">
            <a href="#hero" class="logo">
                <img src="../images/konLogo.png" alt="BookWyrm Logo" class="logo-img" id="navbar-logo" loading="lazy">
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
