<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');

	$logEdicaoDAO 	= new LogEdicaoDAO();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('registrationlog', SEC_USER_PFA_ID);
		
		if ( $aUserRotina <> false ) {
			if (!Empty($_GET)) {
				if ( isset($_GET['ledTk']) ) {
					$aLogCadastro = $logEdicaoDAO->buscaById($_GET['ledTk']);
				}
			} else {
				$post = file_get_contents("php://input");	
				if($post){
					$postData 		= json_decode($post);

					$led_id 	= ($SEC_USER_TIPO <> 3 ? (isset($postData->filter_user) ? $postData->filter_user : null) : SEC_USER_ID);
					$led_action	= (isset($postData->filter_action) ? $postData->filter_action : null);

					$aLogCadastro = $logEdicaoDAO->buscaFilters($led_id, $led_action);
				} else {
					if ( SEC_USER_TIPO <> 3 ) {
						$aLogCadastro = $logEdicaoDAO->buscaAll();
					} else {
						$aLogCadastro = $logEdicaoDAO->buscaByUser(SEC_USER_ID);
					}
				}
		
			}

			echo json_encode($aLogCadastro);
		} else {
			echo json_encode(array('error' => 'Usuario sem acesso a essa rotina.'));			
		}
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>



