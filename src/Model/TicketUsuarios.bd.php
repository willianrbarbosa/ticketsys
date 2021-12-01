<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\TicketUsuariosDAO;
	use TicketSys\Model\Classes\TicketUsuarios;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_usuarios', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$TicketUsuariosDAO = new TicketUsuariosDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$tku_id 	= 0;
			} else {
				$tku_id 	= $postData->tku_id;
			}
			$tku_tkt_id 	= $postData->tku_tkt_id;
			$tku_user_id 	= $postData->tku_user_id;
			$tku_tipo 	= (!Empty($postData->tku_tipo) ? $postData->tku_tipo : null);
			$tku_notif_email 	= (!Empty($postData->tku_notif_email) ? ($postData->tku_notif_email ? 'S' : 'N') : 'N');
			$tku_notif_sistema 	= (!Empty($postData->tku_notif_sistema) ? ($postData->tku_notif_sistema ? 'S' : 'N') : 'N');

			$TicketUsuarios = new TicketUsuarios($tku_id,$tku_tkt_id,$tku_user_id,$tku_tipo,$tku_notif_email,$tku_notif_sistema);

			$nRetId = $tku_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($TicketUsuariosDAO->Insere($TicketUsuarios)) {
				 		$lOk = true;
						$nRetId = $TicketUsuariosDAO->id_inserido;
						$TicketUsuariosDAO->cReturnMsg  = 'TicketUsuarios inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketUsuariosDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($TicketUsuariosDAO->Altera($TicketUsuarios)) {
				 		$lOk = true;
						$TicketUsuariosDAO->cReturnMsg  = 'TicketUsuarios alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketUsuariosDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketUsuariosDAO->Deleta($TicketUsuarios)) {
				 		$lOk = true;
						$TicketUsuariosDAO->cReturnMsg  = 'TicketUsuarios excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketUsuariosDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketUsuariosDAO->Restaura($TicketUsuarios)) {
				 		$lOk = true;
						$TicketUsuariosDAO->cReturnMsg  = 'TicketUsuarios restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketUsuariosDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new LogEdicaoDAO();
			$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'ticket_usuarios', $nRetId, $postData->ctrlaction, 'ticket_usuarios', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$TicketUsuariosDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
