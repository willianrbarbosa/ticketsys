<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\TicketUsuariosDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;
	include('session_vars.php');
	
	$TicketUsuariosDAO = new TicketUsuariosDAO();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_usuarios', SEC_USER_PFA_ID);

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

			if ( isset($_GET['tkuTK']) ) {
				$aTicketUsuarios = $TicketUsuariosDAO->buscaByID($_GET['tkuTK'], $cWhere);
			} elseif ( isset($_GET['tku_tkt_id']) ) {
				$aTicketUsuarios = $TicketUsuariosDAO->buscaByTicket($_GET['tku_tkt_id'], $cWhere);
			} elseif ( isset($_GET['tku_user_id']) ) {
				$aTicketUsuarios = $TicketUsuariosDAO->buscaByUser_id($_GET['tku_user_id'], $cWhere);
			} elseif ( isset($_GET['tku_tipo']) ) {
				$aTicketUsuarios = $TicketUsuariosDAO->buscaByTipo($_GET['tku_tipo'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aTicketUsuarios = $TicketUsuariosDAO->buscaAllDeleted($cWhere);
			} else {
				$aTicketUsuarios = $TicketUsuariosDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aTicketUsuarios);
	}
?>
