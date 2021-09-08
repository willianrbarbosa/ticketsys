<?php
	session_start();

	include('class/security.class.php');
	include('class/notificacaoDAO.class.php');
	include('session_vars.php');

	$security 			= new Security();	
	$notificacaoDAO 	= new notificacaoDAO();

	if ( SESSION_EXISTS ) {
		if (!Empty($_GET)) {
			if ( isset($_GET['ntfTk']) ) {
				$aNotificacoes = $notificacaoDAO->buscaById($_GET['ntfTk']);
			} elseif ( isset($_GET['userTK']) ) {
				if ( $_GET['userTK'] == 'null' ) {
					$aNotificacoes = $notificacaoDAO->buscaByUser(SEC_USER_ID, 0);
				} elseif ( $_GET['userTK'] == 'index' ) {
					$aNotificacoes = $notificacaoDAO->buscaUnreadByUser(SEC_USER_ID, 20);
				} else {
					$aNotificacoes = $notificacaoDAO->buscaByUser($_GET['userTK']);
				}
			} elseif ( isset($_GET['user_nao_lida']) ) {
				$aNotificacoes = $notificacaoDAO->buscaUnreadByUser($_GET['user_nao_lida']);
			}
		} else {
			$aNotificacoes = $notificacaoDAO->buscaAll();
		}
		echo json_encode($aNotificacoes);

	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>