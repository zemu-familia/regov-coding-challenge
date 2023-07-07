<?php
if(session_status() === PHP_SESSION_NONE)
    session_start();

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'regov';

$db = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ($db->connect_errno) {
  echo 'Failed to connect to MySQL: ' . $db->connect_error;
  exit();
}
?>