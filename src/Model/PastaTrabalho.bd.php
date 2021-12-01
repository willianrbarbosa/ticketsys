<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\PastaTrabalhoDAO;
	use TicketSys\Model\Classes\PastaTrabalho;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('pasta_trabalho', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$PastaTrabalhoDAO = new PastaTrabalhoDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$pst_id 	= 0;
			} else {
				$pst_id 	= $postData->pst_id;
			}
			$pst_descricao 	= $postData->pst_descricao;
			$pst_grt_id 	= $postData->pst_grt_id;

			$PastaTrabalho = new PastaTrabalho($pst_id,$pst_descricao,$pst_grt_id);

			$nRetId = $pst_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($PastaTrabalhoDAO->Insere($PastaTrabalho)) {
				 		$lOk = true;
						$nRetId = $PastaTrabalhoDAO->id_inserido;
						$PastaTrabalhoDAO->cReturnMsg  = 'Pasta de Trabalho inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$PastaTrabalhoDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($PastaTrabalhoDAO->Altera($PastaTrabalho)) {
				 		$lOk = true;
						$PastaTrabalhoDAO->cReturnMsg  = 'Pasta de Trabalho alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$PastaTrabalhoDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($PastaTrabalhoDAO->Deleta($PastaTrabalho)) {
				 		$lOk = true;
						$PastaTrabalhoDAO->cReturnMsg  = 'Pasta de Trabalho excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$PastaTrabalhoDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($PastaTrabalhoDAO->Restaura($PastaTrabalho)) {
				 		$lOk = true;
						$PastaTrabalhoDAO->cReturnMsg  = 'Pasta de Trabalho restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$PastaTrabalhoDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new LogEdicaoDAO();
			$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'pasta_trabalho', $nRetId, $postData->ctrlaction, 'pasta_trabalho', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$PastaTrabalhoDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
