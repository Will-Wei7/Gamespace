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

$postshtml = "";
$pf_img = "images/defaultpfp.png";

function validate($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function get_posts($db, $mode, $filter = NULL)
{
  $sql = "SELECT user_profile.fname, user_profile.lname, posts.text,
      DATE_FORMAT(posts.date, '%c/%d/%Y') AS date, posts.user_id, category.title, posts.category_id,  postimg.path AS postimgpath,
      COALESCE(pfimg.path, 'images/defaultpfp.png') AS pfimgpath,
      games.id AS game_id, games.name AS game_name
    FROM posts
    LEFT JOIN user_profile ON user_profile.user_id = posts.user_id
    LEFT JOIN category ON posts.category_id = category.id
    LEFT JOIN uploads AS postimg ON posts.pic_id = postimg.id
    LEFT JOIN uploads AS pfimg ON user_profile.pic_id = pfimg.id
    LEFT JOIN games ON games.id = posts.game_id
    WHERE (posts.user_id IN (
      SELECT DISTINCT friend_id FROM friends WHERE user_id = :id
    ) OR (posts.user_id = :id))";

  if ($filter != NULL && $filter != 'all') {
    $sql .= " AND posts.category_id = :filter";
  }

  $sql .= " ORDER BY posts.date DESC";

  $stmt = $db->prepare($sql);

  if ($filter != NULL && $filter != 'all') {
    $stmt->execute(array(':id' => $_SESSION['id'], ':filter' => $filter));
  } else {
    $stmt->execute(array(':id' => $_SESSION['id']));
  }
  

  $posts_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($mode == 0) {
    $postshtml = "";

    foreach ($posts_data as $post) {
      $postshtml .= "<div class='post'><div class='post-small-content'>";

      if ($post['postimgpath'] != null) {
        $postshtml .= "<img class='post-image' src='./" . $post['postimgpath'] . "' alt='Post image' title='Open image'/>";
      }

      $postshtml .= "<img class='user-image' src='./" . $post['pfimgpath'] . "' alt='User picture' />";
      $postshtml .= "<a href='./profile.php?user_id=" . $post['user_id'] . "'><h3>" . $post['fname'] . " " . $post['lname'] . "</h3></a>";
      $postshtml .= "<h5> • " . $post['title'] . " • </h5>";

      if ($post['game_id'] != null) {
        $postshtml .= "<a href='./games.php?game_id=" . $post['game_id'] . "'><h3>" . $post['game_name'] . "</h3></a>";
      }

      $postshtml .= "<p>" . $post['text'] . "</p><h5 class='post-date'>" . $post['date'] . "</h5></div></div>";
    }
    return $postshtml;
  }

  return json_encode($posts_data, JSON_FORCE_OBJECT);
}

if (isset($_POST['submit']) && isset($_POST['text'])) {
  try {
    $pic_id = null;

    $text = validate($_POST['text']);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->beginTransaction();

    if (empty($text)) {
      throw new Exception('Please some post content');
    }

    if (strlen($text) > 4096) {
      throw new Exception('Post is too long');
    }

    if (isset($_FILES['upload-photo'])) {
      $next_id = $db->query("SHOW TABLE STATUS LIKE 'uploads'")->fetch(PDO::FETCH_ASSOC)['Auto_increment'];

      $fname = basename($_FILES['upload-photo']['name']);
      $ftype = strtolower(pathinfo($fname, PATHINFO_EXTENSION));

      $valid_types = array('jpg', 'png');

      if (!in_array($ftype, $valid_types)) {
        throw new Exception('Filetype must be: (jpg, png)');
      }

      $path = 'uploads/' . $next_id . '.' . $ftype;

      $stmt = $db->prepare("INSERT INTO uploads (path) VALUES (:path)");
      $stmt->execute(array(':path' => $path));

      $pic_id = $next_id;

      // If something other than 1 row was affected then it failed
      if ($stmt->rowCount() != 1) {
        throw new Exception('Failed to upload image');
      }

      if (!move_uploaded_file($_FILES['upload-photo']['tmp_name'], $path)) {
        throw new Exception('File transfer failed');
      }
    }

    $postshtml = "";

    $stmt = $db->prepare("INSERT INTO posts (id, user_id, date, text, category_id, pic_id, game_id)
    VALUES (NULL, :id, current_timestamp(), :texti, :cat, :pic_path, :game)");

    $game_id = ($_POST['gameinp'] == 0 ? null : $_POST['gameinp']);
    $stmt->execute(array(':id' => $_SESSION['id'], ':texti' => $_POST['text'], ':cat' => $_POST['categoryinp'], ':pic_path' => $pic_id, ':game' => $game_id));

    if ($stmt->rowCount() != 1) {
      throw new Exception('Error: Post submission failed');
    }

    $db->commit();
    unset($GLOBALS['_FILES']);
    echo get_posts($db, 1);
  } catch (Exception $e) {
    $db->rollBack();
    echo json_encode(array('errors' => $e->getMessage()));
    exit();
  }
} else if (isset($_POST['filter-submit'])) {
  echo get_posts($db, 1, $_POST['filter']);
} else {
?>

  <?php
  $post_data = get_posts($db, 0);

  $stmt = $db->prepare("SELECT user_profile.fname, user_profile.lname, friends.friend_id
  FROM user_profile
  RIGHT JOIN friends ON friends.friend_id = user_profile.user_id
  WHERE friends.user_id = :id");
  $stmt->execute(array(':id' => $_SESSION['id']));

  $friendshtml = "";
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $friend) {
    $friendshtml .= "<li><a href='./profile.php?user_id=" . $friend['friend_id'] . "'>" . $friend['fname'] . " " . $friend['lname'] . "</a></li>";
  }


  $stmt = $db->prepare("SELECT * FROM category");
  $stmt->execute();

  $categoryhtml = "";
  $categoryhtml2 = "<option value='all' selected>All</option>";
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $categoryhtml .= "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
    $categoryhtml2 .= "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
  }

  $stmt = $db->prepare("SELECT uploads.path FROM user_profile
  INNER JOIN uploads ON uploads.id = user_profile.pic_id
  WHERE user_profile.user_id = :id");
  $stmt->execute(array(':id' => $_SESSION['id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    $pf_img = $row['path'];
  }

  $stmt = $db->prepare("SELECT id, name FROM games");
  $stmt->execute();

  $gameshtml = "<option value='0' selected>None</option>";
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $gameshtml .= "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
  }
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Gamespace - Home</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="./styles/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/underscore@1.13.1/underscore-umd-min.js"></script>
    <script src="./scripts/script.js"></script>
  </head>

  <body>
    <div class="top-menu">
      <img src="./images/logo1.png" alt="Gamespace logo" />
      <button id="home-btn">Home</button>
      <button id="profile-btn" name=<?= "'" . $_SESSION['id'] . "'" ?>>Profile</button>
      <button id="friends-btn">Friends</button>
    </div>

    <div class="content">
      <button id="logout"><a href="./logout.php">Logout</a></button>

      <div class="home-container">
        <div class="make-post">
          <img src="<?= $pf_img ?>" alt="Profile picture" />
          <div class="post-container">

            <div class="clicked">
              <form action="index.php" id="make-post-form" method="post">
                <textarea type="text" name="text" id="make-post-text" rows="5" placeholder="Create a post..."></textarea>
                <span>
                  <label for="categoryinp">Category:</label>
                  <select name="categoryinp" id="categoryinp">
                    <?= $categoryhtml ?>
                  </select>

                  <label for="gameinip">Game:</label>
                  <select name="gameinp" id="gameinp">
                    <?= $gameshtml ?>
                  </select>

                  <input type="file" name="upload-photo" id="upload-photo">
                  <img class="upload-photo" id="upload-photo-btn" src="./images/upload-photo.jpg" alt="upload-photo" title="Upload image" />
                </span>
                <span>
                  <button type="button" id="close-make-post">Close</button>
                  <button type="submit" name="submit" id="post">Post</button>
                </span>
              </form>
            </div>

            <input class="un-clicked" type="text" placeholder="Create a post...">
          </div>
        </div>

        <div class="main-content">
          <div class="posts">
            <div id="posts-header" class="posts-header">
              <h2>Latest Posts</h2>
              <form action="index.php" id="filter-form" method="post">
                <button id="filter-submit" name="filter-submit" type="submit"></button>
                <select name="filter" class="post-filter" id="filter">
                  <?= $categoryhtml2 ?>
                </select>
              </form>
            </div>
            <!--Post/game achievment etc. will go here-->
            <?= $post_data ?>
          </div>
        </div>
      </div>

      <div class="sidebar">
        <div class="sidebar-container">
          <div class="header-container">
            <h2>Friends</h2>
            <div id="hp-fr" class="circle plus"></div>
          </div>
          <ul>
            <?= $friendshtml ?>
          </ul>
        </div>
      </div>
    </div>
  </body>

  </html>

<?php } ?>