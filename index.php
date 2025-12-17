<?php
session_start();
?>
<?php
$islogin = false;
if(isset($_SESSION['username'])){
   $islogin=true; 
}
require_once 'function.php';
?>
<?php

$sql = new PDO("mysql:host=localhost;dbname=blog;","root","");
if($sql){
  echo "ok";
}

$data = $sql->prepare("SELECT * from article");
$data ->execute();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>My Blog</title>
<link rel="stylesheet" type="text/css" href="styles/index.css">

</head>

<body>

<!-- ===== Navbar ===== -->
<nav class="navbar">
  <div class="navbar-top">
    <div class="nav-left">
      <div class="user-icon">👤</div>
      <div class="site-title">
        <?php
        if(isset($_SESSION['username'])){
          echo $_SESSION['username'];
        } else {
          echo 'User';
        }
        ?>
      </div>
    </div>
    <div class="nav-right">

      <?php
     
       if($islogin && isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author')):
        ?>
        <a href="display_post.php?mode=add_article"><button class="add-btn">Add article</button></a>
      <?php endif; ?>
      <?php if($islogin): ?>
       <a href="login.php?mode=null"> <button class="logout-btn">Logout</button></a>
      <?php else: ?>
        <div class="auth-buttons">
          <a href="login.php?mode=login"><button class="login-btn">Login</button></a>
          <a href="login.php?mode=signup"><button class="create-btn">Create Account</button></a>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="navbar-bottom">
    <a href="index.php" class="nav-link">Home</a>
       <?php
       if($islogin && isset($_SESSION['role']) && $_SESSION['role'] == 'admin' ){
        ?>
    <a href="dashboard.php?action=show_users" class="nav-link">Dashboard</a>

    <?php }else{ echo '';} ?>

  </div>
</nav>

<!-- ===== Blog Cards ===== -->
<div class="blog-container">
<?php
foreach($data as $art) {
    $post_id = $art['id_art'];
    $coment_count = get_count_comments_in_post($sql,$post_id);
    ?>
<a href="display_post.php?postid=<?php echo $post_id ?>&mode=display">
    <div class="blog-card" id="post_<?php echo $post_id; ?>">
        <div class="menu-dots">⋮</div>
        <img src="<?php echo htmlspecialchars($art['image_url']); ?>">
        <div class="card-content">
            <div class="card-title"><?php echo htmlspecialchars($art['titre_art']); ?></div>
            <div class="card-footer">
                <div class="date"><?php echo htmlspecialchars($art['date_up_art']); ?></div>
                <div class="comments">
                    <span>💬</span>
                    <span><?php echo $coment_count; ?></span>
                </div>
            </div>
        </div>
    </div>
</a>

<?php } ?>

</div>


</body>
</html>