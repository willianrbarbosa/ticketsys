<?php
	session_start();

	include('class/security.class.php');
	include('class/TicketHistoricoDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();
	$TicketHistoricoDAO = new TicketHistoricoDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_historico', SEC_USER_PFA_ID);

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		if (!Empty($_GET)) {

			$cWhere			= '';
			if ( (isset($_GET['cFiltro'])) ) {
				$cWhere		= $_GET['cFiltro'];
				$cWhere		= str_replace("*", "%", $cWhere);
			}

			if ( isset($_GET['tkhTK']) ) {
				$aTicketHistorico = $TicketHistoricoDAO->buscaByID($_GET['tkhTK'], $cWhere);
			} elseif ( isset($_GET['tkh_tkt_id']) ) {
				$aTicketHistorico = $TicketHistoricoDAO->buscaByTicket($_GET['tkh_tkt_id'], $cWhere);
			} elseif ( isset($_GET['tkh_user_id']) ) {
				$aTicketHistorico = $TicketHistoricoDAO->buscaByUser_id($_GET['tkh_user_id'], $cWhere);
			} elseif ( isset($_GET['tkh_data_hora']) ) {
				$aTicketHistorico = $TicketHistoricoDAO->buscaByData_hora($_GET['tkh_data_hora'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aTicketHistorico = $TicketHistoricoDAO->buscaAllDeleted($cWhere);
			} else {
				$aTicketHistorico = $TicketHistoricoDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aTicketHistorico);
	}
?>
