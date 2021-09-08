<?php
	session_start();
	
	include('class/security.class.php');
	include('class/indicadoresDAO.class.php');
	include('class/indicadores.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	if ( $security->Exist() ) {
		$post = file_get_contents("php://input");

		if($post){
			$postData 		= json_decode($post);
			$indicadoresDAO = new indicadoresDAO();	

			$ind_chave 		= strtoupper($postData->ind_chave);
			$ind_param 		= $postData->ind_param;
			$ind_valor 		= str_replace(",",".", $postData->ind_valor);
			$ind_descricao 	= $postData->ind_descricao;

		    $Indicadores = new Indicadores($ind_chave,$ind_param,$ind_valor,$ind_descricao);

			$nRetId = $ind_chave;
			if ($postData->ctrlaction == 'new') {
				if ($indicadoresDAO->Insere($Indicadores)) {
					$lOk = true;
				} else {
					$lOk = false;
				}	
			} elseif ($postData->ctrlaction == 'edit') {			
				if ($indicadoresDAO->Altera($Indicadores)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			}/* elseif ($postData->ctrlaction == 'delete') {			
				if ($indicadoresDAO->Deleta($Indicadores)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			}*/
		
			$logEdicaoDAO 	= new logEdicaoDAO();
			$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'indicators', $nRetId, 'edit', 'indicadores', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>