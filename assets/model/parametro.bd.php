<?php
	session_start();
	
	include('class/security.class.php');
	include('class/parametroDAO.class.php');
	include('class/parametro.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	if ( $security->Exist() ) {
		$post = file_get_contents("php://input");

		if($post){
			$postData = json_decode($post);
			$parametroDAO = new parametroDAO();	

			if ($postData->ctrlaction == 'ncm_homologa') {	

				if ( !Empty($postData->NCMs) ) {
					$par_conteudo = '[';
					for ($n=0; $n < count($postData->NCMs); $n++) { 
						$par_conteudo .= ($n > 0 ? ',' : '').'"'.$postData->NCMs[$n]->ncm_id.'"';
					}
					$par_conteudo .= ']';

					$aParametro 	= $parametroDAO->buscaByKey('NCMHOMOLOG');
					
					$par_key 		= 'NCMHOMOLOG';
					$par_descricao 	= $aParametro['par_descricao'];
				} else {
					$par_key 		= 'NCMHOMOLOG';
					$par_conteudo 	= null;
					$par_descricao 	= $aParametro['par_descricao'];
				}
				$Parametro = new Parametro($par_key,$par_conteudo,$par_descricao);

				$nRetId = $par_key;
				if ($parametroDAO->Altera($Parametro)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			} else {
				$par_key 		= $postData->par_key;
				$par_conteudo 	= $postData->par_conteudo;
				$par_descricao 	= $postData->par_descricao;

				$Parametro = new Parametro($par_key,$par_conteudo,$par_descricao);

				$nRetId = $par_key;
				if ($postData->ctrlaction == 'new') {
					if ($parametroDAO->Insere($Parametro)) {
						$lOk = true;
					} else {
						$lOk = false;
					}	
				} elseif ($postData->ctrlaction == 'edit') {			
					if ($parametroDAO->Altera($Parametro)) {
						$lOk = true;
					} else {
						$lOk = false;
					}
				}/* elseif ($postData->ctrlaction == 'delete') {			
					if ($parametroDAO->Deleta($Parametro)) {
						$lOk = true;
					} else {
						$lOk = false;
					}
				}*/
			}
		
			$logEdicaoDAO 	= new logEdicaoDAO();
			$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'parameters', $nRetId, 'edit', 'parametro', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>