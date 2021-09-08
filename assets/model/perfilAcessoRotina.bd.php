<?php
	session_start();
	include('class/security.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/perfilAcessoRotina.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	if ( $security->Exist() ) {

		$post = file_get_contents("php://input");

		if($post){
			$postData 			= json_decode($post);

			$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();	

			$nRetId =  $postData[0]->perfilID;

			if ($postData[0]->ctrlaction == 'new') {
				if ($perfilAcessoRotinaDAO->ApagaByPerfil($postData[0]->perfilID)) {
					for ($i=0; $i < count($postData); $i++) { 
						if ( (isset($postData[$i]->selecionado)) AND (!Empty($postData[$i]->selecionado)) ) {
							if($postData[$i]->selecionado == 'true'){
								$pta_rot_nome 	= $postData[$i]->rot_nome;
								$pta_pfa_id 	= $postData[0]->perfilID;
								$pta_nivel 		= $postData[$i]->nivel;
								$pta_user_atrib = $security->getUser_id();
									
							    $perfilAcessoRotina = new perfilAcessoRotina($pta_rot_nome,$pta_pfa_id,$pta_nivel,$pta_user_atrib);

								if ($perfilAcessoRotinaDAO->Insere($perfilAcessoRotina)) {
									$lOk 	= true;
								} else {
									$lOk = false;
									break;
								}
							}
						}
					}
				}	
			}

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>