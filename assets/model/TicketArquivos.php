<?php
	session_start();
	
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);

	include('class/security.class.php');
	include('class/TicketArquivosDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();
	$TicketArquivosDAO = new TicketArquivosDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_arquivos', SEC_USER_PFA_ID);

		if ( $aUserRotina == false ) {
			$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket', SEC_USER_PFA_ID);

			if ( $aUserRotina == false ) {
				$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('triagemTicket', SEC_USER_PFA_ID);

				if ( $aUserRotina == false ) {
					echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
					exit;
				}
			}
		}

		if (!Empty($_GET)) {

			$cWhere			= '';
			if ( (isset($_GET['cFiltro'])) ) {
				$cWhere		= $_GET['cFiltro'];
				$cWhere		= str_replace("*", "%", $cWhere);
			}

			if ( isset($_GET['tkaTK']) ) {
				$aTicketArquivos = $TicketArquivosDAO->buscaByID($_GET['tkaTK'], $cWhere);
			} elseif ( isset($_GET['tka_tkt_id']) ) {
				$aTicketArquivos = $TicketArquivosDAO->buscaByTkt_id($_GET['tka_tkt_id'], $cWhere);
			} elseif ( isset($_GET['tka_user_id']) ) {
				$aTicketArquivos = $TicketArquivosDAO->buscaByUser_id($_GET['tka_user_id'], $cWhere);
			} elseif ( isset($_GET['tka_data_hora']) ) {
				$aTicketArquivos = $TicketArquivosDAO->buscaByData_hora($_GET['tka_data_hora'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aTicketArquivos = $TicketArquivosDAO->buscaAllDeleted($cWhere);
			} else {
				$aTicketArquivos = $TicketArquivosDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aTicketArquivos);
	}
?>
