<?php
	session_start();
	
	include('class/security.class.php');
	include('class/parametroDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();	
	$parametroDAO = new parametroDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

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