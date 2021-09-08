<?php
	session_start();

	include('class/security.class.php');
	include('class/TipoAtividadeDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();
	$TipoAtividadeDAO = new TipoAtividadeDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('tipo_atividade', SEC_USER_PFA_ID);

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

			if ( isset($_GET['tavTK']) ) {
				$aTipoAtividade = $TipoAtividadeDAO->buscaByID($_GET['tavTK'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aTipoAtividade = $TipoAtividadeDAO->buscaAllDeleted($cWhere);
			} else {
				$aTipoAtividade = $TipoAtividadeDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aTipoAtividade);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>
