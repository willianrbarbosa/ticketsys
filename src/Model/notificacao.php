<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\NotificacaoDAO;
	include('session_vars.php');
		
	$notificacaoDAO 	= new NotificacaoDAO();

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
					$aNotificacoes = $notificacaoDAO->buscaByUser($_GET['userTK'], 20);
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