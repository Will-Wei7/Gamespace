<?php
session_start();
if (!isset($_SESSION['id']) && !isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

if (isset($_GET['user_id'])) {
  $id = $_GET['user_id'];
} else {
  $id = $_SESSION['id'];
}

if ($id == $_SESSION['id']) {
  $pf_mode = array("<a id='settings-button' href='#''>Edit Profile and Settings</a>", "<button class='circle plus'></button>");
} else {
  $pf_mode = array("", "", "");
}

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "root";
$dbname = "gamespace";

$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword);

if (!$db) {
  echo "Connection failed!";
}

function get_profile($db, $id)
{
  $stmt = $db->prepare('SELECT user_profile.fname, user_profile.lname, user_profile.bio,
    CASE WHEN uploads.path IS NOT NULL THEN uploads.path ELSE "images/defaultpfp.png" END AS path
  FROM user_profile
  LEFT JOIN uploads ON user_profile.pic_id = uploads.id
  WHERE user_id = :id');
  $stmt->execute(array(':id' => $id));

  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  $fullname = $user['fname'] . " " . $user['lname'];
  $bio = $user['bio'];
  $pf_img = "<img id='my-profile-pic' src='" . $user['path'] . "' alt='Profile picture' />";

  return array($fullname, $bio, $pf_img);
}

$profile_data = get_profile($db, $id);

$stmt = $db->prepare("SELECT user_profile.fname, user_profile.lname, posts.text, DATE_FORMAT(posts.date, '%c/%d/%Y') AS date, posts.user_id, category.title, postimg.path AS postimgpath,
    games.id AS game_id, games.name AS game_name
  FROM posts
  LEFT JOIN user_profile ON user_profile.user_id = posts.user_id
  LEFT JOIN category ON posts.category_id = category.id
  LEFT JOIN uploads AS postimg ON posts.pic_id = postimg.id
  LEFT JOIN games ON games.id = posts.game_id
  WHERE (posts.user_id = :id) ORDER BY posts.date DESC");
$stmt->execute(array(':id' => $id));

$posts_data = $stmt->fetchAll();

$postshtml = "";
foreach ($posts_data as $post) {
  $postshtml .= "<div class='post'><div class='post-small-content'>";

  if ($post['postimgpath'] != NULL) {
    $postshtml .= "<img class='post-image' src='" . $post['postimgpath'] . "' alt='Post image' />";
  }

  $postshtml .= "<a href='./profile.php?user_id=" . $id . "' />";
  $postshtml .= "<h3>" . $profile_data[0] . "</h3></a><h5> • " . $post['title'] . " • </h5>";

  if ($post['game_id'] != null) {
    $postshtml .= "<a href='./games.php?game_id=" . $post['game_id'] . "'><h3>" . $post['game_name'] . "</h3></a>";
  }

  $postshtml .= "<p>" . $post['text'] . "</p><h5 class='post-date'>" . $post['date'] . "</h5></div></div>";
}

$stmt = $db->prepare("SELECT user_profile.fname, user_profile.lname, friends.friend_id FROM user_profile
  RIGHT JOIN friends ON friends.friend_id = user_profile.user_id
  WHERE friends.user_id = :id");
$stmt->execute(array(':id' => $id));

$friendshtml = "";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $friend) {
  $frname = $friend['fname'] . " " . $friend['lname'];
  $friendshtml .= "<li><a href='./profile.php?user_id=" . $friend['friend_id'] . "'>" . $frname . "</a></li>";
}

$stmt = $db->prepare("SELECT games.name, games.id FROM games
  LEFT JOIN user_games ON games.id = user_games.game_id
  WHERE user_games.user_id = :id");
$stmt->execute(array(':id' => $id));

$gameshtml = "";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $game) {
  $gameshtml .= "<li><a href='./games.php?game_id=" . $game['id'] . "'>" . $game['name'] . "</a></li>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Gamespace - Profile</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="./styles/style.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/underscore@1.13.1/underscore-umd-min.js"></script>
  <script src="./scripts/script.js"></script>
</head>

<body>
  <button id="logout"><a href="./logout.php">Logout</a></button>

  <div class="top-menu">
    <img src="./images/logo1.png" alt="Gamespace logo" />
    <button id="home-btn">Home</button>
    <button id="profile-btn" name=<?= "'" . $_SESSION['id'] . "'" ?>>Profile</button>
    <button id="friends-btn">Friends</button>
  </div>

  <div class="content">

    <div class="main-content">
      <div class="profile">
        <?= $profile_data[2] ?>
        <h1 id="my-name"><?= $profile_data[0] ?></h1>
        <p id="my-bio"><?= $profile_data[1] ?></p>
        <?= $pf_mode[0] ?>
      </div>

      <div class="posts">
        <!--Post/game achievment etc. will go here-->
        <?= $postshtml ?>
      </div>
    </div>

    <div class="sidebar">
      <div id="games" class="sidebar-container">
        <div class="header-container">
          <h2>Games</h2>
        </div>
        <ul>
          <?= $gameshtml ?>
        </ul>
      </div>

      <hr />

      <div id="friends" class="sidebar-container">
        <div class="header-container">
          <h2>Friends</h2>
          <?= $pf_mode[1] ?>
        </div>
        <ul>
          <?= $friendshtml ?>
        </ul>
      </div>
    </div>
  </div>
</body>

</html>