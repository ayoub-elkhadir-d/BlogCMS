<?php
session_start();
$islogin = false;
if (isset($_SESSION['username'])) {
    $islogin = true;
}
$sql = new PDO("mysql:host=localhost;dbname=blog;", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$mode   = $_GET['mode']   ?? 'display';
$postid = $_GET['postid'] ?? null;

require_once 'function.php';
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
                    if (isset($_SESSION['username'])) {
                        echo $_SESSION['username'];
                    } else {
                        echo 'User';
                    }
                    ?>
                </div>
            </div>
            
            <div class="nav-right">
                <?php if ($islogin && isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author')): ?>
                    <a href="display.php?mode=add_article"><button class="add-btn">Add article</button></a>
                <?php endif; ?>
                <?php if ($islogin): ?>
                    <a href="regester.php?mode=null"> <button class="logout-btn">Logout</button></a>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="regester.php?mode=login"><button class="login-btn">Login</button></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="navbar-bottom">
            <a href="index.php" class="nav-link">Home</a>
            
            <?php if ($islogin && isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author')) { ?>
                <a href="dashboard.php?action=show_users" class="nav-link">Dashboard</a>
            <?php } ?>
        </div>
    </nav>

    <?php
 
    $post_data = get_article($sql, $postid);
    $post_comments_ = get_approved_comments_by_post($sql, $postid);
    $all_categories = get_all_categories($sql);
    $all_roles = get_all_roles($sql);
    ?>
<!--""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""-->
<!--""""""""""""""""""""""""""""""" Article manage """"""""""""""""""""""""""""""""""""""""""""-->
<!--""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""-->

<!--""""""""""""""""""""""""""""""""""""""Add Article"""""""""""""""""""""""""""""""""""""""""""-->
    <?php if ($mode == 'add_article' && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author')) { ?>
        <form class="post-form" method="POST" action="display.php?postid=null&mode=save_article">
            <div class="form-group">
                <label for="username">User Name</label>
                <input type="text" id="username" name="username" class="form-input" readonly>
            </div>
            <div class="form-group">
                <label for="created_at">Date Create</label>
                <input type="datetime-local" id="created_at" name="created_at" class="form-input">
            </div>
            <div class="form-group">
                <label for="img_url">Image Url</label>
                <input type="text" id="img_url" name="img_url" class="form-input" placeholder="Link Image">
            </div>
            <!--get categories-->
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" class="form-select">
                    <?php foreach ($all_categories as $cat) { ?>
                        <option value="<?php echo $cat['titre_cat'] ?>"><?php echo $cat['titre_cat'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" class="form-input" placeholder="Write titre..">
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


    <?php foreach ($post_data as $post) {  ?>
        <?php
        $cate = get_categorie_name_by_id($sql, $post['category']);
        $user = get_user_name_by_id($sql, $post['id_user']);
        ?>
<!--""""""""""""""""""""""""""""""""""""""Dispaly Article"""""""""""""""""""""""""""""""""""""""""""-->
        <?php if ($mode == 'display') { ?>
            <div class="post">
                <div class="post-header">
                    <div class="user-info">
                        <strong>@<?php echo $user['user_name'] ?></strong> · <span> <?php echo $post['date_up_art'] ?> </span> . <span> <?php echo $cate['titre_cat']  ?> </span>
                    </div>
                    <?php if ($islogin && isset($_SESSION['role']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){ ?>
                        <div class="actions">
                            <a href="display.php?postid=<?php echo $postid ?>&mode=edit"> <button class="edit">Edit</button> </a>
                            <a href="display.php?postid=<?php echo $postid ?>&mode=delet"> <button class="delete">Delete</button> </a>
                        </div>
                    <?php } ?>
                </div>

                <div class="post-image" style="margin:15px 0;">
                    <img class="img_dis" src="<?php echo $post['image_url'] ?>" alt="post image">
                </div>

                <div class="post-title"><?php echo $post['titre_art'] ?></div>
                <div class="post-content">
                    <?php echo $post['content_art'] ?>
                </div>

                <div class="add-comment" style="display:flex;gap:10px;margin-top:20px;">
                    <form method="POST" action="display.php?postid=<?php echo $postid ?>&mode=comment_submit">
                        <input name="comment" type="text" placeholder="Write comment ...." style="flex:1;padding:8px;border-radius:5px;border:1px solid #ccc;">
                        <button type="submit" style="padding:8px 15px;border:none;border-radius:5px;background:#0d6efd;color:#fff;cursor:pointer;">Save</button>
                    </form>
                </div>

                <div class="comments">
                    <?php foreach ($post_comments_ as $cmt) { 
                        $user = get_user_name_by_id($sql,$cmt['user_name']);
                        ?>
                        <div class="comment">
                            <?php $id_mt = $cmt['id_cmt'] ?>
                            <div class="comment-user"><?php
                                echo ($cmt['user_name'] == null) ? '@Visiteur' :  $user['user_name'];
                            ?></div>
                            <div class="comment-content"><?php echo $cmt['content_cmt'] ?></div>
                            <?php if ($islogin && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                                <div class="comment-actions">
                                    <a href="display.php?postid=<?php echo $postid ?>&mode=delet_cmt&cmt_id=<?php echo $id_mt ?>"> <button class="delete">حذف</button></a>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            </div>
<!--""""""""""""""""""""""""""""""""""""""Edit Article"""""""""""""""""""""""""""""""""""""""""""-->
            <?php if ($mode == 'edit' && isset($_SESSION['role'])&&($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author')) {
                $user = get_user_name_by_id($sql, $post['id_user']);?>
                <form class="post-form" method="POST" action="display.php?postid=<?php echo $postid ?>&mode=updated">
                    <div class="form-group">
                        <label for="username">User Name</label>
                        <input type="text" id="username" name="username" value="@<?php echo $user['user_name'] ?>" class="form-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="created_at">Date Create</label>
                        <input type="datetime-local" id="created_at" name="created_at" value="<?php echo $post['date_up_art'] ?>" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="img_url">Image Url</label>
                        <input type="text" id="img_url" name="img_url" value="<?php echo $post['image_url'] ?>" class="form-input" placeholder="Link Image">
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-select">
                            <?php foreach ($all_categories as $cat) { ?>
                                <option value="<?php echo $cat['id'] ?>"><?php echo $cat['titre_cat'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" id="title" name="title" value="<?php echo $post['titre_art'] ?>" class="form-input" placeholder="Write titre..">
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" class="form-textarea" placeholder="Write..."><?php echo $post['content_art'] ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">Save</button>
                    </div>
                </form>
            <?php } ?>
    <?php } ?>
<!--""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""-->
<!--""""""""""""""""""""""""""""""" User manage """"""""""""""""""""""""""""""""""""""""""""-->
<!--""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""-->

<!--""""""""""""""""""""""""""""""""""""""Edit user"""""""""""""""""""""""""""""""""""""""""""-->

    <?php if ($mode == 'edit_user' && isset($_SESSION['role'])  && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $user = get_user_byid($sql, $postid); ?>
        <form class="post-form" method="POST" action="display.php?postid=<?php echo $postid ?>&mode=save_update_user">
            <div class="form-group">
                <label for="username">User Name</label>
                <input type="text" id="username" name="username" value="<?php echo $user['user_name'] ?>" class="form-input" readonly>
            </div>
            <div class="form-group">
                <label for="username">Email</label>
                <input type="text" id="username" name="email" value="<?php echo $user['email'] ?>" class="form-input" required="">
            </div>
            <div class="form-group">
                <label for="username">Password</label>
                <input type="text" id="username" name="pass" value="<?php echo $user['password_hash'] ?>" class="form-input" required="">
            </div>
            <div class="form-group">
                <label for="category">Role</label>
                <select id="category" name="role" class="form-select">
                    <?php foreach ($all_roles as $Role) { ?>
                        <option value="<?php echo $Role['role'] ?>"> <?php echo $Role['role'] ?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">Save</button>
            </div>
        </form>
    <?php } ?>
<!--""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""-->
<!--""""""""""""""""""""""""""""""" Category manage """"""""""""""""""""""""""""""""""""""""""""-->
<!--""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""-->

    <?php if (($mode == 'edit_category' || $mode == 'add_category') && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { 
        $category = get_categorie_by_id($sql, $postid) ?> <!-- get category name witch id -->
        
        <form class="post-form" method="POST" action="display.php?postid=<?php echo $postid ?>&mode=<?php echo ($mode == 'edit_category') ? 'save_update_category' : 'save_add_category'; ?>">
            <div class="form-group">
                <label for="username">Titre</label>
                <input type="text" id="username" name="titre" value="<?php echo ($mode == 'edit_category') ? $category['titre_cat'] : ''; ?>" class="form-input" required="">
            </div>
            <div class="form-group">
                <label for="username">Description</label>
                <input type="text" id="username" name="description" value="<?php echo ($mode == 'edit_category') ? $category['description'] : ''; ?>" class="form-input" required="">
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">Save</button>
            </div>
        </form>
    <?php } ?>
<!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->


<!--mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm-->
<!--mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm[functions mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm-->
<!--mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm-->


<!--mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm Article function mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm-->
<!-- delet article -->
    <?php
   if ($mode == 'delet' && isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author')) {
        delete_article($sql, $postid);
        header("Location: index.php");
        exit;
    }
//    update article
    if ($mode == 'updated' && isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'author') ) {
        update_article($sql, $_POST["title"], $_POST["content"], $_POST["img_url"], $_POST["created_at"], $_POST["category"], $postid);
        header("Location: index.php");
        exit;
    }
    //    add article
  if ($mode == 'save_article' && isset($_SESSION['role']) &&  ($_SESSION['role'] == 'admin'  || $_SESSION['role'] == 'author')) {
        $user_id_data = get_user_id_by_name($sql, $_SESSION['username']); //get user id witch this methode 
        $get_cat_id = get_categorie_id_by_iname($sql, $_POST["category"]);
        add_article($sql, $_POST["title"], $_POST["content"], $_POST["img_url"], $user_id_data['id'], $get_cat_id['id']);
        header("Location: index.php");
    }

//wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww User function  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww

//oooooooooooooooo  save updated user oooooooooooooooooo
    if ($mode == 'save_update_user' && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        update_user_byid($sql, $_POST["username"], $_POST["pass"], $_POST["email"], $_POST["role"]);
        header("Location: dashboard.php?action=show_users");
    }
//oooooooooooooooo  delet user  oooooooooooooooooo
    if ($mode == 'delet_user'  && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        delete_user_by_name($sql, $postid);
        header("Location: dashboard.php?action=show_users");
        exit;
    }

//wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww Comment function  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww


//oooooooooooooooo  add new comment  oooooooooooooooooo
    if ($mode == 'comment_submit') {
        $comment = $_POST["comment"]; //get comment content from methode post 

        if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {  //check if user is login set this name in db else set null 
            add_comment_anonymous($sql, $comment, $postid); //add comment witch null value 
        } else {
            $user_id_data = get_user_id_by_name($sql, $_SESSION['username']);
            add_comment_user($sql, $comment, $postid, $user_id_data['id']);  //add comment witch username value 
        }
        header("Location: display.php?postid=$postid&mode=display");
    }
//oooooooooooooooo  delete comment  oooooooooooooooooo
    if ($mode == 'delet_cmt' && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        delete_comment_by_id($sql, $_GET["cmt_id"]);
        header("Location: dashboard.php?action=show_comments");
    }
//oooooooooooooooo  accept comment  oooooooooooooooooo

//if addmin mark un comment as spam this comment not show but admin onmy can accept this comment if click in accept this comment show agine
    if ($mode == 'accept_comment' && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        approve_comment_by_id($sql, $_GET["postid"]);
        header("Location: dashboard.php?action=show_comments");
    }
// admin can add commentsz as spam

    if ($mode == 'add_cmt_to_spam' && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        add_cmt_to_spam($sql, $_GET["cmt_id"]);
        header("Location: dashboard.php?action=show_comments");
    }
//wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww Category function  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww


//oooooooooooooooo  update category  oooooooooooooooooo

    if ($mode == 'save_update_category'  && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $get_cat_id = get_categorie_id_by_iname($sql, $postid);
        update_category($sql, $get_cat_id['id'], $_POST['titre'], $_POST['description']);
        header("Location: dashboard.php?action=show_category");
    }
//oooooooooooooooo  add category  oooooooooooooooooo
    if ($mode == 'save_add_category'  && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        add_category($sql, $_POST['titre'], $_POST['description']);
        header("Location: dashboard.php?action=show_category");
    }
//oooooooooooooooo  delete category  oooooooooooooooooo
    if ($mode == 'delet_category'  && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $get_cat_id = get_categorie_id_by_iname($sql, $postid);
        delet_category($sql, $get_cat_id['id']);
        header("Location: dashboard.php?action=show_category");
    }
//wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww
  
    ?>

</body>
</html>
