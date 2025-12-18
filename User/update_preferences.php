<?php
    session_start();
    if ($_SESSION['role'] !== 'Reader') {
        header("Location: ../loginForm.php");
        exit();
    }
    require '../includes/connection.php';


    // Fetch and display success or error messages
    $success = $_GET['success'] ?? '';
    $error = $_GET['error'] ?? '';
    if ($success) {
        echo "<script>alert('Success: " . htmlspecialchars($success) . "');</script>";
    }
    if ($error) {
        echo "<script>alert('Error: " . htmlspecialchars($error) . "');</script>";
    }
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    if (isset($_POST['updatePreferences'])) {

        if (isset($_POST['genres']) && is_array($_POST['genres']) && count($_POST['genres']) > 0) {
            $username = $_SESSION['username'];
            $sql = "UPDATE users SET preferences = ?, target = ? WHERE username = ?";
            $escapedGenres = array_map([$conn, 'real_escape_string'], $_POST['genres']);
            $genres = implode(',', $escapedGenres);
            $stmt = $conn->prepare($sql);
            $goal = test_input($conn->real_escape_string($_POST['reading_goal']));
            $stmt->bind_param("sis",$genres,$goal,$username);

            if($stmt->execute()){
                echo "<script>alert('succesfully updated')</script>";
                header("Location: Post-LogIn.php");
            }
            else{
                echo "<script>alert('error updating')</script>";
                header("Location: Post-LogIn.php");
            }
        }
        

        
    }

?>


