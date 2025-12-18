<?php
    session_start();
    if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';
    require 'AdminFunctions.php';
?>

<?php   //get messages from the database
    $sql = $conn->prepare("SELECT * FROM messages");
    $sql->execute();
    $msgResult = $sql->get_result();
    $messages = $msgResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/konLogo.png">
    <link rel="stylesheet" href="../CSS/AdminDash.css">
    <link rel="stylesheet" href="../CSS/content.css">
    <link rel="stylesheet" href="../CSS/test.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Dashboard</title>
</head>
<body>
    <nav class="nav-bar">

        <section class="nav-logo">
            <img src="../images/konLogo.png" alt="BookWyrm logo">
            <h2>BookWyrm</h2>
        </section>

        <ul class="nav-links">
             <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
                <li><a href="content.php"><i class="fas fa-book"></i> Manage Library</a></li>  
                <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>  
                <li><a href="viewLogs.php"><i class="fas fa-laptop"></i> View Logs</a></li>         
                <li class="dropdown">
                    <a href="#"><i class="fas fa-cog"></i> Settings</a>
                    <ul class="dropdown-content">
                        <li><a href="settings.php">Edit My Details</a></li>
                        <li><a href="changePassword.php">Change My Password</a></li>
                    </ul>
                </li>
        </ul>
        <div class="bottom-links">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <main>
         <section class="header" >
            <div>
                <h1>Admin Dashboard</h1>
            </div>

            <div class="user-info">
                <img src="../images/pochita.jpg" alt="Admin Profile Picture" class="profile-pic">
                <p>Hello, <?= $_SESSION['firstname'] ?>!</p>
            </div>
         </section>

        <section class="overview">
            <div class="overview-card">
                <h3>Active Users</h3>
                <p><?= count(getActiveUsers($conn)) ?></p>
            </div>

            <div class="overview-card" id="top-manga">
                <h3>Top Manga</h3>

                <ol>
                    <!-- <li>Berserk</li>
                    <li>Attack on Titan</li>
                    <li>Jujutsu Kaisen</li>
                    <li>Kaiju No.8</li>
                    <li>Bleach</li> -->
                    <?php
                        $trendingBooks = getTopBooks($conn);
                        if ($trendingBooks && is_array($trendingBooks)) {
                            foreach ($trendingBooks as $book) {
                                echo "<li>" . htmlspecialchars($book['title']) . " - <strong>" . htmlspecialchars($book['author']) . "</strong></li>";
                            }
                        } else {
                            echo "<li>N/A</li>";
                        }
                    ?>
                </ol>
            </div>

            <div class="overview-card">
                <h3>Total Number of Uploads</h3>
                <p><?= getTotalUploads($conn)['total_uploads'] ?></p>
            </div>
        </section>

        
        <section class="graphs">
            <div class="graph-card">
                <h3>Genre Distribution</h3>
                <?php
                    $sql = $conn->prepare("SELECT genres FROM books"); 
                    $sql->execute();
                    $result = $sql->get_result();
                    $genresCount = [];
                    
                    while ($row = $result->fetch_assoc()) {
                        $genres = explode(',', $row['genres']);
                        foreach ($genres as $genre) {
                            $genre = trim($genre);
                            if (!empty($genre)) {
                                if (isset($genresCount[$genre])) {
                                    $genresCount[$genre]++;
                                } else {
                                    $genresCount[$genre] = 1;
                                }
                            }
                        }
                    }
                    arsort($genresCount);
                ?>

                <script> //externally sourced code to create and display the graph.
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('genreChart').getContext('2d');
                        const genreData = <?php echo json_encode($genresCount); ?>;
                        const labels = Object.keys(genreData);
                        const data = Object.values(genreData);
                        
                        const genreChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Genre Distribution',
                                    data: data,
                                    backgroundColor: [ '#E6A95A'
                                    ],
                                    borderColor: ['#D58936'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false 
                                    },
                                    title: {
                                        display: true,
                                        text: 'Book Genres Distribution'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Number of Books'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Genres'
                                        }
                                    }
                                }
                            },
                        });
                    });
            </script>

            <canvas id="genreChart" width="100%" height="50"></canvas>
            </div>
        </section>
    
        <section class="messages">
            <div class="table-container">
                <h3>Recent Messages</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($msgResult->num_rows > 0) {
                                while ($msg = $msgResult->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($msg['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($msg['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($msg['message']) . "</td>";
                                    echo "<td><a href='deleteMessage.php?id=" . $msg['message_id'] . "' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this message?');\">Delete</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No messages found.</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

       
    </main>
</body>
</html>