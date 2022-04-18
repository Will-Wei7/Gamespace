<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit();
}

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "root";
$dbname = "gamespace";

$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword);

function validate($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if (isset($_POST['friends-search-submit']) && isset($_POST['friends-search'])) {
  $search = validate($_POST['friends-search']);

  if (empty($search)) {
    echo json_encode(array('errors' => 'Please provide a search parameter'));
    exit();
  }

  $search = $search . '%';
  addcslashes($search, '%_');

  $stmt = $db->prepare(
    'SELECT user_profile.user_id, user_profile.fname,
  user_profile.lname, COALESCE(user_profile.bio, "") AS bio,
  COALESCE(uploads.path, "images/defaultpfp.png") AS path
  FROM user_profile
  INNER JOIN users ON users.id = user_profile.user_id
  LEFT JOIN uploads ON uploads.id = user_profile.pic_id
  WHERE (user_profile.fname LIKE :search OR user_profile.lname LIKE :search OR users.email LIKE :search)
    AND user_id != :id
    AND user_id NOT IN (
      SELECT friend_id FROM friends WHERE user_id = :id
    )'
  );

  $stmt->execute(array(':search' => $search, ':id' => $_SESSION['id']));

  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (!$rows) {
    echo json_encode(array('errors' => 'No matching users found'));
    exit();
  }

  echo json_encode($rows);
} else if (isset($_POST['add-friend-btn']) && isset($_POST['friend-id'])) {
  $friend_id = validate($_POST['friend-id']);

  if (empty($friend_id)) {
    echo json_encode(array('errors' => 'Internal error: Friend not added'));
    exit();
  }

  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->beginTransaction();

  $stmt = $db->prepare("INSERT INTO friends (user_id, friend_id) VALUES (:id, :fid), (:fid, :id)");
  $stmt->execute(array(':id' => $_SESSION['id'], ':fid' => $friend_id));

  if ($stmt->rowCount() != 2) {
    $db->rollBack();
    echo json_encode(array('errors' => 'Internal error: Friend not added'));
    exit();
  }

  $db->commit();
  echo json_encode(array('msg' => 'Friend added'));
} else if (isset($_POST['delete-friend-btn']) && isset($_POST['friend-id'])) {
  $friend_id = validate($_POST['friend-id']);

  if (empty($friend_id)) {
    echo json_encode(array('errors' => 'Internal error: Friend not removed'));
    exit();
  }

  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->beginTransaction();

  $stmt = $db->prepare("DELETE FROM friends WHERE (user_id = :id AND friend_id = :fid) OR (user_id = :fid AND friend_id = :id)");
  $stmt->execute(array(':id' => $_SESSION['id'], ':fid' => $friend_id));

  if ($stmt->rowCount() != 2) {
    $db->rollBack();
    echo json_encode(array('errors' => 'Internal error: Friend not removed'));
    exit();
  }

  $db->commit();
  echo json_encode(array('msg' => 'Friend removed'));
}
