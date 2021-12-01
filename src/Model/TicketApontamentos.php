<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\TicketApontamentosDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;
	include('session_vars.php');
	
	$TicketApontamentosDAO = new TicketApontamentosDAO();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_apontamentos', SEC_USER_PFA_ID);

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

			if ( isset($_GET['tkpTK']) ) {
				$aTicketApontamentos = $TicketApontamentosDAO->buscaByID($_GET['tkpTK'], $cWhere);
			} elseif ( isset($_GET['tkp_tkt_id']) ) {
				$aTicketApontamentos = $TicketApontamentosDAO->buscaByTicket($_GET['tkp_tkt_id'], $cWhere);
			} elseif ( isset($_GET['tkp_user_id']) ) {
				$aTicketApontamentos = $TicketApontamentosDAO->buscaByUser_id($_GET['tkp_user_id'], $cWhere);
			} elseif ( isset($_GET['tkp_data']) ) {
				$aTicketApontamentos = $TicketApontamentosDAO->buscaByData($_GET['tkp_data'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aTicketApontamentos = $TicketApontamentosDAO->buscaAllDeleted($cWhere);
			} else {
				$aTicketApontamentos = $TicketApontamentosDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aTicketApontamentos);
	}
?>
