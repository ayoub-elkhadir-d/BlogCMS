<?php
session_start();
require_once 'function.php';

$islogin = isset($_SESSION['username']);
$action = $_GET['action'] ?? 'show_articles';

$sql = new PDO("mysql:host=localhost;dbname=blog;", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$users_stmt = $sql->prepare("SELECT user_name, password_hash, email, role FROM users");
$users_stmt->execute();
$all_users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

$articles_stmt = $sql->prepare("SELECT * FROM article");
$articles_stmt->execute();
$all_articles = $articles_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles/dashboard.css">
</head>
<body>
<?php if($islogin && $_SESSION['role'] == 'admin'): ?>

<nav class="navbar">
   
    <div class="navbar-top">
        <div class="nav-left">
            <div class="user-icon">👤</div>
            <div class="site-title">
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </div>
        </div>
        <div class="nav-right">
            <a href="display_post.php?mode=add_article"><button class="add-btn">Add Article</button></a>
            <a href="login.php?mode=null"><button class="logout-btn">Logout</button></a>
        </div>
    </div>
    <div class="navbar-bottom">
        <a href="index.php" class="nav-link">Home</a>
        <a href="?action=show_users" class="nav-link <?php echo $action=='show_users'?'active':''; ?>">Users</a>
        <a href="?action=show_articles" class="nav-link <?php echo $action=='show_articles'?'active':''; ?>">Articles</a>
        <a href="?action=show_comments" class="nav-link <?php echo $action=='show_comments'?'active':''; ?>">Comments</a>
        <a href="?action=show_category" class="nav-link">Category</a>
        <a href="?action=show_statistics" class="nav-link">Statistics</a>
    </div>
</nav>

<div class="container">
 
    <?php if ($action == 'show_users'){?>
        <h2>Users Management</h2>
<div class="table-wrapper">
    <table class="fl-table">
        <thead>
        <tr>
           <th>Name</th>
           <th>Email</th>
            <th>Role</th>
            <th>Password</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($all_users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['user_name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td class="password"><?php echo htmlspecialchars($user['password_hash']); ?></td>
            <td>
                <a href="display_post.php?postid=<?php echo $user['user_name']; ?>&mode=edit_user">
                    <button class="action-btn edit-btn">Edit</button>
                </a>
                <a href="display_post.php?postid=<?php echo $user['user_name']; ?>&mode=delet_user" onclick="return confirm('Are you sure?');">
                    <button class="action-btn delete-btn">Delete</button>
                </a>
            </td>
        </tr>
       <?php endforeach; ?>
        
        <tbody>
    </table>
      <?php }?>



    <?php if ($action == 'show_comments'){ 
        $all_comments = get_all_comments($sql);
        ?>
    <h2>Comment Management</h2>
   <div class="table-wrapper">
            <table class="fl-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Content</th>
                        <th>Created At</th>
                        <th>Post ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($all_comments as $comment): ?>
                    <tr>
                        <td class="post_id"><?php echo htmlspecialchars($comment['id_cmt']); ?></td>
                        <td class="content_cmt"><?php echo htmlspecialchars($comment['content_cmt']); ?></td>
                        <td><?php echo htmlspecialchars($comment['created_at_cmt']); ?></td>
                        <td class="post_id2"><?php echo htmlspecialchars($comment['post_id']); ?></td>
                        <td><?php echo htmlspecialchars($comment['status_cmt']); ?></td>
                       <?php if($comment['status_cmt']=='pending' || $comment['status_cmt']=='spam'){?>
                        <td>
                            <a href="display_post.php?postid=<?php echo $comment['id_cmt']; ?>&mode=accept_comment">
                                <button class="action-btn edit-btn">Accept</button>
                            </a>
                            <a href="display_post.php?postid=null&mode=delet_cmt&cmt_id=<?php echo $comment['id_cmt'] ?>">
                                <button class="action-btn delete-btn">Decline</button>
                            </a>
                        </td>
                    <?php }else{?>
                          <td>
                              <a href="display_post.php?postid=null&mode=add_cmt_to_spam&cmt_id=<?php echo $comment['id_cmt'] ?>">
                                  <button class="action-btn spam-btn" style=" background-color: #ff9900; color:white;">Add Spam</button>
                              </a>
                            <a href="display_post.php?postid=null&mode=delet_cmt&cmt_id=<?php echo $comment['id_cmt'] ?>">
                                <button class="action-btn delete-btn">Delet</button>
                            </a>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
 <?php }?>

    <?php if ($action == 'show_articles'){?>
        <h2>Articles Management</h2>
        <div class="table-wrapper">
            <table class="fl-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Image</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_articles as $article): ?>
                    <tr onclick="window.location='display_post.php?postid=<?php echo $article['id_art']; ?>&mode=display'" style="cursor:pointer">
                        <td><?php echo htmlspecialchars($article['id_art']); ?></td>
                        <td class="title-preview font_small">
                            <?php echo htmlspecialchars($article['titre_art']); ?></td>
                        <td class="content-preview font_small">
                            <?php echo htmlspecialchars($article['content_art']); ?></td>
                        <td>
                            <?php if (!empty($article['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article Image" class="img-preview">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </td>

                        <td class="font_small"><?php echo htmlspecialchars($article['date_cr_art']); ?></td>
                        <td class="font_small"><?php echo htmlspecialchars($article['date_up_art']); ?></td>
                        <td ><?php echo htmlspecialchars($article['category']); ?></td>
                        <td class="status-col"><?php echo htmlspecialchars($article['status_post']); ?></td>
                        <td><?php echo htmlspecialchars($article['view_count']); ?></td>
                        <td>
                            <a href="display_post.php?postid=<?php echo $article['id_art']; ?>&mode=edit">
                                <button class="action-btn edit-btn">Edit</button>
                            </a>
                            <a href="display_post.php?postid=<?php echo $article['id_art']; ?>&mode=delet" onclick="return confirm('Are you sure?');">
                                <button class="action-btn delete-btn">Delete</button>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php }?>

</div>
<?php else: ?>
    <p style="text-align:center; margin-top:50px; font-size:20px;">Admin Only</p>
<?php endif; ?>
</body>
</html>