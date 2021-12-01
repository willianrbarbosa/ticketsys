<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\IndicadoresDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');

	$indicadoresDAO = new IndicadoresDAO();
	$PerfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $PerfilAcessoRotinaDAO->buscaByPerfilRotina('indicators', SEC_USER_PFA_ID);
		
		if ( $aUserRotina <> false ) {
			if (!Empty($_GET)) {
				$aIndicadores = $indicadoresDAO->buscaByChave($_GET['indKey']);
			} else {
				$aIndicadores = $indicadoresDAO->buscaAll();
			}

			echo json_encode($aIndicadores);
		} else {
			echo json_encode(array('error' => 'Usuario sem acesso a essa rotina.'));			
		}
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>