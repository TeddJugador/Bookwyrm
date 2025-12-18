<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/nav.css">
    <link rel="stylesheet" href="CSS/aboutus.css">
    <link rel="icon" href="images/konLogo.png">
    <title>About Us | BookWyrm</title>
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

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <h1>Miracle Systems</h1>
                <p>
                    We are a dedicated collective of diverse thinkers, committed to delivering excellence and innovation through strong collaboration and mutual support.
                </p>
            </div>
        </section>

        <!-- Vision and Mission Section -->
        <section id="vision-mission" class="section">
            <div class="container">
                <div class="section-title">
                    <p>Foundations of Our Work</p>
                    <h2>Our Core Values & Purpose</h2>
                </div>

                <div class="mission-grid">
                    <!-- Vision Card -->
                    <div class="mission-card">
                        <h3>Vision: Leading with Integrity</h3>
                        <p style="color: var(--text-light);">
                            Our vision is to set the benchmark for effective teamwork in our field. We strive to be recognized for our ethical approach, technical proficiency, and positive impact on the projects and communities we engage with.
                        </p>
                    </div>

                    <!-- Mission Card -->
                    <div class="mission-card" style="border-top-color: var(--accent);">
                        <h3>Mission: Sharing the love of reading</h3>
                        <p style="color: var(--text-light);">
                            We believe that manga should be accessible to everyone, everywhere. Our goal is to create the best online manga reading experience that combines the traditional feel of reading manga with modern web technology.
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Members Section -->
        <section id="team-members" class="section" style="background-color: var(--white);">
            <div class="container">
                <div class="section-title">
                    <p>The Driving Force</p>
                    <h2>Meet Our Group Members</h2>
                </div>

                <div class="team-grid">
                    <!-- Team Member 1 -->
                    <div class="team-card">
                        <img src="images/Munashe_IMG.jpeg" alt="Portrait of Munashe's avator">
                        <div class="member-info">
                            <h3>Munashe Madziwanyika</h3>
                            <p>Project Leader</p>
                            <p>Specializes in cyber-security and likes airplanes...a lot</p>
                        </div>
                    </div>

                    <!-- Team Member 2 -->
                    <div class="team-card">
                        <img src="images/theoImage.jpg" alt="Portrait of Theo's avator">
                        <div class="member-info">
                            <h3>Theodore Masi</h3>
                            <p>The Guy with a Girlfriend</p>
                            <p>Fullstack Developer. Excels at quality of life functionalities and likes to troll us too much.</p>
                        </div>
                    </div>

                    <!-- Team Member 3 -->
                    <div class="team-card">
                        <img src="images/Keith_IMG.jpg" alt="Portrait of Keith's avator">
                        <div class="member-info">
                            <h3>Keith Dube</h3>
                            <p>The CSS Nerd</p>
                            <p>Fullstack Developer.<br>CSS, CSS, and more CSS.<br>
                                Likes playing games in lectures.</p>
                        </div>
                    </div>

                    <!-- Team Member 4 -->
                    <div class="team-card">
                        <img src="images/kylePhoto.jpg" alt="Portrait of Kyle's avator">
                        <div class="member-info">
                            <h3>Kyle Nkomo</h3>
                            <p>The Cutesy of the Group</p>
                            <p>Fullstack Developer.
                                <br>Likes apricot jam and cheese toasties...</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Contact/CTA Section -->
        <section id="contact-us" class="section">
             <div class="container" style="text-align: center; padding: 2rem; background-color: var(--secondary-color); border-radius: var(--radius-lg);">
                 <h2 style="font-size: 2rem; color: var(--text-dark); margin-bottom: 1rem;">Let's Collaborate</h2>
                 <p style="color: var(--text-dark); margin-bottom: 2rem;">
                     Have questions about our mission or want to discuss a potential partnership? We'd love to hear from you.
                 </p>
                 <a href="home.php#contact" style="background-color: var(--primary-color); color: var(--white); padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 700; display: inline-block; box-shadow: var(--shadow);">
                     Get In Touch
                 </a>
            </div>
        </section>

    </main>

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

    <!-- JavaScript for Mobile Menu Toggle and Smooth Scroll -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const navLinks = document.getElementById('main-nav');
            const header = document.querySelector('header');
            
            // Toggle Mobile Menu
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                
                // Change icon based on state (optional, but good UX)
                if (navLinks.classList.contains('active')) {
                    menuToggle.textContent = '✕'; 
                } else {
                    menuToggle.textContent = '☰';
                }
            });

            // Close menu and handle scroll when a link is clicked
            document.querySelectorAll('#main-nav a').forEach(link => {
                link.addEventListener('click', (e) => {
                    // Only close on mobile (if active)
                    if (navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        menuToggle.textContent = '☰';
                    }
                    
                    // Simple custom scroll logic to ensure consistency
                    e.preventDefault();
                    const targetId = e.target.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        // Offset for the sticky header height (approx 60px)
                        const headerHeight = header.offsetHeight + 20; 
                        window.scrollTo({
                            top: targetElement.offsetTop - headerHeight,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>