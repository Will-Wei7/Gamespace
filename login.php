<!DOCTYPE html>
<html lang="en">

<head>
  <title>Gamespace - Login</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="./styles/login.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/underscore@1.13.1/underscore-umd-min.js"></script>
  <script src="./scripts/script.js"></script>
</head>

<body>
  <div class="content <?php if (isset($_GET['signup'])) { echo "right-panel-active"; } ?>">
    <div class="form-container sign-up-container">
      <form action="login_db.php" method="post">
        <h1>Create Account</h1>

        <span>or use your email for registration</span>
        <?php if (isset($_GET['error']) && isset($_GET['signup'])) { ?>
          <p class="error"><?php echo $_GET['error']; ?></p>
        <?php } ?>
        <input type="text" name="fname" placeholder="First Name" />
        <input type="text" name="lname" placeholder="Last Name" />
        <input type="email" name="email" placeholder="Email" />
        <input type="password" name="password" placeholder="Password" />
        <button type="submit" name="sign-up">Sign Up</button>
      </form>
    </div>

    <div class="form-container sign-in-container">
      <form action="login_db.php" method="post">
        <h1>Sign in</h1>

        <span>or use your account</span>
        <?php if (isset($_GET['error']) && !isset($_GET['signup'])) { ?>
          <p class="error"><?php echo $_GET['error']; ?></p>
        <?php } ?>
        <input type="email" name="email" placeholder="Email" />
        <input type="password" name="password" placeholder="Password" />
        <a href="#">Forgot your password?</a>
        <button type="submit" name="sign-in">Sign In</button>
      </form>
    </div>

    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Welcome Back!</h1>
          <p>To keep connected with us please login with your personal info</p>
          <button class="ghost" id="sign-in">Sign In</button>
        </div>

        <div class="overlay-panel overlay-right">
          <h1>Welcome to Gamespace!</h1>
          <p>We are excited to help you connect and explore the world of games</p>
          <button class="ghost" id="sign-up">Sign Up</button>
        </div>
      </div>
    </div>
  </div>
</body>

</html>