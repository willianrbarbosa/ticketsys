<?php
	session_start();

	include('class/security.class.php');
	include('class/PrioridadeTicketDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();
	$PrioridadeTicketDAO = new PrioridadeTicketDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('prioridade_ticket', SEC_USER_PFA_ID);

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

			if ( isset($_GET['prtTK']) ) {
				$aPrioridadeTicket = $PrioridadeTicketDAO->buscaByID($_GET['prtTK'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aPrioridadeTicket = $PrioridadeTicketDAO->buscaAllDeleted($cWhere);
			} else {
				$aPrioridadeTicket = $PrioridadeTicketDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aPrioridadeTicket);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>
