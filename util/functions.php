<?php

// shortcut function
// checks if there's a POST request
function postRequested(){
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// shortcut function
// starts session if it is not started yet
function startSession(){
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }
}

function isLoggedIn(){
	return isset($_SESSION['user']);
}

// shortcut function
// returns sanitised string if variable is found, returns null otherwise
function filterStringPOST($varName){
    return isset($_POST[$varName]) ? filter_input(INPUT_POST, $varName, FILTER_SANITIZE_STRING) : null;
}

?>