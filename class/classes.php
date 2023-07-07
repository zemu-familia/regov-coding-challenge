<?php
class User{
	private $id;
	private $username;
	
	// mutators
	function set_id($uniqid){
		$this -> id = $uniqid;
	}
	function set_username($username){
		$this -> username = $username;
	}
	
	// retrievers
	function get_id(){
		return $this -> id;
	}
	function get_username(){
		return $this -> username;
	}
}
?>