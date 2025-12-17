<?php
session_start();
?>
<?php
$islogin = false;
if(isset($_SESSION['username'])){
   $islogin=true; 
}
?>
<?php
$sql = new PDO("mysql:host=localhost;dbname=blog;","root","",[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$mode   = $_GET['mode']   ?? 'display';
$postid = $_GET['postid'] ?? null;

require_once 'function.php'

?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8" />
<title>Post UI</title>
<link rel="stylesheet" type="text/css" href="styles/display.css">
</head>
<body>
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
      <?php if($islogin && isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author')): ?>

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
    <?php }else{ echo '';}?>
 </div>
</nav>
<?php 
$post = $sql->prepare("SELECT * FROM article WHERE id_art = ?");
$post->execute([$postid]);

$post_comments = $sql->prepare("SELECT * FROM comment WHERE post_id = ? and status_cmt='approved'");
$post_comments->execute([$postid]);

$category_names = $sql->prepare("SELECT titre_cat FROM category");
$category_names->execute();

$roles_names = $sql->prepare("SELECT DISTINCT role FROM users");
$roles_names->execute();


$post_data =$post -> fetchAll(PDO::FETCH_ASSOC);
$all_roles =$roles_names -> fetchAll(PDO::FETCH_ASSOC);
$post_comments_ =$post_comments -> fetchAll(PDO::FETCH_ASSOC);
$all_categories =$category_names -> fetchAll(PDO::FETCH_ASSOC);

?>
 <!-- ============================================================ -->
<?php if($mode=='add_article'){?>
<form class="post-form" method="POST" action="display_post.php?postid=null&mode=save_article">
    <div class="form-group">
        <label for="username">User Name</label>
        <input type="text" id="username" name="username" class="form-input" readonly>
    </div>
    
    <div class="form-group">
        <label for="created_at">Date Create</label>
        <input type="datetime-local" id="created_at" name="created_at"  class="form-input">
    </div>
    
    <div class="form-group">
        <label for="img_url">Image Url</label>
        <input type="text" id="img_url" name="img_url" class="form-input" placeholder="Link Image">
    </div>
    
    <div class="form-group">
        <label for="category">Category</label>
             <select name="category" class="form-select">
            <?php foreach($all_categories as $cat){ ?>
            <option  value="<?php echo $cat['titre_cat'] ?>"><?php echo $cat['titre_cat'] ?></option>
             <?php } ?>
         </select>
    </div>
    
    <div class="form-group">
        <label for="title">Titre</label>
        <input type="text" id="title" name="title"  class="form-input" placeholder="Write titre..">
    </div>
    
    <div class="form-group">
        <label for="content">Content</label>
        <textarea id="content" name="content" class="form-textarea" placeholder="Write..."></textarea>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="submit-btn">Save</button>
    </div>
</form>
    <?php } ?>

<?php foreach($post_data as $data){  ?>
    <!-- ============================================================ -->
     
    <?php if($mode=='display'){?>
<div class="post">
    <div class="post-header">
        <div class="user-info">
        <strong>@<?php echo $data['id_user'] ?></strong> · <span> <?php echo $data['date_up_art'] ?> </span> . <span> <?php echo $data['category'] ?> </span>
        </div>
        <?php  
       if($islogin && isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author')){ ?>

        <div class="actions">
           <a href="display_post.php?postid=<?php echo $postid?>&mode=edit"> <button class="edit">Edit</button> </a>
          <a href="display_post.php?postid=<?php echo $postid?>&mode=delet">  <button class="delete">Delete</button> </a>
        </div>

<?php }else{echo '';}?> 

    </div>

    <div class="post-image" style="margin:15px 0;">
        <img class="img_dis" src="<?php echo $data['image_url'] ?>" alt="post image" >
    </div>

    <div class="post-title"><?php echo $data['titre_art'] ?></div>
    <div class="post-content">
<?php echo $data['content_art'] ?>
    </div>


 <div class="add-comment" style="display:flex;gap:10px;margin-top:20px;">
    <form method="POST" action="display_post.php?postid=<?php echo $postid?>&mode=comment_submit">
        <input name="comment" type="text" placeholder="اكتب تعليقك..." style="flex:1;padding:8px;border-radius:5px;border:1px solid #ccc;">
       <button type="submit" style="padding:8px 15px;border:none;border-radius:5px;background:#0d6efd;color:#fff;cursor:pointer;">Save</button>
       </form>
    </div>

   
 <div class="comments">
    <?php foreach($post_comments_ as $cmt){ ?>
        <div class="comment">
            <?php $id_mt= $cmt['id_cmt'] ?>
            <div class="comment-user"><?php echo $cmt['user_name'] ?></div>
            <div class="comment-content"><?php echo $cmt['content_cmt'] ?></div>
        <?php  
if($islogin && $_SESSION['role'] == 'admin'){ ?>
               <div class="comment-actions">
               <a href="display_post.php?postid=<?php echo $postid?>&mode=delet_cmt&cmt_id=<?php echo $id_mt ?>"> <button class="delete">حذف</button></a>
            </div>

<?php }else{echo '';}?> 

        </div>
    <?php } ?>

</div>
<?php }?>
  </div>
 
 <!-- ============================================================ -->
<?php if($mode=='edit'){?>
<form class="post-form" method="POST" action="display_post.php?postid=<?php echo $postid?>&mode=updated">
    <div class="form-group">
        <label for="username">User Name</label>
        <input type="text" id="username" name="username" value="<?php echo $data['id_user'] ?>" class="form-input" readonly>
    </div>
    
    <div class="form-group">
        <label for="created_at">Date Create</label>
        <input type="datetime-local" id="created_at" name="created_at" value="<?php echo $data['date_up_art'] ?>" class="form-input">
    </div>
    
    <div class="form-group">
        <label for="img_url">Image Url</label>
        <input type="text" id="img_url" name="img_url" value="<?php echo $data['image_url'] ?>" class="form-input" placeholder="Link Image">
    </div>
    
    <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="category" class="form-select">
            <?php foreach($all_categories as $cat){ ?>
            <option value="<?php echo $cat['titre_cat'] ?>" <?php if($cat['titre_cat'] == $data['category']) echo 'selected'; ?>><?php echo $cat['titre_cat'] ?></option>
            <?php } ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="title">Titre</label>
        <input type="text" id="title" name="title" value="<?php echo $data['titre_art'] ?>" class="form-input" placeholder="Write titre..">
    </div>
    
    <div class="form-group">
        <label for="content">Content</label>
        <textarea id="content" name="content" class="form-textarea" placeholder="Write..."><?php echo $data['content_art']?></textarea>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="submit-btn">Save</button>
    </div>
</form>
    <?php } ?>

    <?php } ?>
    
    <?php if($mode=='edit_user'){
        $user = get_user_byid($sql,$postid);?>

<form class="post-form" method="POST" action="display_post.php?postid=<?php echo $postid?>&mode=save_update_user">
    <div class="form-group">
        <label for="username">User Name</label>
        <input type="text" id="username" name="username" value="<?php echo $user['user_name']?>" class="form-input" readonly>
    </div>
    <div class="form-group">
        <label for="username">Email</label>
        <input type="text" id="username" name="email" value="<?php echo $user['email']?>" class="form-input"required="">
    </div>
    <div class="form-group">
        <label for="username">Password</label>
        <input type="text" id="username" name="pass" value="<?php echo $user['password_hash']?>" class="form-input" required="" >
    </div>

<div class="form-group">
        <label for="category">Role</label>
        <select id="category" name="role" class="form-select">
            <?php foreach($all_roles as $Role){ ?>
            <option value="<?php echo $Role['role'] ?>"> <?php echo $Role['role'] ?> </option>
            <?php } ?>
        </select>
    </div>
    <div class="form-actions">
        <button type="submit" class="submit-btn">Save</button>
    </div>

</form>
    <?php } ?> 
<!-- ======================== functions ============================ -->
    
    <?php if($mode=='delet'){
    $username = $postid;

    $stmt = $sql->prepare("DELETE FROM comment WHERE post_id = ?");
    $stmt->execute([$username]);

    $stmt = $sql->prepare("DELETE FROM article WHERE id_art = ?");
    $stmt->execute([$username]);
        header("Location: index.php");
        exit;
    }?>
     <!-- ============================================================ -->

<?php if($mode=='save_update_user'){
    $user     = $_POST["username"];
    $pass     = $_POST["pass"];
    $email    = $_POST["email"];
    $role  = $_POST["role"];
    update_user_byid($sql, $user,$pass,$email,$role);
    header("Location: dashboard.php?action=show_users");
   }?>
  
  
    <?php if($mode=='delet_user'){
     $username = $postid;

    $stmt = $sql->prepare("DELETE FROM comment WHERE user_name = ?");
    $stmt->execute([$username]);

    $stmt = $sql->prepare("UPDATE article SET id_user = NULL WHERE id_user = ?");
    $stmt->execute([$username]);

    $stmt = $sql->prepare("DELETE FROM users WHERE user_name = ?");
    $stmt->execute([$username]);

    header("Location: dashboard.php?action=show_users");
    exit;
        
    }?>


 <!--============================================================ -->
<?php if($mode=='updated'){ ?>

<?php

$user     = $_POST["username"];
$date     = $_POST["created_at"];
$title    = $_POST["title"];
$content  = $_POST["content"];
$cat      = $_POST["category"];
$img_url  = $_POST["img_url"];


$stmt = $sql->prepare("
    UPDATE article SET 
        titre_art   = :title,
        content_art = :content,
        image_url   = :img,
        date_up_art = :date,
        category  = :cat
    WHERE id_art = :id
");

$stmt->execute([
    ':title'   => $title,
    ':content' => $content,
    ':img'     => $img_url,
    ':date'    => $date,
    ':cat'     => $cat,
    ':id'      => $postid
]);


header("Location: index.php");
exit;
?> 
<?php } ?>
 <!-- ============================================================ -->
 <?php if($mode=='comment_submit'){
     
     $comment = $_POST["comment"];
     $user = null;
     if(isset($_SESSION['username'])){
       $user = $_SESSION['username'];
     }
    
     $stmt = $sql->prepare("INSERT INTO comment (content_cmt, created_at_cmt, post_id,user_name,status_cmt) VALUES ('$comment', NOW(), $postid,'$user','pending');");
     $stmt->execute();
        header("Location: display_post.php?postid=$postid&mode=display");
  
    }?>
    <!-- ============================================================ -->
 <?php if($mode=='delet_cmt'){
     $comment_id = $_GET["cmt_id"];
     $stmt = $sql->prepare("DELETE FROM comment WHERE id_cmt = $comment_id;");
     $stmt->execute();
     header("Location: dashboard.php?action=show_comments");
    }?>
    
 <?php if($mode=='accept_comment'){
     $comment_id = $_GET["postid"];
     $stmt = $sql->prepare("UPDATE comment set status_cmt = 'approved' WHERE id_cmt = $comment_id;");
     $stmt->execute();
     header("Location: dashboard.php?action=show_comments");
    }?>

 <?php if($mode=='add_cmt_to_spam'){
     $comment_id = $_GET["cmt_id"];
     $stmt = $sql->prepare("UPDATE comment set status_cmt = 'spam' WHERE id_cmt = $comment_id;");
     $stmt->execute();
     header("Location: dashboard.php?action=show_comments");
    }?>
     <!-- ============================================================ -->
    <!-- ============================================================ -->
   <?php if($mode=='save_article'){
  
   $user     = $_POST["username"];
   $date     = $_POST["created_at"];
   $title    = $_POST["title"];
   $content  = $_POST["content"];
   $cat      = $_POST["category"];
   $img_url  = $_POST["img_url"];
   
   
   $stmt = $sql->prepare("
   INSERT INTO article 
   (titre_art, content_art, image_url, date_cr_art, date_up_art, id_user, category, status_post, view_count)
   VALUES 
   (:title, :content, :img, NOW(), NOW(), :user, :cat, 'published', 1245)
   ");
   
   $stmt->execute([
       ':title'   => $title,
       ':content' => $content,
       ':img'     => $img_url,
       ':user'    => $_SESSION['username'],
       ':cat'     => $cat
   ]);
   
   header("Location: index.php");
   
   
   }
   
   ?> 

</body>
</html>