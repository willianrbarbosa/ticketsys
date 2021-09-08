<?php
	session_start();
	
	include('class/security.class.php');
	include('class/usuarioDAO.class.php');

	//pega os dados do $http do Angular
	$post = file_get_contents("php://input");

	if($post){
		$security = new Security();	
		$usuarioDAO = new usuarioDAO();

		$aUser = $usuarioDAO->buscaByToken($post);

		$email 	= $aUser['user_email'];
		$passwd = $aUser['user_passwd'];

		$cMsg = '';

		if ($security->userValid($email, $passwd, false, $cMsg) == true){
			echo json_encode(array("return"=>true, "msg"=>$cMsg));
		} else {
			echo json_encode(array("return"=>false, "msg"=>$cMsg ));
		}
	}
?>