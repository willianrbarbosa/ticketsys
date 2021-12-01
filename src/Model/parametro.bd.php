<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\ParametroDAO;
	use TicketSys\Model\Classes\Parametro;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('parameters', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if($post){
			$postData = json_decode($post);
			$parametroDAO = new ParametroDAO();	

		
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
			}
		
			$logEdicaoDAO 	= new LogEdicaoDAO();
			$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'parameters', $nRetId, 'edit', 'parametro', Date('Y-m-d H:i:s'));
			$logEdicaoDAO->Insere($logEdicao);

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>