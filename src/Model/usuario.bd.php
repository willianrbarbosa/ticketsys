<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\UsuarioDAO;
	use TicketSys\Model\Classes\Usuario;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('users', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if($post){
			$postData = json_decode($post);
			$usuarioDAO = new UsuarioDAO();


			/*if ($postData->ctrlaction == 'theme') {
				$nRetId = ($postData->nUserTK == 'null' ? $security->getUser_id() : $postData->nUserTK);
				if ($usuarioDAO->setUserTheme($nRetId, $postData->theme)) {
					$lOk = true;
				} else {
					$lOk = false;				
				}
			} elseif ($postData->ctrlaction == 'changeEmp') {
				$security->setUserCliente($postData->nCliTK);
				$lOk 	= true;
				$nRetId = $postData->nCliTK;
			} else {*/
			
				
			if ($postData->ctrlaction == 'new') {
				$user_id 		= 0;			
				$tokenGeneric 	= MD5(Date('yyyymmdd').$postData->user_email.time());
				$user_token 	= substr(preg_replace("/[^0-9]/", "", hash('sha256', $tokenGeneric)),1,20);
				$user_prc_status= 'N';
				$user_ativo 	= 'S';
			} else {
				$user_id 		= $postData->user_id;
				if($postData->ctrlaction == 'inativa'){
					$user_token 	= null;
					$user_ativo 	= 'N';
				}elseif($postData->ctrlaction == 'ativa'){
					$tokenGeneric 	= MD5(Date('yyyymmdd').$postData->user_email.time());
					$user_token 	= substr(preg_replace("/[^0-9]/", "", hash('sha256', $tokenGeneric)),1,20);
					$user_ativo 	= 'S';
				}else{
					$user_token 		= $postData->user_token;
				}
				$user_prc_status= $postData->user_prc_status;
				$user_ativo 	= $postData->user_ativo;
			}
			$user_nome 			= $postData->user_nome;
			$user_email 		= $postData->user_email;
			$user_pfa_id 		= $postData->user_pfa_id;
			
			if (!Empty($postData->user_passwd)) {
				$user_passwd	=  MD5($postData->user_passwd);
			} else {
				if ($postData->ctrlaction == 'edit') {
					$aUserData 		= $usuarioDAO->buscaById($user_id);
					$user_passwd	= $aUserData['user_passwd'];
				} else {
					$user_passwd	= MD5('123456');
				}
			}

			$user_cli_id 		 = json_encode($postData->user_cli_id);
			if (isset($postData->user_photo)) {
				$user_photo	=  $postData->user_photo;
			} else {
				$user_photo	= '';
			}
			$user_tipo 			= $postData->user_tipo;
			$user_pst_id 		= (isset($postData->user_pst_id) ? $postData->user_pst_id : null);
			$user_auditor_mod 	= $postData->user_auditor_mod;
			if (isset($postData->user_resp_ticket)) {
				if ( !Empty($postData->user_resp_ticket) ) {
					$user_resp_ticket	= 'S';
				} else {
					$user_resp_ticket	= 'N';
				}
			} else {
				$user_resp_ticket	= 'N';
			}
			$user_email_confirm 	= 'S';
			$Usuario = new Usuario($user_id,$user_nome,$user_email,$user_passwd,$user_pfa_id,$user_photo,$user_tipo,$user_token,$user_ativo,$user_pst_id,$user_resp_ticket,$user_email_confirm);

			$nRetId = $user_id;

			if ($postData->ctrlaction == 'new') {			
				if ($usuarioDAO->Insere($Usuario)) {
					$lOk = true;
					$nRetId = $usuarioDAO->id_inserido;
				} else {
					$lOk = false;
				}	
			} elseif ($postData->ctrlaction == 'edit') {			
				if ($usuarioDAO->Altera($Usuario)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			} elseif ($postData->ctrlaction == 'delete') {			
				if ($usuarioDAO->Deleta($Usuario)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			} elseif ($postData->ctrlaction == 'inativa') {			
				if ($usuarioDAO->Inativa($Usuario)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			} elseif ($postData->ctrlaction == 'ativa') {			
				if ($usuarioDAO->Ativa($Usuario)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			}

			//Aqui vai atualizar as sessions do usuário (caso seja o mesmo do logado)
			if ( ($security->getUser_id() == $user_id) AND ($lOk) ) {
				$security->setUser_nome($user_nome);
				$security->setUser_email($user_email);
				$security->setUser_tipo($user_tipo);
			}
			// $security->setUser_cli_id($user_cli_id);
			
			$logEdicaoDAO 	= new LogEdicaoDAO();
			$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'users', $nRetId, $postData->ctrlaction, 'usuario', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>