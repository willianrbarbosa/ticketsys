<?php
	session_start();
	
	include('class/security.class.php');
	include('class/usuarioFavoritoDAO.class.php');
	include('session_vars.php');

	$security = new Security();	
	$usuarioFavoritoDAO = new usuarioFavoritoDAO();
 
	if ( SESSION_EXISTS ) {

		if (!Empty($_GET)) {
			if ( isset($_GET['ufvTk']) ) {
				$aUserFavoritos = $usuarioFavoritoDAO->buscaById($_GET['ufvTk']);
			} elseif ( isset($_GET['userTK']) ) {
				if ( $_GET['userTK'] == 'null' ) {
					$aUserFavoritos = $usuarioFavoritoDAO->buscaByUserId(SEC_USER_ID);
				} else {
					$aUserFavoritos = $usuarioFavoritoDAO->buscaByUserId($_GET['userTK']);
				}
			}
		} else {
			$aUserFavoritos = array();
		}

		echo json_encode($aUserFavoritos);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>