<?php 
    //deletes a message from the table
    require '../includes/connection.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = $conn->prepare("DELETE FROM messages WHERE message_id = ?");
        $sql->bind_param("i", $id);
        $sql->execute();

        if ($sql->affected_rows > 0) {
            header("Location: AdminDash.php?success=Message deleted successfully");
        } else {
            header("Location: AdminDash.php?error=Failed to delete message");
        }
    } else {
        header("Location: AdminDash.php?error=Invalid request");
    }

?>