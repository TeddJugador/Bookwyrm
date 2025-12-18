<!-- Munashe Madziwanyika - g23M8754
     Theodore Masi - g23M7028
     Kyle Nkomo - g23N8653
     Keith Dube - g23D5910 
-->

<?php

    require_once 'includes/connection.php';
    session_start();
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $error = $_GET['error'] ?? '';
    if(isset($_POST['reset'])){
        //fetch secret
        $username = $_SESSION['tmp_user'];
        $security = $conn->real_escape_string($_POST['security']);
        $tmp_pass = test_input($conn->real_escape_string($_POST['new-password']));
        $pass = test_input($conn->real_escape_string($_POST['confirmPassword']));
        if (strlen($tmp_pass) < 8) {
            header("Location: forgotPassword.php?error=password+must+be+at+least+8+characters");
            exit();
        }
        if ($tmp_pass !== $pass) {
            header("Location: forgotPassword.php?error=passwords+do+not+match");
            exit();
        }
        $new_pass = password_hash($tmp_pass, PASSWORD_DEFAULT);
        $sec = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $sec->bind_param("ss", $username, $username);
        $sec->execute();
        $sec_r = $sec->get_result();
        if ($sec_r){
            $row=$sec_r->fetch_assoc();
            if (password_verify($security, $row['security'])||$security==$row['security']){
                //update password
                $up = "UPDATE users SET password = '$new_pass' WHERE username = '$username' ";
                $up_r = $conn->query($up);
                if($up_r){
                    header('Location: loginForm.php?success=password+reset+successfully');
                    exit();
                }
                else{
                    header("Location: forgotPassword.php?error=password+not+updated");
                    exit();
                }
            }
            else{
                header("Location: forgotPassword.php?error=incorrect+security+key");
                exit();

            }
        }
        else{
            header("Location: forgotPassword.php?error=error+fetching+user");
            exit();
        }
    }
    if (isset($_POST['submit'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $retrieve = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $retrieve->bind_param("ss", $username, $username);
        $retrieve->execute();
        $result = $retrieve->get_result();
        if($result->num_rows>0){
            $_SESSION['tmp_user'] = $username;
            header('Location: forgotPassword.php');
        }
        else{
            header("Location: forgotPassword.php?error=username+or+email+does+not+exist");
            exit();
        }
  }
  

  

?>

<!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookWyrm Forgot Password</title>
    <link rel="icon" href="images/konLogo.png">
    <link rel="stylesheet" href="CSS/LoginSignin.css">
  </head>
  
  <body>
    <a href="home.php" class="back">Home</a>
    <form action="forgotPassword.php" method="POST" id="login-form">
      <h2>Reset Password</h2>
      <?php if ($error): ?>
        <span class="error" style="color: red;"><?php echo htmlspecialchars($error); ?></span>
      <?php endif; ?>
      <div class="input-box">
        
        <?php
            if (isset($_SESSION['tmp_user'])){
                echo '<input type="text" name="set_username" id="set_username" disabled value ="'.$_SESSION['tmp_user'].'" required>';
            }
            else{
                echo '<input type="text" name="username" id="username" required>';
                echo '<label for="username">Username</label>';
            }
        ?>
      </div>
      <?php 
        if (isset($_SESSION['tmp_user'])){?>
            <div class="input-box">
        <input type="text" name="security" id="security" required>
        <label for="security">Favourite Anime Character?</label>
      </div>
      <div class="input-box">
        <input type="password" name="new-password" id="new-password" required>
        <label for="new-password">New Password</label>
      </div>
      <div class="input-box">
        <input type="password" name="confirmPassword" id="confirmPassword" required>
        <label for="password">Confirm Password</label>
        <?php 
        }
        if (isset($_SESSION['tmp_user'])){?>
            <div class="buttons">
            <input type="submit" name="reset" value="Reset Password" id="login-btn">
        </div>
      <?php  }
        else{?>
            <div class="buttons">
        <input type="submit" name="submit" value="Find account" id="login-btn">
      </div>
       <?php }

      ?>
      
      <br>
      
      <div>
        <td colspan="2"><a href="signupForm.php">Don't have an account? Sign up</a>
      </div>
    </form>
    <script src="JS/signup.js"></script>
  </body>

</html>