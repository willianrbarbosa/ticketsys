<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\PerfilAcessoDAO;
	use TicketSys\Model\Classes\PerfilAcesso;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();
	
	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('accessprofiles', SEC_USER_PFA_ID);
		
		if ( $aUserRotina <> false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if($post){
			$postData 			= json_decode($post);
			$perfilAcessoDAO 	= new PerfilAcessoDAO();	

			$pfa_id 		= $postData->pfa_id;
			$pfa_descricao 	= $postData->pfa_descricao;

		    $perfilAcesso = new PerfilAcesso($pfa_id,$pfa_descricao);

			$nRetId = $pfa_id;

			if ($postData->ctrlaction == 'new') {
				if ($perfilAcessoDAO->Insere($perfilAcesso)) {
					$lOk 	= true;
					$nRetId = $perfilAcessoDAO->id_inserido;
				} else {
					$lOk = false;
				}	
			} elseif ($postData->ctrlaction == 'edit') {			
				if ($perfilAcessoDAO->Altera($perfilAcesso)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			} elseif ($postData->ctrlaction == 'delete') {			
				if ($perfilAcessoDAO->Deleta($perfilAcesso)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			} elseif ($postData->ctrlaction == 'restaura') {			
				if ($perfilAcessoDAO->Restaura($perfilAcesso)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			}
		
			$logEdicaoDAO 	= new LogEdicaoDAO();
			$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'accessprofile', $nRetId, $postData->ctrlaction, 'perfil_acesso', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>