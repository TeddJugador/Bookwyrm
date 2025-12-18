<?php

    session_start();

    $action = $_GET['action'] ?? '';

    if (isset($_SESSION['username'])) {
        switch ($_SESSION['role']) {
            case 'Reader':
                // Reader page
                switch ($action) {
                    case 'l':
                    case '':
                        // Reader dashboard
                        header("Location: User/Post-Login.php");
                        exit();
                        break;
                    case 'b':
                        // Library
                        header("Location: library.php");
                        exit();
                        break;
                }
                break;
            // case 'Author':
            //     // Author page
            //     break;
            case 'Admin':
                // Admin page
                header("Location: Admin/AdminDash.php");
                exit();
                break;
        }
    } else {
        header("Location: loginForm.php?action=$action");
        exit();
    }

?>