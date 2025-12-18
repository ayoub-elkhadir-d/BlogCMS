<?php
session_start();
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'null'; 

$pdo = new PDO("mysql:host=localhost;dbname=blog;", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="styles/regester.css">
</head>
<body>

<nav class="main-nav">
    <div class="nav-top">
        <div class="nav-left">
            <div class="user-avatar">
                <i class="fas fa-user"></i> </div>
            <span class="brand-name">@User</span>
            <span class="brand-name">    Blog cms</span>
        </div>
     
    </div>

    <div class="nav-bottom">
        <a href="index.php" class="nav-link">Home</a>
    
    </div>
</nav>

    <div class="form-container">
        <h2>Login</h2>
       <form method="POST" action="regester.php?mode=set_login_info">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="pswd" placeholder="Enter your password" required>
            
            <div class="forgot-pass">
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn">Login</button>
            
            <p class="footer-text">
                Don't have an account? <a href="signup.html">Signup</a>
            </p>
        </form>
    </div>

  
	


<?php if($mode == 'null' && isset($_SESSION['username']) != ''){
session_unset();
}?>
<?php if($mode == 'set_login_info'){


$email    = $_POST['email'];
$password = $_POST['pswd'];

$stmt = $pdo->prepare("
    SELECT  email,password_hash,role,user_name
    FROM users 
    WHERE email = ? AND password_hash = ?
");
$stmt->execute([$email, $password]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
	
$_SESSION['username'] =  $user['user_name'];
$_SESSION['role']= $user['role'];
header("Location: index.php");
} else {
	
}


}

?>

</body>
</html>