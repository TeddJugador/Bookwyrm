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
                <li><a href="content.php"><i class="fas fa-book"></i> Manage Library</a></li>  
                <li><a href="users.php" ><i class="fas fa-users"></i> Manage Users</a></li>      
                <li><a href="#"class="active"><i class="fas fa-laptop"></i> View Logs</a></li>   
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

         <!-- <section class="actions">
            <a href="addManga.php"><button class="add-btn">Add New Manga</button></a>

         </section> -->
                  <!--Search and Filter Section-->
   <section id="search-container">
    <h3 class='search-title'>Search & Filter Users</h3>
    <form method="GET" class="search-form">

        <!-- Search Input -->
        <input 
            type="text" 
            id="manga-search" 
            name="search" 
            placeholder="Search by username"
            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
        >

        <!-- Filter Buttons / Dropdowns -->
        <div id="genre-filter-container">

            <!-- Status Filter -->
            <button type="button" class="log-btn">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Any</option>
                    <option value="success" <?php echo (isset($_GET['status']) && $_GET['status'] === 'success') ? 'selected' : ''; ?>>Successful</option>
                    <option value="failed" <?php echo (isset($_GET['status']) && $_GET['status'] === 'failed') ? 'selected' : ''; ?>>Failed</option>
                </select>
            </button>

            <!-- Attempts Filter -->
            <button type="button" class="log-btn">
                <label for="attempts">Attempts</label>
                <select id="attempts" name="attempts">
                    <option value="">Any</option>
                    <option value="1" <?php echo (isset($_GET['attempts']) && $_GET['attempts'] === '1') ? 'selected' : ''; ?>>Less Than 3</option>
                    <option value="2" <?php echo (isset($_GET['attempts']) && $_GET['attempts'] === '2') ? 'selected' : ''; ?>>3 or More</option>
                </select>
            </button>

            <!-- Start Date Filter -->
            <button type="button" class="log-btn">
                <label for="s_filterDate">Start Date</label>
                <select id="s_filterDate" name="s_filterDate">
                    <option value="">Any</option>
                    <option value="Day" <?php echo (isset($_GET['s_filterDate']) && $_GET['s_filterDate'] === 'Day') ? 'selected' : ''; ?>>Past Day</option>
                    <option value="Week" <?php echo (isset($_GET['s_filterDate']) && $_GET['s_filterDate'] === 'Week') ? 'selected' : ''; ?>>Past Week</option>
                    <option value="Month" <?php echo (isset($_GET['s_filterDate']) && $_GET['s_filterDate'] === 'Month') ? 'selected' : ''; ?>>Past Month</option>
                    <option value="Months" <?php echo (isset($_GET['s_filterDate']) && $_GET['s_filterDate'] === 'Months') ? 'selected' : ''; ?>>Past 6 Months</option>
                    <option value="Year" <?php echo (isset($_GET['s_filterDate']) && $_GET['s_filterDate'] === 'Year') ? 'selected' : ''; ?>>Past Year</option>
                    <option value="Years" <?php echo (isset($_GET['s_filterDate']) && $_GET['s_filterDate'] === 'Years') ? 'selected' : ''; ?>>+1 Years</option>
                </select>
            </button>

            <!-- End Date Filter -->
            <button type="button" class="log-btn">
                <label for="e_filterDate">End Date</label>
                <select id="e_filterDate" name="e_filterDate">
                    <option value="">Any</option>
                    <option value="Day" <?php echo (isset($_GET['e_filterDate']) && $_GET['e_filterDate'] === 'Day') ? 'selected' : ''; ?>>Past Day</option>
                    <option value="Week" <?php echo (isset($_GET['e_filterDate']) && $_GET['e_filterDate'] === 'Week') ? 'selected' : ''; ?>>Past Week</option>
                    <option value="Month" <?php echo (isset($_GET['e_filterDate']) && $_GET['e_filterDate'] === 'Month') ? 'selected' : ''; ?>>Past Month</option>
                    <option value="Months" <?php echo (isset($_GET['e_filterDate']) && $_GET['e_filterDate'] === 'Months') ? 'selected' : ''; ?>>Past 6 Months</option>
                    <option value="Year" <?php echo (isset($_GET['e_filterDate']) && $_GET['e_filterDate'] === 'Year') ? 'selected' : ''; ?>>Past Year</option>
                    <option value="Years" <?php echo (isset($_GET['e_filterDate']) && $_GET['e_filterDate'] === 'Years') ? 'selected' : ''; ?>>+1 Years</option>
                    <option value="None" <?php echo (isset($_GET['e_filterDate']) && $_GET['e_filterDate'] === 'None') ? 'selected' : ''; ?>>Not Logged Out</option>
                </select>
            </button>

        </div>

        <!-- Control Row -->
        <div id="controls-row">
            <button class="apply-filters-btn" type="submit" name="submit">Apply Filters</button>
            <button class="clear-filters-btn" type="submit" name="reset" id="reset-btn">Clear Filters</button>
        </div>
    </form>
