<?php
	session_start();
	// Inclui o arquivo com o sistema de segurança
	include('class/security.class.php');
	$security = new Security();		
	setcookie('rememberme', '', time() - 60 * 60 * 24 * 30, '/');
	setcookie('customertoken', '', time() - 60 * 60 * 24 * 30, '/');
	$security->goIndex();
	
?>