<!-- Munashe Madziwanyika - g23M8754
     Theodore Masi - g23M7028
     Kyle Nkomo - g23N8653
     Keith Dube - g23D5910 
-->

<?php
  // Get any error messages from the signup process
  $error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookWyrm Log In</title>
    <link rel="icon" href="images/konLogo.png">
    <link rel="stylesheet" href="CSS/LoginSignin.css">
    <!-- <link rel="stylesheet" href="index.css"> -->
    <script src="signup.js"></script>
  </head>
  
  <body onoffline="offline()">
    <a href="home.php" class="back">Home</a>
    <form action="signup.php" method="POST" id="signup-form" onsubmit="return validateSignUp()">
      <h2>Sign Up</h2>
      <?php if ($error): ?>
        <span class="error" style="color: red;"><?php echo htmlspecialchars($error); ?></span>
      <?php endif; ?>
      <div class="names">
        <div class="input-box">
          <input type="text" name="firstname" id="firstname" maxlength="50" oninput="validateNames()" required>
          <label for="firstname">First Name</label>
        </div>
        <div class="input-box">
          <input type="text" name="lastname" id="lastname" maxlength="50" oninput="validateNames()" required>
          <label for="lastname">Last Name</label>
        </div>
      </div>
      <div class="contacts">
        <div class="input-box">
          <input type="tel" name="phone" id="phone" inputmode="numeric" onchange="validateNumber()" required>
          <label for="phone">Contact Number</label>
        </div>
        <div class="input-box">
          <input type="email" name="email" id="email" required>
          <label for="email">Email Address</label>
        </div>
      </div>
  
      <div class="input-box">
        <input type="text" name="username" id="username" required>
        <label for="username">Username</label>
      </div>
      <div class="input-box">
        <input type="password" name="password" id="password" required>
        <label for="password">Password</label>
      </div>
      <div class="input-box">
        <input type="password" name="confirmPassword" id="confirmPassword" required>
        <label for="password">Confirm Password</label>
      </div>
      <br>
      <div class="buttons">
        <input type="submit" name="submit" id="signup-btn" value="Sign Up">
        <input type="reset" name="reset" id="reset-btn">
      </div>

      <br>
      <span id="message"></span>

      <br>
      <div class="login">
        <a href="loginForm.php?action=l">Already have an account? Log In</a>
      </div>
    </form>
  </body>

</html>