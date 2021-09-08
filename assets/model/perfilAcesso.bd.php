<?php
	session_start();
	include('class/security.class.php');
	include('class/perfilAcessoDAO.class.php');
	include('class/perfilAcesso.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	if ( $security->Exist() ) {
		$post = file_get_contents("php://input");

		if($post){
			$postData 			= json_decode($post);
			$perfilAcessoDAO 	= new perfilAcessoDAO();	

			$pfa_id 		= $postData->pfa_id;
			$pfa_descricao 	= $postData->pfa_descricao;

		    $perfilAcesso = new perfilAcesso($pfa_id,$pfa_descricao);

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
		
			$logEdicaoDAO 	= new logEdicaoDAO();
			$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'accessprofile', $nRetId, $postData->ctrlaction, 'perfil_acesso', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>