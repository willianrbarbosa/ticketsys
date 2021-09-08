<?php
	session_start();

	include('class/security.class.php');
	include('class/SituacaoTicketDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();
	$SituacaoTicketDAO = new SituacaoTicketDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('situacao_ticket', SEC_USER_PFA_ID);

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

			if ( isset($_GET['sttTK']) ) {
				$aSituacaoTicket = $SituacaoTicketDAO->buscaByID($_GET['sttTK'], $cWhere);
			} elseif ( isset($_GET['cKanban']) ) {
				$aSituacaoTicket = $SituacaoTicketDAO->buscaByKanban($_GET['cKanban'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aSituacaoTicket = $SituacaoTicketDAO->buscaAllDeleted($cWhere);
			} else {
				$aSituacaoTicket = $SituacaoTicketDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aSituacaoTicket);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>
