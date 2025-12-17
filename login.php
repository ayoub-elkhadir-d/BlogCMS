<?php
session_start();
?>
<?php
$mode = $_GET['mode'];

$pdo = new PDO("mysql:host=localhost;dbname=blog;", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Slide Navbar</title>
	<link rel="stylesheet" type="text/css" href="styles/login.css">
</head>
<body>
	<div class="main">  	
		<input type="checkbox" id="chk" aria-hidden="true">

			<div class="signup">
				<form>
					<label for="chk" aria-hidden="true">Sign up</label>
					<input type="text" name="txt" placeholder="User name" required="">
					<input type="email" name="email" placeholder="Email" required="">
                   <input type="number" name="broj" placeholder="BrojTelefona" required="">
					<input type="password" name="pswd" placeholder="Password" required="">
					<button>Sign up</button>
				</form>
			</div>

			<div class="login">
				<form method="POST" action="login.php?mode=set_login_info">
					<label for="chk" aria-hidden="true">Login</label>
					<input type="email" name="email" placeholder="Email" required="">
					<input type="password" name="pswd" placeholder="Password" required="">
					<button type="submit" id="login">Login</button>
				</form>
             
			</div>
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