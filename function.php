
<?php
// ======================== functions ============================




//============================Role============================ /
function get_all_roles($sql) {
    $stmt = $sql->prepare("SELECT DISTINCT role FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//===========================user==================================== /
function get_user_byid($sql, $user_name) {
    $stmt = $sql->prepare("SELECT * FROM users WHERE user_name = ?");
    $stmt->execute([$user_name]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function update_user_byid($sql, $user_name, $password, $email, $role) {
    $stmt = $sql->prepare("UPDATE users set email=?, password_hash=?, role=? WHERE user_name = ?");
    $stmt->execute([$email, $password, $role, $user_name]);
}

function get_user_id_by_name($sql, $name) {
    $stmt = $sql->prepare("SELECT id FROM users WHERE user_name = ?");
    $stmt->execute([$name]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_user_name_by_id($sql, $id) {
    $stmt = $sql->prepare("SELECT user_name FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function delete_user_by_name($sql, $name) {
    $stmt = $sql->prepare("DELETE FROM users WHERE user_name = ?");
    $stmt->execute([$name]);
}

//============================Category============================ /

function get_categorie_by_id($sql, $id) {
    $stmt = $sql->prepare("SELECT * from category where titre_cat=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_all_categories($sql) {
    $stmt = $sql->prepare("SELECT * from category");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_categorie_id_by_iname($sql, $name) {
    $stmt = $sql->prepare("SELECT id from category where titre_cat=?");
    $stmt->execute([$name]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function get_categorie_name_by_id($sql, $id) {
    $stmt = $sql->prepare("SELECT titre_cat from category where id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function add_category($sql, $name, $desc) {
    $stmt = $sql->prepare("INSERT into category (titre_cat,description) VALUES (?, ?)");
    $stmt->execute([$name, $desc]);
}

function delet_category($sql, $id) {
    $stmt = $sql->prepare("DELETE FROM category WHERE id = ?");
    $stmt->execute([$id]);
}
function update_category($sql, $idd, $titre_cat, $description) {
    $stmt = $sql->prepare("UPDATE category SET titre_cat = ?, description = ? WHERE id = ?");
    $stmt->execute([$titre_cat, $description, $idd]);
}

//============================comment============================ /
function get_all_comments($sql) {
    $stmt = $sql->prepare("SELECT * from comment");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_approved_comments_by_post($sql, $postid) {
    $stmt = $sql->prepare("SELECT * FROM comment WHERE post_id = ? and status_cmt='approved'");
    $stmt->execute([$postid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_count_comments_in_post($sql, $post_id) {
    $comments = $sql->prepare("SELECT COUNT(*) FROM comment WHERE post_id = ? and status_cmt='approved'");
    $comments->execute([$post_id]);
    return $comments->fetchColumn();
}
function add_cmt_to_spam($sql, $comment_id) {
    $stmt = $sql->prepare("UPDATE comment set status_cmt = 'spam' WHERE id_cmt = ?");
    $stmt->execute([$comment_id]);
}

function delete_comment_by_id($sql, $id) {
    $stmt = $sql->prepare("DELETE FROM comment WHERE id_cmt = ?");
    $stmt->execute([$id]);
}

function approve_comment_by_id($sql, $id) {
    $stmt = $sql->prepare("UPDATE comment set status_cmt = 'approved' WHERE id_cmt = ?");
    $stmt->execute([$id]);
}

function add_comment_anonymous($sql, $comment, $postid) {
    $stmt = $sql->prepare("INSERT INTO comment (content_cmt, created_at_cmt, post_id, user_name, status_cmt) VALUES (?, NOW(), ?, null, 'approved')");
    $stmt->execute([$comment, $postid]);
}

function add_comment_user($sql, $comment, $postid, $user_id) {
    $stmt = $sql->prepare("INSERT INTO comment (content_cmt, created_at_cmt, post_id, user_name, status_cmt) VALUES (?, NOW(), ?, ?, 'approved')");
    $stmt->execute([$comment, $postid, $user_id]);
}
//============================article============================ /
function update_article($sql, $title, $content, $img_url, $date, $cat, $postid) {
    $stmt = $sql->prepare("UPDATE article SET titre_art = :title, content_art = :content, image_url = :img, date_up_art = :date, category = :cat WHERE id_art = :id");
    $stmt->execute([':title' => $title, ':content' => $content, ':img' => $img_url, ':date' => $date, ':cat' => $cat, ':id' => $postid]);
}
function add_article($sql, $title, $content, $img_url, $user, $cat) {
    $stmt = $sql->prepare("INSERT INTO article (titre_art, content_art, image_url, date_cr_art, date_up_art, id_user, category, status_post, view_count) VALUES (:title, :content, :img, NOW(), NOW(), :user, :cat, 'published', 0)");
    $stmt->execute([':title' => $title, ':content' => $content, ':img' => $img_url, ':user' => $user, ':cat' => $cat]);
}

function get_article($sql, $postid) {
    $post = $sql->prepare("SELECT * FROM article WHERE id_art = ?");
    $post->execute([$postid]);
    return $post->fetchAll(PDO::FETCH_ASSOC);
}
function delete_article($sql, $id) {
    $stmt = $sql->prepare("DELETE FROM article WHERE id_art = ?");
    $stmt->execute([$id]);
}
//============================dashboard============================ /
function get_all_users_dashboard($sql) {
    $stmt = $sql->prepare("SELECT user_name, password_hash, email, role FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_all_articles_dashboard($sql) {
    $stmt = $sql->prepare("SELECT * FROM article");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
?>