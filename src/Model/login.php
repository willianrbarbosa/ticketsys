<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\UsuarioDAO;
	use PDO;

	$security = new Security();	
	//pega os dados do $http do Angular
	$post = file_get_contents("php://input");
	$email 			= '';
	$passwd 		= '';
	if($post){
		$dados 		= json_decode($post);

		$email 		= $dados->email;
		$passwd 	= $dados->passwd;
		$rememberme = (isset($dados->rememberme) ? $dados->rememberme : false);
	} else {
		$usuarioDAO = new UsuarioDAO();	

		//Verifica se existe o cookie de sessão
		if ( (isset($_COOKIE['rememberme']) && $_COOKIE['rememberme'] != '' ? $_COOKIE['rememberme'] : false) ) {
			$rememberme 	= $_COOKIE['rememberme'];
			$aRememberMe 	= explode('/', $rememberme);

			$cUserEmail 	= base64_decode($aRememberMe[0]);
			$cUserPasswd 	= base64_decode($aRememberMe[1]);
			$cUserToken 	= base64_decode($aRememberMe[2]);

			//verifica se o token salvo no cookie ainda existe para o usuário
			$aUser 			= $usuarioDAO->buscaByToken($cUserToken);
			if ( !Empty($aUser) ) {
				$email 		= $cUserEmail;
				$passwd 	= $cUserPasswd;
				$rememberme = true;				
			}
		} 
	}
	$cMsg 			= '';
	$cMsgCrt		= '';

	if ( (!Empty($email)) AND (!Empty($passwd)) ) {
		if ($security->userValid($email, $passwd, true, $cMsg) == true){	
			if ($security->newUserToken($email) == true) {

				$security->conn->beginTransaction();

				$sql = "INSERT INTO usuario_acesso (usa_user_id,usa_user_email,usa_data,usa_ip,usa_plataforma) VALUES (?,?,?,?,?)";
				$stmt = $security->conn->prepare($sql);						
				$stmt->bindValue(1, $security->getUser_id()		, PDO::PARAM_INT);
				$stmt->bindValue(2, $security->getUser_email()	, PDO::PARAM_STR);
				$stmt->bindValue(3, Date('Y-m-d H:i:s')			, PDO::PARAM_STR);
				$stmt->bindValue(4, $_SERVER['REMOTE_ADDR']		, PDO::PARAM_STR);
				$stmt->bindValue(5, 'ticketsys'					, PDO::PARAM_STR);

				$stmt->execute();	
				$security->conn->commit();

				if ( $rememberme == true ) {
					setcookie('rememberme', base64_encode($email)."/".base64_encode($passwd)."/".base64_encode($security->getUser_token()), time() + 60 * 60 * 24 * 30, '/');
				} else {
					setcookie('rememberme', '', time() - 60 * 60 * 24 * 30, '/');
				}

				echo json_encode(array("return"=>true, "msg"=>$cMsg));
			} else {
				echo json_encode(array("return"=>false, "msg"=>'Erro ao gerar o Token do usuário.' ));
			}
		} else {
			echo json_encode(array("return"=>false, "msg"=>$cMsg ));
		}
	}
?>