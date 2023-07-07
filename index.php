<?php
require_once 'util/db.php';
require_once 'util/functions.php';
require_once 'class/classes.php';

// check if logged in
startSession();
if(isLoggedIn()){
	header('Location: dashboard.php');
}else{
	if(postRequested()){
		$username = filterStringPOST('username');
		$password = filterStringPOST('password');
		if($username != null && $password != null){
			// basic login
			$stmt = $db -> prepare("SELECT uniqid, username, password FROM user WHERE username = ? AND password = ?");
			$stmt -> bind_param('ss', $username, $password);
			$stmt -> execute();
			$result = $stmt -> get_result();
			
			// if there's a match, sign in and redirect
			if($result -> num_rows > 0){
				while($row = $result -> fetch_assoc()){
					$user = new User();
					$user -> set_id($row['uniqid']);
					$user -> set_username($row['username']);
					
					$_SESSION['user'] = $user;
					header('Location: dashboard.php');
				}
			}else{
				$error = 'Invalid login. Please try again.';
			}
		}else{
			$error = 'Please fill in all the fields.';
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- bootstrap and style import -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
	<link rel="stylesheet" href="./style.css">
</head>
<body>
	<!-- bootstrap script import -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
	<div class="container rounded text-center p-4 mt-4">
		<form method="post">
			<h1 class="fs-4">Sign in</h1>
			<div class="form-floating mb-3">
				<input type="text" class="form-control" id="username" name="username" placeholder="Insert username">
				<label for="username">Username</label>	
			</div>
			<div class="form-floating mb-3">
				<input type="password" class="form-control" id="password" name="password" placeholder="Insert password">
				<label for="password">Password</label>	
			</div>
			<input type="submit" class="btn btn-primary" value="Sign In">
		</form>
		<p class="form-text mt-2">Don't have an account? <a href="register.php">Register</a>!</p>
		<?php
		if(isset($error)){
			echo "<p style='color:red'>$error</p>";
		}
		?>
	</div>
</body>
</html>