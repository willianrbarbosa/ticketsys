<?php
	session_start();
	// Inclui o arquivo com o sistema de segurança
	include('class/security.class.php');
	include('class/usuarioDAO.class.php');
	$security = new Security();		

	$lSessionExist	= $security->PageSafe();

	//Verifica se existe o cookie de sessão
	if ( !$lSessionExist ) {
		if ( (isset($_COOKIE['rememberme']) && $_COOKIE['rememberme'] != '' ? $_COOKIE['rememberme'] : false) ) {
			$usuarioDAO = new usuarioDAO();	

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

				if ($security->userValid($email, $passwd, true, $cMsg) == true){								
					setcookie('rememberme', base64_encode($email)."/".base64_encode($passwd)."/".base64_encode($security->getUser_token()), time() + 60 * 60 * 24 * 30, '/');

					$security->conn->beginTransaction();

					$sql = "INSERT INTO usuario_acesso (usa_user_id,usa_user_email,usa_data,usa_ip,usa_plataforma) VALUES (?,?,?,?,?)";
					$stmt = $security->conn->prepare($sql);						
					$stmt->bindValue(1, $security->getUser_id()		, PDO::PARAM_INT);
					$stmt->bindValue(2, $security->getUser_email()	, PDO::PARAM_STR);
					$stmt->bindValue(3, Date('Y-m-d H:i:s')			, PDO::PARAM_STR);
					$stmt->bindValue(4, $_SERVER['REMOTE_ADDR']		, PDO::PARAM_STR);
					$stmt->bindValue(5, 'ticketsys'				, PDO::PARAM_STR);

					$stmt->execute();	
					$security->conn->commit();

					$lSessionExist	= $security->PageSafe();
				}

			}
		}
	}
	
	echo json_encode(array("session"=>$lSessionExist));
?>