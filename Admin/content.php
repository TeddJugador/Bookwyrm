<?php
    session_start();
    if ($_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';
    require 'AdminFunctions.php';


    // Fetch and display success or error messages
    $success = $_GET['success'] ?? '';
    $error = $_GET['error'] ?? '';
    if ($success) {
        echo "<script>alert('Success: " . htmlspecialchars($success) . "');</script>";
    }
    if ($error) {
        echo "<script>alert('Error: " . htmlspecialchars($error) . "');</script>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/konLogo.png">
    <link rel="stylesheet" href="../CSS/content.css">
    <link rel="stylesheet" href="../CSS/test.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manage Content</title>
</head>
<body>
    <nav class="nav-bar"> 

        <section class="nav-logo">
            <img src="../images/konLogo.png" alt="BookWyrm logo">
            <h2>BookWyrm</h2>
        </section>

        <ul class="nav-links">
             <li><a href="AdminDash.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
                <li><a href="#" class="active"><i class="fas fa-book"></i> Manage Library</a></li>  
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
                <h1>Manage Library</h1>
            </div>

            <div class="user-info">
                <img src="../images/pochita.jpg" alt="Admin Profile Picture" class="profile-pic">
                <p>Hello, <?= $_SESSION['firstname'] ?>!</p>
            </div>
         </section>

        <!--Search and Filter Section-->
   <section id="search-container">
    <h3 class='search-title'>Search & Filter</h3>
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


         

         <section id='card-container'>
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
                    if (isset($_GET['genres']) && is_array($_GET['genres']) && count($_GET['genres']) > 0) {
                        $escapedGenres = array_map([$conn, 'real_escape_string'], $_GET['genres']);
                        $genreConditions = array_map(function($genre) {
                            return " FIND_IN_SET('$genre', genres) > 0";
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
                    echo '<script>window.location.href = "content.php";</script>';
                    //exit();
                    
                }

                //Pagination
                $limit = 12; // Number of entries to show in a page.
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
                if($result->num_rows > 0){
                    while($book = $result->fetch_assoc()){?>
                        <div class='card' >
                                <div class='card-inner'>
                                    <div class ='front'>
                                        <?php
                                            $coverPath = '../images/Covers/' . $book['cover'];
                                            if (empty($book['cover']) || !file_exists($coverPath)) {
                                                $coverPath = '../images/Covers/default-cover.jpeg'; // default image
                                            }
                                        ?>
                                        <img src='<?= $coverPath; ?>' alt='Cover' class='card-img'>
                                        <h3><?= htmlspecialchars($book['title']); ?></h3>
                                        <p><?= htmlspecialchars($book['author']); ?></p>
                                        <p><?= $book['status']; ?></p>
                                    </div>
                                    <div class='back'>
                                        <p class='gens'><strong>Genres: </strong><?= htmlspecialchars($book['genres']); ?></p>
                                        <br>
                                        <p><strong>Rating: </strong><?= htmlspecialchars($book['rating']); ?>/5</p>
                                        <br>
                                        <p class='desc'><strong>Description: </strong><?=htmlspecialchars($book['description']); ?></p>
                                        <br>
                                        <div class='card-actions'>
                                                <a href='editManga.php?book_id=<?=$book['book_id']; ?>'><button class='edit-btn'><i class='fas fa-edit'></i> Edit</button></a>
                                                <a href='deleteManga.php?book_id=<?=$book['book_id']; ?>'><button class='delete-btn'><i class='fas fa-trash-alt'></i> Delete</button></a>
                                        </div>
                                    </div> 
                                </div>
                            </div>

                  <?php  }
                } else {?>
                    <div id="no-results">
                        <h2>No Manga found.</h2>
                    </div>
                    
                <?php }
                $conn->close();
            ?>
         </section>

         <section class="actions">
            <a href="addManga.php"><button class="add-btn">Add New Manga</button></a>
         </section>

         <!-- Pagination -->
         <div class="pagination">
            <div class="page-info">
                <?php if($totalPages == 0) $totalPages = 1;
                    echo "<p>Showing page $page of $totalPages<p>"; 
                ?>
            </div>
            <div class="pagination-controls">
                <?php 
                    if($page >1){
                        echo '<a href="content.php?page=1#card-container" class="page-btn">First</a>';
                        echo '<a href="content.php?page='.($page - 1).'#card-container" class="page-btn">Prev</a>';
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
                            echo '<a href="content.php?page='.$i.'#card-container" class="page-btn">'.$i.'</a>';
                        }
                    }

                    if($page < $totalPages){
                        echo '<a href="content.php?page='.($page + 1).'#card-container" class="page-btn">Next</a>';
                        echo '<a href="content.php?page='.$totalPages.'#card-container" class="page-btn">Last</a>';
                    }

                ?>
            </div>
            </div>

         <!-- <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Description</th>
                        <th>Status</th>

                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // $books = getAllBooks($conn);
                        // if ($books && is_array($books)) {
                        //     foreach ($books as $book) {
                        //         echo "<tr>";
                        //         echo "<td><img src='../images/Covers/".$book['cover']."' alt='Cover' class='cover-img'></td>";
                        //         echo "<td>" . htmlspecialchars($book['title']) . "</td>";
                        //         echo "<td>" . htmlspecialchars($book['author']) . "</td>";
                        //         echo "<td>" . htmlspecialchars($book['genres']) . "</td>";
                        //         echo "<td>" . htmlspecialchars($book['description']) . "</td>";
                        //         echo "<td>" . $book['status']. "</td>";
                        //         echo "<td>
                        //                 <a href='editManga.php?book_id=".$book['book_id']."'><button class='edit-btn'><i class='fas fa-edit'></i> Edit</button></a>
                        //                 <a href='deleteManga.php?book_id=".$book['book_id']."'><button class='delete-btn'><i class='fas fa-trash-alt'></i> Delete</button></a>
                        //               </td>";
                        //         echo "</tr>";
                        //     }
                        // } else {
                        //     echo "<tr><td colspan='7'>No books found.</td></tr>";
                        // }
                    ?>
                </tbody>
            </table>
         </section> -->
    </main>
    <script src="../JS/content.js"></script>
    
</body>
</html>