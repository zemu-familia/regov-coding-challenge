<?php
require_once 'class/classes.php';
require_once 'util/db.php';
require_once 'util/functions.php';

startSession();

// redirect to login page if not logged in
if(!isLoggedIn()){
	redirect('./');
}else{
	$user = $_SESSION['user'];
	$userid = $user -> get_id();
	
	// get currently saved text and draft
	$stmt = $db	 -> prepare("SELECT about_me, about_me_draft FROM user WHERE uniqid = ?");
	$stmt -> bind_param('i', $userid);
	$stmt -> execute();

	$result = $stmt -> get_result();
	if($result -> num_rows == 1){
		$row = $result -> fetch_assoc();
		$about_me = $row['about_me'];
		$about_me_draft = $row['about_me_draft'];
	}
	
	// handle POST request
	if(postRequested()){
		// handle POST request for About Me updates
		if(isset($_POST['submit'])){
			$text = filterStringPOST('aboutme');
			$submit = $_POST['submit'];
			if($submit == 'Save'){
				// if saving actual text, update it and remove the draft in db
				$query = 'UPDATE user SET about_me = ?, about_me_draft = NULL WHERE uniqid = ?';
			}else{
				// if saving as draft, only update draft in db
				$query = 'UPDATE user SET about_me_draft = ? WHERE uniqid = ?';
			}
			$stmt = $db -> prepare($query);
			$stmt -> bind_param('si', $text, $userid);
			$stmt -> execute();
			
			// set success message
			if($stmt -> affected_rows > 0){
				if($submit == 'Save')
					$success = 'Your profile has been updated.';
				else
					$success = 'Draft has been saved.';
			}else{
				// if no rows are updated because values are the same, keep displaying as success
				if($text == $about_me)
					$success = 'Your profile has been updated.';
				elseif($text == $about_me_draft)
					$success = 'Draft has been saved.';
				else // otherwise, it is safe to assume that an error occurred
					$error = 'An error occurred. Please try again later.';
			}
		}
		
		// handle POST request for password update
		if(isset($_POST['accSubmit'])){
			$newPass = filterStringPOST('password');
			$newPassConfirm = filterStringPOST('passwordconfirm');
			
			if($newPass != null && $newPassConfirm != null){
				if(trim($newPass) != '' && trim($newPassConfirm != '')){
					if($newPass == $newPassConfirm){
						$stmt = $db -> prepare('UPDATE user SET password = ? WHERE uniqid = ?');
						$stmt -> bind_param('si', $newPass, $userid);
						$stmt -> execute();
						
						if($stmt -> affected_rows > 0){
							$accSuccess = 'Your password has successfully been updated.';
						}else{
							$accError = 'An error occurred while updating password. Please try again.' . $stmt -> errno;
						}
					}else{
						$accError = 'Passwords do not match';
					}
				}else{
					$accError = 'Invalid password. Please try another one.';
				}

			}else{
				$accError = 'Please fill in all fields.';
			}
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
		<h1 class="fs-4">Hello <?= $user -> get_username(); ?> ! </h1>
		<p class="form-text mt-2"><a class="link-secondary" href="logout.php">Log out</a></p>
		<table class="table">
			<tr>
				<td>
					<h2 class="fs-6 text-start">User Profile</h2>
					<form method="post">
						<div class="form-floating mb-3">
							<textarea class="form-control" id="aboutme" name="aboutme" placeholder="About me"><?= $about_me ?></textarea>
							<label for="aboutme">About Me</label>	
						</div>
						<?php
							// display error message if there is one
							if(isset($error)){
								echo "<p style='color:red'>$error</p>";
							}
							
							// display success message if there is one
							if(isset($success)){
								echo "<p style='color:blue'>$success</p>";
							}
						?>
						<input type="submit" name="submit" class="btn btn-secondary" value="Save as Draft">
						<input type="button" class="btn btn-secondary" name="retrieve" value="Retrieve from Draft" <?= empty($about_me_draft) ? 'disabled title="No draft found."' : 'onclick="retrieveDraft()"' // if draft is empty, disable button. else, set onclick to retrieve draft ?>>
						<input type="submit" name="submit" class="btn btn-primary" value="Save">
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<h2 class="fs-6 text-start mt-4">Account Settings</h2>
					<form method="post">
						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="username" placeholder="Username" disabled value="<?= $user -> get_username() ?>">
							<label for="username">Username</label>
						</div>
						<div class="form-floating mb-3">
							<input type="password" class="form-control" id="password" name="password" placeholder="password" required>
							<label for="password">New Password</label>
						</div>
						<div class="form-floating mb-3">
							<input type="password" class="form-control" id="passwordconfirm" name="passwordconfirm" placeholder="passwordconfirm" required>
							<label for="passwordconfirm">Confirm New Password</label>
						</div>
						<?php
							// display error message if there is one
							if(isset($accError)){
								echo "<p style='color:red'>$accError</p>";
							}
							
							// display success message if there is one
							if(isset($accSuccess)){
								echo "<p style='color:blue'>$accSuccess</p>";
							}
						?>
						<input class="btn btn-primary" type="submit" name="accSubmit" value="Save">
					</form>
				</td>
			</tr>
		
	</div>
	<!-- jQuery import -->
	<script src="https://code.jquery.com/jquery-3.7.0.slim.min.js" integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>
	<?php
	// only print the script to html if draft is not empty
	if(!empty($about_me_draft)){
	?>
	<script>
		function retrieveDraft(){
			$('#aboutme').val('<?= $about_me_draft ?>');
		}
	</script>
	<?php
	}
	?>
</body>
</html>