</section>



        <div id = 'the-table'>
         <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Attempts</th>
                        <th>Log Out</th>

                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM userlogs WHERE log_id = log_id ";
                        $paginationSql = "SELECT COUNT(*) AS total FROM userlogs where log_id = log_id ";
                        //apply filters
                        if(isset($_GET['submit'])){
                            if (!empty($_GET['status'])){
                                $status = $conn->real_escape_string($_GET['status']);
                                $sql .= " AND status = '$status'";
                                $paginationSql .= " AND status = '$status'";
                            }
                            if (!empty($_GET['attempts'])){
                                $attempts = $conn->real_escape_string($_GET['attempts']);
                                switch($attempts){
                                    case "1":
                                        $sql .= " AND attempts < 3";
                                        $paginationSql .= " AND attempts < 3";

                                        break;
                                    case "2":
                                        $sql .= " AND attempts >= 3";
                                        $paginationSql .= " AND attempts >= 3";
                                }
                                
                            }
                            if(isset($_GET['search'])){
                                $target = $conn->real_escape_string($_GET['search']);
                                $sql .= " AND username LIKE '%$target%'";
                                $paginationSql .= " AND username LIKE '%$target%'";
                            }
                            // Date filter
                            if (!empty($_GET['s_filterDate'])) {
                                $s_dateFilter = $conn->real_escape_string($_GET['s_filterDate']);
                                
                                switch ($s_dateFilter) {
                                case "Day":
                                    $sql .= " AND timestampdiff(DAY,time,now()) < 1 ";
                                    $paginationSql .= " AND timestampdiff(DAY,time,now()) < 1 ";
                                    break;
                                case "Week":
                                    $sql .= " AND timestampdiff(WEEK,time,now()) < 1 ";
                                    $paginationSql .= " AND timestampdiff(WEEK,time,now()) < 1 ";
                                    break;
                                case "Month":
                                    $sql .= " AND timestampdiff(MONTH,time,now()) < 1 ";
                                    $paginationSql .= " AND timestampdiff(MONTH,time,now()) < 1 ";
                                    break;
                                case "Months":
                                    $sql .= " AND timestampdiff(MONTH,time,now()) < 7 ";
                                    $paginationSql .= " AND timestampdiff(MONTH,time,now()) < 7 ";
                                    break;
                                case "Year":
                                    $sql .= " AND timestampdiff(YEAR,time,now()) < 1 ";
                                    $paginationSql .= " AND timestampdiff(YEAR,time,now()) < 1 ";
                                    break;
                                case "Years":
                                    $sql .= " AND timestampdiff(YEAR,time,now()) >= 1 ";
                                    $paginationSql .= " AND timestampdiff(YEAR,time,now()) >= 1 ";
                                    break;
                                }
                            }
                            if (!empty($_GET['e_filterDate'])) {
                                $e_dateFilter = $conn->real_escape_string($_GET['e_filterDate']);
                                
                                switch ($e_dateFilter) {
                                case "Day":
                                    $sql .= " AND timestampdiff(DAY,session_end,now()) < 1 ";
                                    $paginationSql .= " AND timestampdiff(DAY,session_end,now()) < 1 ";
                                    break;
                                case "Week":
                                    $sql .= " AND timestampdiff(WEEK,session_end,now()) < 1 ";
                                    $paginationSql .= " AND timestampdiff(WEEK,session_end,now()) < 1 ";
                                    break;
                                case "Month":
                                    $sql .= " AND timestampdiff(MONTH,session_end,now()) < 1 ";
                                    $paginationSql .= " AND timestampdiff(MONTH,session_end,now()) < 1 ";
                                    break;
                                case "Months":
                                    $sql .= " AND timestampdiff(MONTH,session_end,now()) < 7 ";
                                    $paginationSql .= " AND timestampdiff(MONTH,session_end,now()) < 7 ";
                                    break;
                                case "Year":
                                    $sql .= " AND timestampdiff(YEAR,session_end,now()) < 1 ";
                                    $paginationSql .= " AND timestampdiff(YEAR,session_end,now()) < 1 ";
                                    break;
                                case "Years":
                                    $sql .= " AND timestampdiff(YEAR,session_end,now()) >= 1 ";
                                    $paginationSql .= " AND timestampdiff(YEAR,session_end,now()) >= 1 ";
                                    break;
                                case "None":
                                    $sql .= " AND session_end is NULL ";
                                    $paginationSql .= " AND session_end is NULL ";
                                }
                            }
                        }
                        
                        if (isset($_GET['reset'])) {
                            // Reset filters
                            $sql = $sql = "SELECT * FROM userslogs WHERE log_id = log_id  ";
                            $paginationSql = "SELECT COUNT(*) AS total FROM users WHERE log_id = log_id ";
                            echo '<script>window.location.href = "viewLogs.php";</script>';
                            //exit();
                            // $sql = "SELECT * FROM books WHERE book_id = book_id ";
                            // $paginationSql = "SELECT COUNT(*) AS total FROM books WHERE book_id = book_id ";
                        }

                        $limit = 10; // number of records per page
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        if ($page < 1) $page = 1;

                        $offset = ($page - 1) * $limit;
                        
                        $resultCount = $conn->query($paginationSql);
                        $rowCount = $resultCount->fetch_assoc();
                        $totalRecords = $rowCount['total'];
                        $totalPages = ceil($totalRecords / $limit);

                        $sql .= " ORDER BY time DESC LIMIT $offset, $limit";
                        $result = $conn->query($sql);
                        if($result->num_rows>0){
                            while($log = $result->fetch_assoc()){
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($log['username'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($log['time'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($log['status'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($log['attempts'] ?? '') . "</td>";
                                if ($log['session_end'] === NULL) {
                                    echo "<td style='color:red'>Not Logged Out</td>";
                                } else {
                                    echo "<td>" . htmlspecialchars($log['session_end']) . "</td>";
                                }
                                echo "</tr>";
                            }
                        }
                        else{
                            echo "<tr><td colspan='5'>No user logs found.</td></tr>";
                        }
                        // $stmt = $conn->prepare("SELECT * FROM userlogs");
                        // $stmt->execute();
                        // $logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        // if ($logs && is_array($logs)) {
                        //     foreach ($logs as $log) {
                        //         echo "<tr>";
                        //         echo "<td>" . htmlspecialchars($log['username'] ?? '') . "</td>";
                        //         echo "<td>" . htmlspecialchars($log['time'] ?? '') . "</td>";
                        //         echo "<td>" . htmlspecialchars($log['status'] ?? '') . "</td>";
                        //         echo "<td>" . htmlspecialchars($log['attempts'] ?? '') . "</td>";
                        //         if ($log['session_end'] === NULL) {
                        //             echo "<td style='color:red'>Not Logged Out</td>";
                        //         } else {
                        //             echo "<td>" . htmlspecialchars($log['session_end']) . "</td>";
                        //         }
                        //         echo "</tr>";
                        //     }
                        // } else {
                        //     echo "<tr><td colspan='5'>No user logs found.</td></tr>";
                        // }
                    ?>
                </tbody>
            </table>
         </section>
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
                        echo '<a href="viewLogs.php?page=1#the-table" class="page-btn">First</a>';
                        echo '<a href="viewLogs.php?page='.($page - 1).'#the-table" class="page-btn">Prev</a>';
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
                            echo '<a href="viewLogs.php?page='.$i.'#the-table" class="page-btn">'.$i.'</a>';
                        }
                    }

                    if($page < $totalPages){
                        echo '<a href="viewLogs.php?page='.($page + 1).'#the-table" class="page-btn">Next</a>';
                        echo '<a href="viewLogs.php?page='.$totalPages.'#the-table" class="page-btn">Last</a>';
                    }

                ?>
            </div>
            </div>
    </main>
    <script>
        //delete confirmation
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                if (!confirm('Are you sure you want to delete this manga?')) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>