<?php
session_start();

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "root";
$dbname = "gamespace";

$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword);

if (!$db) {
  echo "Connection failed!";
}

function validate($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if (isset($_POST['email']) && isset($_POST['password'])) {
  $email = validate($_POST['email']);
  $pass = validate($_POST['password']);


  if (empty($email)) {
    header("Location: login.php?error=Email is required");
    exit();
  }

  if (empty($pass)) {
    header("Location: login.php?error=Password is required");
    exit();
  }

  if (isset($_POST['sign-in'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE email=:email");
    $stmt->execute(array(':email' => $email));

    $row = $stmt->fetch();

    if (!$row) {
      header("Location: login.php?error=Email not found");
    }

    if ($row && password_verify($pass, $row['pswd'])) {
      echo "Logged in!";

      $_SESSION['email'] = $row['email'];
      $_SESSION['id'] = $row['id'];

      header("Location: index.php");
      exit();
    } else {
      header("Location: login.php?error=Incorrect credentials");
      exit();
    }
  } else if (isset($_POST['sign-up']) && isset($_POST['fname']) && isset($_POST['lname'])) {
    $fname = validate($_POST['fname']);
    $lname = validate($_POST['lname']);

    if (empty($fname)) {
      header("Location: login.php?error=First Name is required&signup=true");
      exit();
    }

    if (empty($lname)) {
      header("Location: login.php?error=Last Name is required&signup=true");
      exit();
    }

    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);

    try {
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $db->beginTransaction();

      $stmt = $db->prepare("INSERT INTO users (email, pswd) VALUES (:email, :pass)");
      $stmt->execute(array(':email' => $email, ':pass' => $pass_hash));

      $_SESSION['email'] = $email;
      $_SESSION['id'] = $db->lastInsertId();

      $stmt = $db->prepare("INSERT INTO user_profile (user_id, fname, lname) VALUES (:id, :fname, :lname)");
      $stmt->execute(array(':id' => $_SESSION['id'], ':fname' => $fname, ':lname' => $lname));

      $db->commit();

      echo "Logged in!";

      header("Location: index.php");
      exit();
    } catch (Exception $e) {
      $db->rollBack();
      if (str_contains($e->getMessage(), "Duplicate entry")) {
        header("Location: login.php?error=Email already in use&signup=true");
        exit();
      } else {
        header("Location: login.php?error=" . $e->getMessage() . "&signup=true");
        exit();
      }
    }
  } else {
    header("Location: login.php?error=System failure");
  }
}
