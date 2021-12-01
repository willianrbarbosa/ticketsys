<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\CategoriaTicketDAO;
	use TicketSys\Model\Classes\CategoriaTicket;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('categoria_ticket', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$CategoriaTicketDAO = new CategoriaTicketDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$cgt_id 	= 0;
			} else {
				$cgt_id 	= $postData->cgt_id;
			}
			$cgt_descricao 	= $postData->cgt_descricao;

			$CategoriaTicket = new CategoriaTicket($cgt_id,$cgt_descricao);

			$nRetId = $cgt_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($CategoriaTicketDAO->Insere($CategoriaTicket)) {
				 		$lOk = true;
						$nRetId = $CategoriaTicketDAO->id_inserido;
						$CategoriaTicketDAO->cReturnMsg  = 'Categoria de Ticket inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$CategoriaTicketDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($CategoriaTicketDAO->Altera($CategoriaTicket)) {
				 		$lOk = true;
						$CategoriaTicketDAO->cReturnMsg  = 'Categoria de Ticket alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$CategoriaTicketDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($CategoriaTicketDAO->Deleta($CategoriaTicket)) {
				 		$lOk = true;
						$CategoriaTicketDAO->cReturnMsg  = 'Categoria de Ticket excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$CategoriaTicketDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($CategoriaTicketDAO->Restaura($CategoriaTicket)) {
				 		$lOk = true;
						$CategoriaTicketDAO->cReturnMsg  = 'Categoria de Ticket restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$CategoriaTicketDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new LogEdicaoDAO();
			$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'categoria_ticket', $nRetId, $postData->ctrlaction, 'categoria_ticket', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$CategoriaTicketDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
