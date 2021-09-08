<?php
	session_start();

	include('class/security.class.php');
	include('class/SituacaoTicketDAO.class.php');
	include('class/SituacaoTicket.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('situacao_ticket', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$SituacaoTicketDAO = new SituacaoTicketDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$stt_id 	= 0;
			} else {
				$stt_id 	= $postData->stt_id;
			}
			$stt_ordem 			= $postData->stt_ordem;
			$stt_descricao 		= $postData->stt_descricao;
			$stt_aprova_ticket 	= (!Empty($postData->stt_aprova_ticket) ? ($postData->stt_aprova_ticket ? 'S' : 'N') : 'N');
			$stt_encerra_ticket = (!Empty($postData->stt_encerra_ticket) ? ($postData->stt_encerra_ticket ? 'S' : 'N') : 'N');
			$stt_kanban 		= (!Empty($postData->stt_kanban) ? ($postData->stt_kanban ? 'S' : 'N') : 'N');

			$SituacaoTicket = new SituacaoTicket($stt_id,$stt_ordem,$stt_descricao,$stt_aprova_ticket,$stt_encerra_ticket,$stt_kanban);

			$nRetId = $stt_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($SituacaoTicketDAO->Insere($SituacaoTicket)) {
				 		$lOk = true;
						$nRetId = $SituacaoTicketDAO->id_inserido;
						$SituacaoTicketDAO->cReturnMsg  = 'Situação de Ticket inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$SituacaoTicketDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($SituacaoTicketDAO->Altera($SituacaoTicket)) {
				 		$lOk = true;
						$SituacaoTicketDAO->cReturnMsg  = 'Situação de Ticket alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$SituacaoTicketDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($SituacaoTicketDAO->Deleta($SituacaoTicket)) {
				 		$lOk = true;
						$SituacaoTicketDAO->cReturnMsg  = 'Situação de Ticket excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$SituacaoTicketDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($SituacaoTicketDAO->Restaura($SituacaoTicket)) {
				 		$lOk = true;
						$SituacaoTicketDAO->cReturnMsg  = 'Situação de Ticket restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$SituacaoTicketDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new logEdicaoDAO();
			$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'situacao_ticket', $nRetId, $postData->ctrlaction, 'situacao_ticket', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$SituacaoTicketDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
