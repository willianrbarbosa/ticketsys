<?php
	session_start();

	include('class/security.class.php');
	include('class/GrupoTrabalhoDAO.class.php');
	include('class/GrupoTrabalho.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('grupo_trabalho', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$GrupoTrabalhoDAO = new GrupoTrabalhoDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$grt_id 	= 0;
			} else {
				$grt_id 	= $postData->grt_id;
			}
			$grt_descricao 	= $postData->grt_descricao;

			$GrupoTrabalho = new GrupoTrabalho($grt_id,$grt_descricao);

			$nRetId = $grt_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($GrupoTrabalhoDAO->Insere($GrupoTrabalho)) {
				 		$lOk = true;
						$nRetId = $GrupoTrabalhoDAO->id_inserido;
						$GrupoTrabalhoDAO->cReturnMsg  = 'Grupo de Trabalho inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$GrupoTrabalhoDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($GrupoTrabalhoDAO->Altera($GrupoTrabalho)) {
				 		$lOk = true;
						$GrupoTrabalhoDAO->cReturnMsg  = 'Grupo de Trabalho alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$GrupoTrabalhoDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($GrupoTrabalhoDAO->Deleta($GrupoTrabalho)) {
				 		$lOk = true;
						$GrupoTrabalhoDAO->cReturnMsg  = 'Grupo de Trabalho excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$GrupoTrabalhoDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($GrupoTrabalhoDAO->Restaura($GrupoTrabalho)) {
				 		$lOk = true;
						$GrupoTrabalhoDAO->cReturnMsg  = 'Grupo de Trabalho restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$GrupoTrabalhoDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new logEdicaoDAO();
			$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'grupo_trabalho', $nRetId, $postData->ctrlaction, 'grupo_trabalho', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$GrupoTrabalhoDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
