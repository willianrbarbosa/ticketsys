<?php
	session_start();

	include('class/security.class.php');
	include('class/TipoAtividadeDAO.class.php');
	include('class/TipoAtividade.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('tipo_atividade', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData = json_decode($post);
			$TipoAtividadeDAO = new TipoAtividadeDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$tav_id 	= 0;
			} else {
				$tav_id 	= $postData->tav_id;
			}
			$tav_descricao 	= $postData->tav_descricao;

			$TipoAtividade = new TipoAtividade($tav_id,$tav_descricao);

			$nRetId = $tav_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($TipoAtividadeDAO->Insere($TipoAtividade)) {
				 		$lOk = true;
						$nRetId = $TipoAtividadeDAO->id_inserido;
						$TipoAtividadeDAO->cReturnMsg  = 'Tipo de Atividade inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TipoAtividadeDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($TipoAtividadeDAO->Altera($TipoAtividade)) {
				 		$lOk = true;
						$TipoAtividadeDAO->cReturnMsg  = 'Tipo de Atividade alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TipoAtividadeDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TipoAtividadeDAO->Deleta($TipoAtividade)) {
				 		$lOk = true;
						$TipoAtividadeDAO->cReturnMsg  = 'Tipo de Atividade excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TipoAtividadeDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TipoAtividadeDAO->Restaura($TipoAtividade)) {
				 		$lOk = true;
						$TipoAtividadeDAO->cReturnMsg  = 'Tipo de Atividade restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TipoAtividadeDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			$logEdicaoDAO 	= new logEdicaoDAO();
			$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'tipo_atividade', $nRetId, $postData->ctrlaction, 'tipo_atividade', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "msg"=>$TipoAtividadeDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
