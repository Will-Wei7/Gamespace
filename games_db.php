<?php
session_start();
if (!isset($_SESSION['id']) && !isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "root";
$dbname = "gamespace";

$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword);

if (!$db) {
  echo "Connection failed!";
}

if (isset($_POST['follow']) && isset($_POST['gameid'])) {
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->beginTransaction();

  $stmt = $db->prepare("INSERT INTO user_games (user_id, game_id) VALUES (:id, :game_id)");
  $stmt->execute(array('id' => $_SESSION['id'], 'game_id' => $_POST['gameid']));

  if ($stmt->rowCount() != 1) {
    $db->rollBack();
    echo json_encode(array('errors' => 'Internal error: Could not follow'));
    exit();
  }

  $db->commit();
  echo json_encode(array("msg" => "Followed!"));
} else if (isset($_POST['unfollow']) && isset($_POST['gameid'])) {
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->beginTransaction();

  $stmt = $db->prepare("DELETE FROM user_games WHERE (user_id = :id AND game_id = :game_id)");
  $stmt->execute(array('id' => $_SESSION['id'], 'game_id' => $_POST['gameid']));

  if ($stmt->rowCount() != 1) {
    $db->rollBack();
    echo json_encode(array('errors' => 'Internal error: Could not unfollow'));
    exit();
  }

  $db->commit();
  echo json_encode(array("msg" => "Unfollowed!"));
}
?>