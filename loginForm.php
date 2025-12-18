<!-- Munashe Madziwanyika - g23M8754
     Theodore Masi - g23M7028
     Kyle Nkomo - g23N8653
     Keith Dube - g23D5910 
-->

<?php
  session_start();
  // Get any error messages from the login process
  $error = $_GET['error'] ?? '';
  $success = $_GET['success'] ?? '';
  $signup = $_GET['signup'] ?? '';
  $action = $_GET['action'] ?? '';
  if ($signup === 'success') {
    echo '<script>alert("Signup successful! Please log in.");</script>';
  }
?>

<!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookWyrm Log In</title>
    <link rel="icon" href="images/konLogo.png">
    <link rel="stylesheet" href="CSS/LoginSignin.css">
  </head>
  
  <body>
    <a href="home.php" class="back">Home</a>
    <form action="login.php?action=<?php echo htmlspecialchars($action); ?>" method="POST" id="login-form">
      <h2>Log In</h2>
      <?php if ($error): ?>
        <span class="error" style="color: red;"><?php echo htmlspecialchars($error); ?></span>
      <?php endif; ?>
      <?php if ($success): ?>
        <span class="error" style="color: green;"><?php echo htmlspecialchars($success); ?></span>
      <?php endif; ?>
      <div class="input-box">
        <input type="text" name="username" id="username" required>
        <label for="username">Username</label>
      </div>

      <div class="input-box">
        <input type="password" name="password" id="password" required>
        <label for="password">Password</label>
      </div>
      <p onclick="showPassword()" id="show">Show Password</p>
      
      <br>
      <!--Forgot Password-->
      <div>
        <?php 
          if (isset($_SESSION['attempts'])&&($_SESSION['attempts']>=1)){
            echo "<a href=forgotPassword.php>Forgot Password?</a>";
          }
        ?>
      </div>

      <div class="buttons">
        <input type="submit" name="submit" value="Log In" id="login-btn">
      </div>
      
      <br>
      
      <div>
        <td colspan="2"><a href="signupForm.php">Don't have an account? Sign up</a>
      </div>
    </form>
    <script src="JS/signup.js"></script>
  </body>

</html>