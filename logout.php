<?php
require_once 'util/functions.php';
startSession();
if(isLoggedIn()){
	session_destroy();
}
redirect('index.php')
?>