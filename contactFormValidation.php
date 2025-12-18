<?php 
    require 'connection.php';

    $fName = $_REQUEST['fname'];
    $sName = $_REQUEST['sname'];
    $email = $_REQUEST['email'];
    $msg = $_REQUEST['message'];

    function clean($cleaned){       //returns a cleaned version of the user's message 
        $cleaned = trim($cleaned);
        $cleaned = htmlspecialchars($cleaned);
        $cleaned = stripslashes($cleaned);

        return $cleaned;
    }

    $msg = clean($msg);
    $fName = clean($fName);
    $sName = clean($sName);
    $email = clean($email);

    $sql = $conn->prepare("INSERT INTO messages (fname, sname, email, message) VALUES (?, ?, ?, ?)");

    $sql->bind_param("ssss",$fName,$sName,$email,$msg);

    if ($sql->execute()) {
        echo "<script>window.alert('Message sent successfully!')</script>";
    } else {
        $err = $sql->error;
        echo "<script>window.alert('Error. Message could not be sent: {$err}')</script>";
    }
?>