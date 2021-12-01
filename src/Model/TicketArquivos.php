<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\TicketArquivosDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;
	include('session_vars.php');
	
	$TicketArquivosDAO = new TicketArquivosDAO();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

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
