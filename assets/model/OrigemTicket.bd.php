<?php
	session_start();

	include('class/security.class.php');
	include('class/OrigemTicketDAO.class.php');
	include('class/OrigemTicket.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('origem_ticket', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$OrigemTicketDAO = new OrigemTicketDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$ort_id 	= 0;
			} else {
				$ort_id 	= $postData->ort_id;
			}
			$ort_descricao 	= $postData->ort_descricao;

			$OrigemTicket = new OrigemTicket($ort_id,$ort_descricao);

			$nRetId = $ort_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($OrigemTicketDAO->Insere($OrigemTicket)) {
				 		$lOk = true;
						$nRetId = $OrigemTicketDAO->id_inserido;
						$OrigemTicketDAO->cReturnMsg  = 'Origem de Ticket inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$OrigemTicketDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($OrigemTicketDAO->Altera($OrigemTicket)) {
				 		$lOk = true;
						$OrigemTicketDAO->cReturnMsg  = 'Origem de Ticket alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$OrigemTicketDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($OrigemTicketDAO->Deleta($OrigemTicket)) {
				 		$lOk = true;
						$OrigemTicketDAO->cReturnMsg  = 'Origem de Ticket excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$OrigemTicketDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($OrigemTicketDAO->Restaura($OrigemTicket)) {
				 		$lOk = true;
						$OrigemTicketDAO->cReturnMsg  = 'Origem de Ticket restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$OrigemTicketDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new logEdicaoDAO();
			$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'origem_ticket', $nRetId, $postData->ctrlaction, 'origem_ticket', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$OrigemTicketDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
