<?php
	session_start();

	include('class/security.class.php');
	include('class/PrioridadeTicketDAO.class.php');
	include('class/PrioridadeTicket.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('prioridade_ticket', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$PrioridadeTicketDAO = new PrioridadeTicketDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$prt_id 	= 0;
			} else {
				$prt_id 	= $postData->prt_id;
			}
			$prt_prioridade 	= $postData->prt_prioridade;
			$prt_descricao 	= $postData->prt_descricao;
			$prt_cor 	= (!Empty($postData->prt_cor) ? $postData->prt_cor : null);

			$PrioridadeTicket = new PrioridadeTicket($prt_id,$prt_prioridade,$prt_descricao,$prt_cor);

			$nRetId = $prt_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($PrioridadeTicketDAO->Insere($PrioridadeTicket)) {
				 		$lOk = true;
						$nRetId = $PrioridadeTicketDAO->id_inserido;
						$PrioridadeTicketDAO->cReturnMsg  = 'Prioridade de Ticket inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$PrioridadeTicketDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($PrioridadeTicketDAO->Altera($PrioridadeTicket)) {
				 		$lOk = true;
						$PrioridadeTicketDAO->cReturnMsg  = 'Prioridade de Ticket alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$PrioridadeTicketDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($PrioridadeTicketDAO->Deleta($PrioridadeTicket)) {
				 		$lOk = true;
						$PrioridadeTicketDAO->cReturnMsg  = 'Prioridade de Ticket excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$PrioridadeTicketDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($PrioridadeTicketDAO->Restaura($PrioridadeTicket)) {
				 		$lOk = true;
						$PrioridadeTicketDAO->cReturnMsg  = 'Prioridade de Ticket restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$PrioridadeTicketDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new logEdicaoDAO();
			$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'prioridade_ticket', $nRetId, $postData->ctrlaction, 'prioridade_ticket', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$PrioridadeTicketDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
