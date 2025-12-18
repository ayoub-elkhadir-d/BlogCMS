<?php
session_start();
require_once 'function.php';

$islogin = isset($_SESSION['username']);
$action = $_GET['action'] ?? 'show_articles'; //this variabel get the action from lien 

$sql = new PDO("mysql:host=localhost;dbname=blog;", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);


$all_users = get_all_users_dashboard($sql);
$all_articles = get_all_articles_dashboard($sql);
$all_cat = get_all_categories($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="styles/dashboard.css">
</head>
<body>
<!-- this the main condaition if admine you can see  thois pge else you can not see-->
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
            <a href="display.php?mode=add_article"><button class="add-btn">Add Article</button></a>
            <a href="regester.php?mode=null"><button class="logout-btn">Logout</button></a>
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
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww Statistics  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<?php if ($action == 'show_statistics'){ ?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
        <div class="stat-value"><?php $count = total_articles($sql); echo $count['count']; ?></div>
        <div class="stat-label">Total Articles</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-comments"></i></div>
        <div class="stat-value"><?php $count = total_comments($sql); echo $count['count']; ?></div>
        <div class="stat-label">Total Comments</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-value"><?php $count = total_users($sql); echo $count['count']; ?></div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-tags"></i></div>
        <div class="stat-value"><?php $count = total_categorie($sql); echo $count['count']; ?></div>
        <div class="stat-label">Total Categories</div>
    </div>
</div>


<div class="top-section">
    <?php $topArt = get_top_user_articles($sql); ?>
    <div class="top-card">
        <div class="top-title"><i class="fas fa-trophy"></i> top user articles</div>
        <div class="top-item">
            <div class="user-info">
                <div class="user-avatar"><?= mb_substr($topArt['user_name'],0,1); ?></div>
                <div class="user-details"><h4><?= $topArt['user_name']; ?></h4></div>
            </div>
            <div class="top-value"><?= $topArt['total_articles']; ?></div>
        </div>
    </div>

    <?php $topCom = get_top_user_comments($sql); ?>
    <div class="top-card">
        <div class="top-title"><i class="fas fa-comments"></i> top user comments</div>
        <div class="top-item">
            <div class="user-info">
                <div class="user-avatar"><?= mb_substr($topCom['user_name'],0,1); ?></div>
                <div class="user-details"><h4><?= $topCom['user_name']; ?></h4></div>
            </div>
            <div class="top-value"><?= $topCom['total_comments']; ?></div>
        </div>
    </div>

    <?php $topPost = get_top_article_comments($sql); ?>
    <div class="top-card">
        <div class="top-title"><i class="fas fa-fire"></i> top article comments</div>
        <div class="top-item">
            <div class="user-info">
                <div class="user-avatar"><i class="fas fa-newspaper"></i></div>
                <div class="user-details"><h4><?= $topPost['titre_art']; ?></h4></div>
            </div>
            <div class="top-value"><?= $topPost['total_comments']; ?></div>
        </div>
    </div>
</div>
<?php } ?>
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww Show Users  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<?php if ($action == 'show_users'){?>
<h2>Users Management</h2>
<div class="table-wrapper">
    <table class="fl-table">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Password</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($all_users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td class="password"><?php echo htmlspecialchars($user['password_hash']); ?></td>
                <td>
                    <a href="display.php?postid=<?php echo $user['user_name']; ?>&mode=edit_user"><button class="action-btn edit-btn">Edit</button></a>
                    <a href="display.php?postid=<?php echo $user['user_name']; ?>&mode=delet_user" onclick="return confirm('Are you sure?');"><button class="action-btn delete-btn">Delete</button></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php } ?>
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww Show Category  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<?php if ($action == 'show_category'){?>
<div class="card1" style="display: flex; justify-content: space-evenly; align-items: center; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 5px;">
    <h2>Category Management</h2>
    <a href="display.php?mode=add_category"><button class="add-btn">Add Category</button></a>
</div>
<div class="table-wrapper">
    <table class="fl-table">
        <thead>
            <tr><th>titre_cat</th><th>description</th><th>action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($all_cat as $cat): ?>
            <tr>
                <td><?php echo htmlspecialchars($cat['titre_cat']); ?></td>
                <td><?php echo htmlspecialchars($cat['description']); ?></td>
                <td>
                    <a href="display.php?postid=<?php echo $cat['titre_cat']; ?>&mode=edit_category"><button class="action-btn edit-btn">Edit</button></a>
                    <a href="display.php?postid=<?php echo $cat['titre_cat']; ?>&mode=delet_category" onclick="return confirm('Are you sure?');"><button class="action-btn delete-btn">Delete</button></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php } ?>
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww Show Comments  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<?php if ($action == 'show_comments'){ 
    $all_comments = get_all_comments($sql);
?>
<h2>Comment Management</h2>
<div class="table-wrapper">
    <table class="fl-table">
        <thead>
            <tr><th>ID</th><th>Content</th><th>Created At</th><th>Post ID</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($all_comments as $comment): ?>
            <tr>
                <td><?php echo htmlspecialchars($comment['id_cmt']); ?></td>
                <td class="content_cmt"><?php echo htmlspecialchars($comment['content_cmt']); ?></td>
                <td><?php echo htmlspecialchars($comment['created_at_cmt']); ?></td>
                <td><?php echo htmlspecialchars($comment['post_id']); ?></td>
                <td><?php echo htmlspecialchars($comment['status_cmt']); ?></td>
                <td>
                    <?php if($comment['status_cmt']=='pending' || $comment['status_cmt']=='spam'): ?>
                        <a href="display.php?postid=<?php echo $comment['id_cmt']; ?>&mode=accept_comment"><button class="action-btn edit-btn">Accept</button></a>
                    <?php else: ?>
                        <a href="display.php?postid=null&mode=add_cmt_to_spam&cmt_id=<?php echo $comment['id_cmt'] ?>"><button class="action-btn spam-btn" style="background-color: #ff9900; color:white;">Add Spam</button></a>
                    <?php endif; ?>
                    <a href="display.php?postid=null&mode=delet_cmt&cmt_id=<?php echo $comment['id_cmt'] ?>"><button class="action-btn delete-btn">Delete</button></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php } ?>
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww Show articles  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<!--wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww-->
<?php if ($action == 'show_articles'){?>
<h2>Articles Management</h2>
<div class="table-wrapper">
    <table class="fl-table">
        <thead>
            <tr><th>ID</th><th>Title</th><th>Content</th><th>Image</th><th>Created At</th><th>Updated At</th><th>Category</th><th>Status</th><th>Views</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($all_articles as $article): 
                $category = get_categorie_name_by_id($sql,$article['category']);
            ?>
            <tr onclick="window.location='display.php?postid=<?php echo $article['id_art']; ?>&mode=display'" style="cursor:pointer">
                <td><?php echo htmlspecialchars($article['id_art']); ?></td>
                <td class="title-preview font_small"><?php echo htmlspecialchars($article['titre_art']); ?></td>
                <td class="content-preview font_small"><?php echo htmlspecialchars($article['content_art']); ?></td>
                <td>
                    <?php if (!empty($article['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article Image" class="img-preview">
                    <?php else: ?>
                        <span>No Image</span>
                    <?php endif; ?>
                </td>
                <td class="font_small"><?php echo htmlspecialchars($article['date_cr_art']); ?></td>
                <td class="font_small"><?php echo htmlspecialchars($article['date_up_art']); ?></td>
                <td><?php echo htmlspecialchars($category['titre_cat']) ?></td>
                <td><?php echo htmlspecialchars($article['status_post']); ?></td>
                <td><?php echo htmlspecialchars($article['view_count']); ?></td>
                <td>
                    <a href="display.php?postid=<?php echo $article['id_art']; ?>&mode=edit"><button class="action-btn edit-btn">Edit</button></a>
                    <a href="display.php?postid=<?php echo $article['id_art']; ?>&mode=delet" onclick="return confirm('Are you sure?');"><button class="action-btn delete-btn">Delete</button></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php } ?>

</div>
<?php else: ?>
    <p style="text-align:center; margin-top:50px; font-size:20px;">Admin Only</p>
<?php endif; ?>
</body>
</html>

<?php


