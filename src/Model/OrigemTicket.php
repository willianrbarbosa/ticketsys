<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\OrigemTicketDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');
	
	$OrigemTicketDAO = new OrigemTicketDAO();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('origem_ticket', SEC_USER_PFA_ID);

		if ( $aUserRotina == false ) {
			$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('triagemTicket', SEC_USER_PFA_ID);

			if ( $aUserRotina == false ) {
				$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticketkanban', SEC_USER_PFA_ID);

				if ( $aUserRotina == false ) {
					$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket', SEC_USER_PFA_ID);

					if ( $aUserRotina == false ) {
						echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
						exit;
					}
				}
			}
		}

		if (!Empty($_GET)) {

			$cWhere			= '';
			if ( (isset($_GET['cFiltro'])) ) {
				$cWhere		= $_GET['cFiltro'];
				$cWhere		= str_replace("*", "%", $cWhere);
			}

			if ( isset($_GET['ortTK']) ) {
				$aOrigemTicket = $OrigemTicketDAO->buscaByID($_GET['ortTK'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aOrigemTicket = $OrigemTicketDAO->buscaAllDeleted($cWhere);
			} else {
				$aOrigemTicket = $OrigemTicketDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aOrigemTicket);
	}
?>
