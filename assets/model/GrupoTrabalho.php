<?php
	session_start();

	include('class/security.class.php');
	include('class/GrupoTrabalhoDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();
	$GrupoTrabalhoDAO = new GrupoTrabalhoDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('grupo_trabalho', SEC_USER_PFA_ID);

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

			if ( isset($_GET['grtTK']) ) {
				$aGrupoTrabalho = $GrupoTrabalhoDAO->buscaByID($_GET['grtTK'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aGrupoTrabalho = $GrupoTrabalhoDAO->buscaAllDeleted($cWhere);
			} else {
				$aGrupoTrabalho = $GrupoTrabalhoDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aGrupoTrabalho);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>
