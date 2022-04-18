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

if (!$db) {
  echo "Connection failed!";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Gamespace - Friend</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/underscore@1.13.1/underscore-umd-min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
  <link href="./styles/style.css" rel="stylesheet">
  <script src="./scripts/script.js"></script>
</head>

<body>
  <button id="logout"><a href="./logout.php">Logout</a></button>

  <div class="top-menu">
    <img src="./images/logo1.png" alt="Gamespace logo" />
    <button id="home-btn">Home</button>
    <button id="profile-btn">Profile</button>
    <button id="friends-btn">Friends</button>
  </div>

  <div id="search-wrapper">
    <div class="bar">
      <form action="friends_db.php" id="friends-search-form">
        <input class="searchbar" type="text" placeholder="Search to find new friends!" name="friends-search" id="friends-search" title="Search">
        <button type="submit" name="friends-search-submit">Search</button>
      </form>
    </div>
  </div>

  <div class="collapse-wrapper" id="friends-search-collapse">
    <div class="collapse-header" data-content="Search Results"><i class="fa fa-fw fa-chevron-down"></i>Search Results</div>
    <div id="friends-search-res"></div>
  </div>

  <div class="collapse-wrapper">
    <div class="collapse-header" data-content="Reccommended Friends"><i class="fa fa-fw fa-chevron-down"></i>Reccommended Friends</div>
    <div id="reccomended-friend-list">
      <?php
      $stmt = $db->prepare('SELECT reccommended.user_id, reccommended.fname,
        reccommended.lname, COALESCE(reccommended.bio, "") AS bio,
        COALESCE(uploads.path, "images/defaultpfp.png") AS path,
        friend.fname AS friend_fname, friend.lname AS friend_lname
      FROM user_profile AS reccommended
      LEFT JOIN uploads ON uploads.id = reccommended.pic_id
      LEFT JOIN (
        SELECT a.fname, a.lname, friends.friend_id
        FROM user_profile AS a
        RIGHT JOIN friends ON friends.user_id = a.user_id
        WHERE a.user_id IN (
          SELECT f.friend_id FROM friends AS f WHERE f.user_id = :id)
      ) AS friend ON friend.friend_id = reccommended.user_id
      WHERE reccommended.user_id IN (
      SELECT friend_id FROM friends
        WHERE user_id IN (
          SELECT friend_id FROM friends WHERE user_id = :id)
        AND friend_id != :id
        AND friend_id NOT IN (SELECT friend_id FROM friends WHERE user_id = :id))
      GROUP BY reccommended.user_id ORDER BY RAND() LIMIT 6');

      $stmt->execute(array(':id' => $_SESSION['id']));

      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($rows as $user) { ?>
        <div class='profile-small'>
          <div class='hidden-id'><?php echo $user['user_id']; ?></div>
          <img alt='Profile Picture' class='profile-pic-small' src='<?php echo $user['path']; ?>'>
          <div class="profile-wrapper">
            <div class='user-name'><a href='./profile.php?user_id=<?php echo $user['user_id']?>'>
              <?php  echo $user['fname'] . ' ' . $user['lname']; ?></a><small>(friends with <?php echo $user['friend_fname'] . ' ' . $user['friend_lname'] ?>)</small></div>
            <p class='bio-small'><?php echo $user['bio']; ?></p>
          </div>
          <button title="Add <?php echo $user['fname'] . ' ' . $user['lname']; ?> as a friend" class="add-friend-btn circle plus add-img"></button>
        </div>
      <?php } ?>
    </div>
  </div>

  <div class="collapse-wrapper">
    <div class="collapse-header" data-content="Current Friends"><i class="fa fa-fw fa-chevron-down"></i>Current Friends</div>
    <div id="friends-list" class="collapse">
      <?php
      $stmt = $db->prepare('SELECT user_profile.user_id, user_profile.fname,
    user_profile.lname, COALESCE(user_profile.bio, "") AS bio,
    COALESCE(uploads.path, "images/defaultpfp.png") AS path
    FROM user_profile
    RIGHT JOIN friends ON friends.user_id = :id
    LEFT JOIN uploads ON uploads.id = user_profile.pic_id
    WHERE user_profile.user_id = friends.friend_id');

      $stmt->execute(array(':id' => $_SESSION['id']));

      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($rows as $user) { ?>
        <div class='profile-small'>
          <div class='hidden-id'><?php echo $user['user_id']; ?></div>
          <img alt='Profile Picture' class='profile-pic-small' src='<?php echo $user['path']; ?>'>
          <div class="profile-wrapper">
            <div class='user-name'><a href='./profile.php?user_id=<?php echo $user['user_id']?>'>
              <?php echo $user['fname'] . ' ' . $user['lname']; ?></a></div>
            <p class='bio-small'><?php echo $user['bio']; ?></p>
          </div>
          <button title="Unfriend <?php echo $user['fname'] . ' ' . $user['lname']; ?>" class="delete-friend-btn circle plus add-img"></button>
        </div>
      <?php } ?>
    </div>
  </div>
</body>

</html>