<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\TicketHistoricoDAO;
	use TicketSys\Model\Classes\TicketHistorico;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;;

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_historico', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$TicketHistoricoDAO = new TicketHistoricoDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$tkh_id 	= 0;
			} else {
				$tkh_id 	= $postData->tkh_id;
			}
			$tkh_tkt_id 	= $postData->tkh_tkt_id;
			$tkh_user_id 	= $postData->tkh_user_id;
			$tkh_data_hora 	= (!Empty($postData->tkh_data_hora) ? implode("-", array_reverse(explode("/", $postData->tkh_data_hora))) : null);
			$tkh_descricao 	= (!Empty($postData->tkh_descricao) ? $postData->tkh_descricao : null);

			$TicketHistorico = new TicketHistorico($tkh_id,$tkh_tkt_id,$tkh_user_id,$tkh_data_hora,$tkh_descricao);

			$nRetId = $tkh_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($TicketHistoricoDAO->Insere($TicketHistorico)) {
				 		$lOk = true;
						$nRetId = $TicketHistoricoDAO->id_inserido;
						$TicketHistoricoDAO->cReturnMsg  = 'TicketHistorico inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketHistoricoDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($TicketHistoricoDAO->Altera($TicketHistorico)) {
				 		$lOk = true;
						$TicketHistoricoDAO->cReturnMsg  = 'TicketHistorico alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketHistoricoDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketHistoricoDAO->Deleta($TicketHistorico)) {
				 		$lOk = true;
						$TicketHistoricoDAO->cReturnMsg  = 'TicketHistorico excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketHistoricoDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketHistoricoDAO->Restaura($TicketHistorico)) {
				 		$lOk = true;
						$TicketHistoricoDAO->cReturnMsg  = 'TicketHistorico restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketHistoricoDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new LogEdicaoDAO();
			$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'ticket_historico', $nRetId, $postData->ctrlaction, 'ticket_historico', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$TicketHistoricoDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
