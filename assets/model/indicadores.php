<?php
	session_start();
	
	include('class/security.class.php');
	include('class/indicadoresDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();	
	$indicadoresDAO = new indicadoresDAO();
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