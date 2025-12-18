<?php
    session_start();
    if (!isset($_SESSION) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';
    require 'AdminFunctions.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/konLogo.png">
    <link rel="stylesheet" href="../CSS/users.css">
    <link rel="stylesheet" href="../CSS/test.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manage Users</title>
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
                <li><a href="#" class="active"><i class="fas fa-users"></i> Manage Users</a></li>   
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
                <h1>Manage Users</h1>
            </div>

            <div class="user-info">
                <img src="../images/pochita.jpg" alt="Admin Profile Picture" class="profile-pic">
                <p>Hello, <?= $_SESSION['firstname'] ?>!</p>
            </div>
         </section>

         <!--Search and Filter Section-->
   <section id="search-container">
    <div class="filter-header">
        <h3>Filter Users</h3>
        <span id="results-count">
            <?php 
                $displayMsg = "Displaying All Users";

                // Apply filters
                if (isset($_GET['submit'])) {
                    // search bar
                    if (!empty($_GET['search'])) {
                        $searchTerm = htmlspecialchars($_GET['search']);
                        $displayMsg = "Search Results for '$searchTerm'";
                    }

                    // role filter
                    if (!empty($_GET['role'])) {
                        $role = $conn->real_escape_string($_GET['role']);
                        $displayMsg .= " (Role: $role)";
                    }

                    }

                echo "<p>$displayMsg<p>";
            ?>
        </span>
    </div>
        <form method="GET" class="search-form">
            <input type="text" id = "manga-search" name="search" placeholder="Search by username, name, surname, email, phone number" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <div class="filter-group">
                <label for="role">User Role</label>
                <select id="role" name ="role">
                    <option value="">Any Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Reader">Reader</option>
                </select>
            </div>

            <div id="controls-row">
                <button class="apply-filters-btn" type="submit" name = "submit">Search</button>
                <button class = "clear-filters-btn" type="submit" name = "reset" id="reset-btn">Clear Search</button>
            </div>
            
            
        </form>

   </section>


        
         <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Role</th>
                        <th col>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM users WHERE deleted = 0 ";
                        $paginationSql = "SELECT COUNT(*) AS total FROM users WHERE deleted = 0 ";
                        if(isset($_GET['submit'])){
                            if (!empty($_GET['role'])){
                                $role = $conn->real_escape_string($_GET['role']);
                                $sql .= " AND role = '$role'";
                                $paginationSql .= " AND role = '$role'";
                            }
                            if(isset($_GET['search'])){
                                $target = $conn->real_escape_string($_GET['search']);
                                $sql .= " AND (first_name LIKE '%$target%'OR last_name LIKE '%$target%' OR email LIKE '%$target%' OR username LIKE '%$target%' OR contact_number LIKE '%$target%')";
                                $paginationSql .= " AND (first_name LIKE '%$target%'OR last_name LIKE '%$target%' OR email LIKE '%$target%' OR username LIKE '%$target%' OR contact_number LIKE '%$target%')";
                            }
                        }
                        
                        if (isset($_GET['reset'])) {
                            // Reset filters
                            $sql = $sql = "SELECT * FROM users WHERE deleted = 0 ";
                            $paginationSql = "SELECT COUNT(*) AS total FROM users WHERE deleted = 0 ";
                            echo '<script>window.location.href = "users.php";</script>';
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

                $sql .= " LIMIT $offset, $limit";
                $result = $conn->query($sql);
                if ($result->num_rows>0){
                    while($user = $result->fetch_assoc()){
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['username'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($user['first_name'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($user['last_name'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($user['email'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($user['contact_number'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars(ucfirst($user['role'])) . "</td>";

                        if ($user['role'] == 'Admin'){
                            echo "<td colspan='2'>
                                <a href='deleteUser.php?id=" .$user['username']. "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                                </td>";
                        } else{
                            echo "<td></td>";
                        }

                    }
                }
                else{
                    echo "<tr><td colspan='6'>No active users found.</td></tr>";
                }
                    ?>
                </tbody>
            </table>

             <section class="actions">
                <a href="add.php"><button class="add-btn">Add New User</button></a>
            </section>

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
                        echo '<a href="users.php?page=1" class="page-btn">First</a>';
                        echo '<a href="users.php?page='.($page - 1).'" class="page-btn">Prev</a>';
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
                            echo '<a href="users.php?page='.$i.'" class="page-btn">'.$i.'</a>';
                        }
                    }

                    if($page < $totalPages){
                        echo '<a href="users.php?page='.($page + 1).'" class="page-btn">Next</a>';
                        echo '<a href="users.php?page='.$totalPages.'" class="page-btn">Last</a>';
                    }

                ?>
            </div>
        </div>

         
    </main>
</body>
</html>

<?php
    $success = $_GET['success'] ?? '';
    $error = $_GET['error'] ?? '';
    if ($success) {
        echo "<script>alert('Success: " . htmlspecialchars($success) . "');</script>";
    }
    if ($error) {
        echo "<script>alert('Error: " . htmlspecialchars($error) . "');</script>";
    }

?>