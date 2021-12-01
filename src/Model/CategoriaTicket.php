<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\CategoriaTicketDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');
	
	$CategoriaTicketDAO = new CategoriaTicketDAO();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('categoria_ticket', SEC_USER_PFA_ID);

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

			if ( isset($_GET['cgtTK']) ) {
				$aCategoriaTicket = $CategoriaTicketDAO->buscaByID($_GET['cgtTK'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aCategoriaTicket = $CategoriaTicketDAO->buscaAllDeleted($cWhere);
			} else {
				$aCategoriaTicket = $CategoriaTicketDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aCategoriaTicket);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>
