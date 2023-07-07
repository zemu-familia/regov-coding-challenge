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
	
	if(postRequested()){
		$text = filterStringPOST('aboutme');
		if(isset($_POST['submit'])){
			$submit = $_POST['submit'];
			if($submit == 'Save'){
				// if saving actual text, update it and remove the draft in db
				$query = "UPDATE user SET about_me = ?, about_me_draft = NULL WHERE uniqid = ?";
			}else{
				// if saving as draft, only update draft in db
				$query = "UPDATE user SET about_me_draft = ? WHERE uniqid = ?";
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
				$error = 'An error occurred. Please try again later.';
			}
		}
	}
	
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
		<h1 class="fs-4">Hello <?= $user -> get_username(); ?> !</h1>
		<form method="post">
			<h2 class="fs-6 text-start">User Profile</h2>
			<div class="form-floating mb-3">
				<textarea class="form-control" id="aboutme" name="aboutme" placeholder="About me"><?= $about_me ?></textarea>
				<label for="aboutme" disabled>About Me</label>	
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
		<p class="form-text mt-2"><a class="link-secondary" href="logout.php">Logout</a></p>
		
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