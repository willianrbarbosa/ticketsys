<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotina;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;

	$security = new Security();
	$logEdicaoDAO 	= new LogEdicaoDAO();
	if ( $security->Exist() ) {

		$post = file_get_contents("php://input");

		if($post){
			$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('accessprofiles', $security->getUser_pfa_id());
	
			if ( $aUserRotina == false ) {
				echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
				exit;
			}

			$postData 			= json_decode($post);

			$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();	

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
								
								$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'accessprofiles', $nRetId, $postData[0]->ctrlaction, 'perfil_rotina', Date('Y-m-d H:i:s'));
								$logEdicaoDAO->Insere($logEdicao);
							}
						}
					}
				}	
			}

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>