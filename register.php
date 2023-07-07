<?php
require_once 'util/db.php';
require_once 'util/functions.php';

// redirect to dashboard if logged in
startSession();
if(isLoggedIn()){
	redirect('dashboard.php');
}else{
	// if not logged in, check if there's a POST request
	if(postRequested()){
		$username = filterStringPOST('username');
		$password = filterStringPOST('password');
		$reenter = filterStringPOST('passwordconfirmation');
		if($username != null && $password != null && $reenter != null){
			// if inputs are valid, insert into table
			if($password == $reenter){
				$stmt = $db -> prepare("INSERT INTO user(username, password) VALUES(?, ?)");
				$stmt -> bind_param('ss', $username, $password);
				$stmt -> execute();
				
				// if database insertion successful, redirect back to login page
				if($stmt -> affected_rows == 1){
					$_SESSION['success'] = "Registration successful. You can now log in!";
					redirect('index.php');
				}else{
					if($stmt -> errno == 1062){
						$error = 'The username you chose has been taken. Please try another one.';
					}else{
						$error = 'Registration failed. Please try again.';
					}
				}
			}else{
				$error = 'Passwords do not match.';
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
			<h1 class="fs-4">Register</h1>
			<div class="form-floating mb-3">
				<input type="text" class="form-control" id="username" name="username" placeholder="Insert username" <?= isset($username) ? "value='$username'" : ''?>>
				<label for="username">Username</label>	
			</div>
			<div class="form-floating mb-3">
				<input type="password" class="form-control" id="password" name="password" placeholder="Insert password">
				<label for="password">Password</label>	
			</div>
			<div class="form-floating mb-3">
				<input type="password" class="form-control" id="passwordconfirmation" name="passwordconfirmation" placeholder="Re-enter password">
				<label for="passwordconfirmation">Re-enter Password</label>	
			</div>
			<input type="submit" class="btn btn-primary" value="Register">
		</form>
		<p class="form-text mt-2">Already have an account? <a href="index.php">Sign in</a>!</p>
		<?php
		// display error message if there is one
		if(isset($error)){
			echo "<p style='color:red'>$error</p>";
		}
		?>
	</div>
</body>
</html>