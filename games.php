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

if (!isset($_GET['game_id'])) {
  $follow_btn = "<p>No game provided</p>";
} else {
  $game_id = $_GET['game_id'];
  $stmt = $db->prepare("SELECT game_id FROM user_games WHERE user_id = :id AND game_id = :game_id");
  $stmt->execute(array(':id' => $_SESSION['id'], ':game_id' => $_GET['game_id']));
  $row = $stmt->fetch();

  $txt = ($row ? 'unfollow' : 'follow');
  $follow_btn = "<a id='" . $txt . "' data-gameid='" . $game_id . "'>" . ucfirst($txt) . "</a>";
}

function get_game($db, $game_id)
{
  $stmt = $db->prepare('SELECT games.id, games.name, games.about,
    COALESCE(uploads.path, "images/defaultpfp.png") AS path
    FROM games
    LEFT JOIN uploads ON games.pic_id = uploads.id
    WHERE games.id = :game_id');

  $stmt->execute(array(':game_id' => $game_id));

  $game = $stmt->fetch(PDO::FETCH_ASSOC);
  $name = $game['name'];
  $about = $game['about'];
  $game_img = "<img id='my-profile-pic' src='" . $game['path'] . "' alt='Profile picture' />";

  return array($name, $about, $game_img);
}

$game_data = get_game($db, $game_id);

$stmt = $db->prepare("SELECT user_profile.fname, user_profile.lname, posts.text, posts.game_id, games.name,
    DATE_FORMAT(posts.date, '%c/%d/%Y') AS date, posts.user_id, category.title, postimg.path AS postimgpath,
    COALESCE(pfimg.path, 'images/defaultpfp.png') AS pfimgpath
  FROM posts
  LEFT JOIN user_profile ON user_profile.user_id = posts.user_id
  LEFT JOIN category ON posts.category_id = category.id
  LEFT JOIN uploads AS postimg ON posts.pic_id = postimg.id
  LEFT JOIN uploads AS pfimg ON user_profile.pic_id = pfimg.id
  LEFT JOIN games ON games.id = posts.game_id
  WHERE posts.game_id = :id ORDER BY posts.date DESC");

$stmt->execute(array(':id' => $game_id));

$posts_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$postshtml = "";
foreach ($posts_data as $post) {
  $postshtml .= "<div class='post'><div class='post-small-content'>";

  if ($post['postimgpath'] != NULL) {
    $postshtml .= "<img class='post-image' src='" . $post['postimgpath'] . "' alt='Post image' />";
  }

  $postshtml .= "<img class='user-image' src='./" . $post['pfimgpath'] . "' alt='User picture' />";
  $postshtml .= "<a href='./profile.php?user_id=" . $_SESSION['id'] . "' />";
  $postshtml .= "<h3>" . $post['fname'] . " " . $post['lname'] . "</h3></a><h5> • " . $post['title'] . " • </h5>";
  $postshtml .= "<a href='./games.php?game_id=" . $post['game_id'] . "'><h3>" . $post['name'] . "</h3></a><p>" . $post['text'] . "</p><h5 class='post-date'>" . $post['date'] . "</h5></div></div>";
}

$stmt = $db->prepare("SELECT user_profile.user_id, user_profile.fname, user_profile.lname FROM user_games
  RIGHT JOIN user_profile ON user_profile.user_id = user_games.user_id
  WHERE user_games.game_id = :id");
$stmt->execute(array(':id' => $game_id));

$followershtml = "";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
  $frname = $row['fname'] . " " . $row['lname'];
  $followershtml .= "<li><a href='./profile.php?user_id=" . $row['user_id'] . "'>" . $frname . "</a></li>";
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
        <?= $game_data[2] ?>
        <h1 id="my-name"><?= $game_data[0] ?></h1>
        <p id="my-bio"><?= $game_data[1] ?></p>
        <?= $follow_btn ?>
      </div>

      <div class="posts">
        <!--Post/game achievment etc. will go here-->
        <?= $postshtml ?>
      </div>
    </div>

    <div class="sidebar">
      <div id="followers" class="sidebar-container">
        <div class="header-container">
          <h2>Followers</h2>
        </div>
        <ul>
          <?= $followershtml ?>
        </ul>
      </div>
    </div>
  </div>
</body>

</html>