<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;

	$security = new Security();		
	setcookie('rememberme', '', time() - 60 * 60 * 24 * 30, '/');
	setcookie('customertoken', '', time() - 60 * 60 * 24 * 30, '/');
	$security->goIndex();
	
?>