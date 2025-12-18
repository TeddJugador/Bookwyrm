<?php

  require 'includes/connection.php';

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }


  if (isset($_POST['submit'])) {
    $username = test_input($conn->real_escape_string($_POST['username']));
    $firstname = test_input($conn->real_escape_string($_POST['firstname']));
    $lastname = test_input($conn->real_escape_string($_POST['lastname']));
    $email = test_input($conn->real_escape_string($_POST['email']));
    $phone = test_input($conn->real_escape_string($_POST['phone']));
    $role = "Reader";
    $password = test_input($conn->real_escape_string($_POST['password']));
    $confirmPassword = test_input($conn->real_escape_string($_POST['confirmPassword']));
  }

  if (empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($password)) {
    header("Location: signupForm.php?error=all+fields+are+required");
    exit();
  }

  if (!preg_match("/^[a-zA-Z'-]+$/", $firstname) || !preg_match("/^[a-zA-Z'-]+$/", $lastname)) {
    header("Location: signupForm.php?error=names+can+only+contain+letters,+apostrophes,+and+hyphens");
    exit();
  }

  if (!preg_match("/^\+?\d{10,15}$/", $phone)) {
    header("Location: signupForm.php?error=invalid+contact+number+format");
    exit();
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signupForm.php?error=invalid+email+format");
    exit();
  }

  if (strlen($password) < 8) {
    header("Location: signupForm.php?error=password+must+be+at+least+8+characters");
    exit();
  }
  if ($password !== $confirmPassword) {
    header("Location: signupForm.php?error=passwords+do+not+match");
    exit();
  }


  $password = password_hash($password, PASSWORD_DEFAULT);

  $retrieve = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? OR contact_number = ?");
  $retrieve->bind_param("sss", $username, $email, $phone);
  $retrieve->execute();
  $result = $retrieve->get_result();

  if ($result->num_rows > 0) {
    header("Location: signupForm.php?error=user+already+exists+.+Go+to+login");
    exit();
  }

  $sql = $conn->prepare("INSERT INTO users (username, first_name, last_name, contact_number, email, password,role) VALUES (?, ?, ?, ?, ?, ?,?)");
  $sql->bind_param("sssssss", $username, $firstname, $lastname, $phone, $email, $password, $role);
  $sql->execute();

  header("Location: loginForm.php?signup=success");

  $conn->close();

?>