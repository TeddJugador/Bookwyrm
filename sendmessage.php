<?php

    require 'includes/connection.php';
    //include 'signup.php';

    if (isset($_POST['submit'])) {
        $name = test_input($conn->real_escape_string($_POST['name']));
        //$lastname = test_input($conn->real_escape_string($_POST['lastname']));
        $email = test_input($conn->real_escape_string($_POST['email']));
        $message = $conn->real_escape_string($_POST['message']);

        if (empty($name) || empty($email) || empty($message)) {//makes sure no fields are empty
            header("Location: home.php?&error=all+fields+are+required");
            exit();
        }

        if (!preg_match("/^[a-zA-Z'-]+$/", $name)) {//maeks sure name only has letters
            header("Location: home.php?error=names+can+only+contain+letters,+apostrophes,+and+hyphens");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {//validates email
            header("Location: home.php?error=invalid+email+format");
            exit();
        }

        $insert = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $name, $email, $message);
        $insert->execute();

        header("Location: home.php?success=message+sent+successfully");
        exit();

    }

?>