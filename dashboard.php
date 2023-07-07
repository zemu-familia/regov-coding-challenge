<?php
require_once 'class/classes.php';
require_once 'util/db.php';
require_once 'util/functions.php';

startSession();
// redirect to login page if not logged in
if(!isLoggedIn()){
	redirect('./');
}
$user = $_SESSION['user'];
echo 'welcome ' . $user -> get_username();
?>