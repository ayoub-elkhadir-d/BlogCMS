<?php  
function get_user_byid($sql, $user_name){
    $stmt = $sql->prepare("SELECT * FROM users WHERE user_name = ?");
    $stmt->execute([$user_name]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function update_user_byid($sql, $user_name,$password,$email,$role){
    $stmt = $sql->prepare("UPDATE users set email='$email',password_hash='$password',role='$role'  WHERE user_name = '$user_name'");
    $stmt->execute();
  
}
function get_all_comments($sql){
    $stmt = $sql->prepare("SELECT * from comment");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_count_comments_in_post($sql,$post_id){ 
    $comments = $sql->prepare("SELECT COUNT(*) FROM comment WHERE post_id = ? and status_cmt='approved'");
    $comments->execute([$post_id]);
    return $comments->fetchColumn();
}

?>