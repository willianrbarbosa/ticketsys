<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\ParametroDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');
	
	$parametroDAO = new ParametroDAO();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('parameters', SEC_USER_PFA_ID);
		
		if ( $aUserRotina == false ) {
			$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('clientprods', SEC_USER_PFA_ID);

			if ( $aUserRotina == false ) {
				echo json_encode(array('error' => 'Usuario sem acesso a essa rotina.'));			
				exit;
			}
		}
		
		if (!Empty($_GET)) {
			$aParametros = $parametroDAO->buscaByKey($_GET['parKey']);
		} else {
			$aParametros = $parametroDAO->buscaAll();
		}

		echo json_encode($aParametros);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>