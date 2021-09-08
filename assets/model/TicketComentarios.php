<?php
	session_start();

	include('class/security.class.php');
	include('class/TicketComentariosDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();
	$TicketComentariosDAO = new TicketComentariosDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_comentarios', SEC_USER_PFA_ID);

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

			if ( isset($_GET['tkcTK']) ) {
				$aTicketComentarios = $TicketComentariosDAO->buscaByID($_GET['tkcTK'], $cWhere);
			} elseif ( isset($_GET['tkc_tkt_id']) ) {
				$aTicketComentarios = $TicketComentariosDAO->buscaByTicket($_GET['tkc_tkt_id'], $cWhere);
			} elseif ( isset($_GET['tkc_user_id']) ) {
				$aTicketComentarios = $TicketComentariosDAO->buscaByUser_id($_GET['tkc_user_id'], $cWhere);
			} elseif ( isset($_GET['tkc_data_hora']) ) {
				$aTicketComentarios = $TicketComentariosDAO->buscaByData_hora($_GET['tkc_data_hora'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aTicketComentarios = $TicketComentariosDAO->buscaAllDeleted($cWhere);
			} else {
				$aTicketComentarios = $TicketComentariosDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aTicketComentarios);
	}
?>
