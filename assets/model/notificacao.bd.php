<?php
	session_start();
	include('class/security.class.php');
	include('class/notificacaoDAO.class.php');
	include('class/notificacao.class.php');

	$security = new Security();
	if ( $security->Exist() ) {
		$post = file_get_contents("php://input");

		if($post){
			$postData 			= json_decode($post);
			$notificacaoDAO 	= new notificacaoDAO();

			if ($postData->ctrlaction == 'new') {
				$ntf_id 		= 0;
			} else {
				$ntf_id 		= $postData->ntf_id;
			}
			$ntf_dest_user_id	= $postData->ntf_dest_user_id;
			$ntf_data_hora		= (isset($postData->ntf_data_hora) ? $postData->ntf_data_hora : Date('Y-m-d H:i:s'));
			$ntf_tipo_alerta	= $postData->ntf_tipo_alerta;
			$ntf_notificacao	= $postData->ntf_notificacao;
			$ntf_url			= $postData->ntf_url;
			$ntf_lida			= (isset($postData->ntf_lida) ? $postData->ntf_lida : 'N');;

			$Notificacao 		= new Notificacao($ntf_id,$ntf_dest_user_id,$ntf_data_hora,$ntf_tipo_alerta,$ntf_notificacao,$ntf_url,$ntf_lida);
 
			$nRetId = $ntf_id;

			if ($postData->ctrlaction == 'new') {
				if ($notificacaoDAO->Insere($Notificacao)) {
					$lOk 	= true;
					$nRetId = $notificacaoDAO->id_inserido;
				} else {
					$lOk = false;
				}	
			} elseif ($postData->ctrlaction == 'edit') {			
				if ($notificacaoDAO->Altera($Notificacao)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			} elseif ($postData->ctrlaction == 'erase') {
				if ($notificacaoDAO->Apaga($Notificacao)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			}

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>