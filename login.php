<?php

  require 'includes/connection.php';
  session_start();

  $action = $_GET['action'] ?? '';
  $_SESSION['attempts'] = $_SESSION['attempts'] ?? 0;

  if (isset($_POST['submit'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
  }

  $retrieve = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
  $retrieve->bind_param("ss", $username, $username);
  $retrieve->execute();
  $result = $retrieve->get_result();


  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
      $_SESSION['attempts']++;
      $status = "success";
      $logsql = $conn->prepare("INSERT INTO userlogs (username, time, status, attempts)
                            VALUES (?, NOW(), ?, ?)");
      $logsql->bind_param("ssi", $row['username'], $status, $_SESSION['attempts']);

      $logsql->execute();
      unset($_SESSION['attempts']);

      $get_log = "SELECT log_id FROM userlogs ORDER BY log_id DESC limit 1";
      $_SESSION['log_id'] = $conn->query($get_log)->fetch_assoc()['log_id'];

      $_SESSION['username'] = $row['username'];
      $_SESSION['firstname'] = $row['first_name'];
      $_SESSION['lastname'] = $row['last_name'];
      $_SESSION['role'] = $row['role'];

    header("Location: redirect.php?action=$action");
    exit();

    } else {
      $_SESSION['attempts']++;
      $status = "failed";
      $logsql = $conn->prepare("INSERT INTO userlogs (username, time, status, attempts)
                            VALUES (?, NOW(), ?, ?)");
      $logsql->bind_param("ssi", $row['username'], $status, $_SESSION['attempts']);

      $logsql->execute();
      header("Location: loginForm.php?error=incorrect+username+or+password");
      exit();
    }
  } else {
    header("Location: loginForm.php?error=incorrect+username+or+password");
    exit();
  }

  $conn->close();

?>