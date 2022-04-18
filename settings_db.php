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

function emailExists($db, $email)
{
  $stmt = $db->prepare("SELECT 1 FROM users WHERE email = :email");
  $stmt->execute(array(':email' => $email));
  return $stmt->fetchColumn();
}

if (isset($_POST['settings-bio']) && isset($_POST['bio'])) {
  $bio = validate($_POST['bio']);

  if (empty($bio)) {
    echo json_encode(array('errors' => 'Bio not provided'));
    exit();
  } else if (strlen($bio) > 2048) {
    echo json_encode(array('errors' => 'Bio too long'));
    exit();
  }

  try {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE user_profile SET bio = :bio WHERE user_id = :id");
    $stmt->execute(array(':bio' => $bio, ':id' => $_SESSION['id']));

    // If something other than 1 row was affected then it failed
    if ($stmt->rowCount() != 1) {
      $db->rollBack();
      echo json_encode(array('errors' => 'Failed to update bio'));
      exit();
    } else {
      $db->commit();
      echo json_encode(array('msg' => 'Bio updated', 'update_id' => 'my-bio', 'update_val' => $bio));
    }
  } catch (Exception $e) {
    $db->rollBack();
    echo json_encode(array('errors' => $e->getMessage()));
  }
} else if (isset($_POST['settings-email']) && isset($_POST['email'])) {
  $email = validate($_POST['email']);

  if (empty($email)) {
    echo json_encode(array('errors' => 'Email not provided'));
    exit();
  } else if (strlen($email) > 255) {
    echo json_encode(array('errors' => 'Email too long'));
    exit();
  }

  if (emailExists($db, $email)) {
    echo json_encode(array('errors' => 'Email already used'));
    exit();
  }

  try {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE users SET email = :email WHERE id = :id");
    $stmt->execute(array(':email' => $email, ':id' => $_SESSION['id']));

    // If something other than 1 row was affected then it failed
    if ($stmt->rowCount() != 1) {
      $db->rollBack();
      echo json_encode(array('errors' => 'Failed to update email'));
      exit();
    } else {
      $db->commit();
      echo json_encode(array('msg' => 'Email updated'));
    }
  } catch (Exception $e) {
    $db->rollBack();
    echo json_encode(array('errors' => $e->getMessage()));
  }
} else if (isset($_POST['settings-password']) && isset($_POST['password'])) {
  $password = validate($_POST['password']);

  if (empty($password)) {
    echo json_encode(array('errors' => 'Password not provided'));
    exit();
  } else if (strlen($password) > 255) {
    echo json_encode(array('errors' => 'Password too long'));
    exit();
  } else if (strlen($password) < '13') {
    echo json_encode(array('errors' => 'Password must be at least 13 characters long'));
    exit();
  } else if (!preg_match("#[0-9]+#", $password)) {
    echo json_encode(array('errors' => 'Password must contain at least 1 number'));
    exit();
  } else if (!preg_match("#[A-Z]+#", $password)) {
    echo json_encode(array('errors' => 'Password must contain at least one capital letter'));
    exit();
  } else if (!preg_match("#[a-z]+#", $password)) {
    echo json_encode(array('errors' => 'Password must contain at least one lowercase letter'));
    exit();
  }

  try {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->beginTransaction();

    $pass_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("UPDATE users SET pswd = :password WHERE id = :id");
    $stmt->execute(array(':password' => $pass_hash, ':id' => $_SESSION['id']));

    // If something other than 1 row was affected then it failed
    if ($stmt->rowCount() != 1) {
      $db->rollBack();
      echo json_encode(array('errors' => 'Failed to update password'));
      exit();
    } else {
      $db->commit();
      echo json_encode(array('msg' => 'Password updated'));
    }
  } catch (Exception $e) {
    $db->rollBack();
    echo json_encode(array('errors' => $e->getMessage()));
    exit();
  }
} else if (isset($_POST['settings-pic']) && isset($_FILES['profile-pic'])) {
  $next_id = $db->query("SHOW TABLE STATUS LIKE 'uploads'")->fetch(PDO::FETCH_ASSOC)['Auto_increment'];

  $fname = basename($_FILES['profile-pic']['name']);
  $ftype = strtolower(pathinfo($fname, PATHINFO_EXTENSION));

  $valid_types = array('jpg', 'png');

  if (!in_array($ftype, $valid_types)) {
    echo json_encode(array('errors' => 'Filetype must be: (jpg, png)'));
    exit();
  }

  $path = 'uploads/' . $next_id . '.' . $ftype;

  try {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->beginTransaction();

    $stmt = $db->prepare("INSERT INTO uploads (path) VALUES (:path)");
    $stmt->execute(array(':path' => $path));

    $stmt = $db->prepare("UPDATE user_profile SET pic_id = :upload_id WHERE user_id = :id");
    $stmt->execute(array(':upload_id' => $db->lastInsertId(), ':id' => $_SESSION['id']));

    // If something other than 1 row was affected then it failed
    if ($stmt->rowCount() != 1) {
      throw new Exception('Failed to upload image');
    }

    if (!move_uploaded_file($_FILES['profile-pic']['tmp_name'], $path)) {
      throw new Exception('File transfer failed');
    }

    $db->commit();
    unset($GLOBALS['_FILES']);
    echo json_encode(array('msg' => 'Image uploaded', 'update_id' => 'my-profile-pic', 'update_val' => $path));
  } catch (Exception $e) {
    $db->rollBack();
    echo json_encode(array('errors' => $e->getMessage()));
    exit();
  }
}
