<?php
	session_start();

	include('class/security.class.php');
	include('class/PastaTrabalhoDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security = new Security();
	$PastaTrabalhoDAO = new PastaTrabalhoDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('pasta_trabalho', SEC_USER_PFA_ID);

		if ( $aUserRotina == false ) {
			$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('triagemTicket', SEC_USER_PFA_ID);

			if ( $aUserRotina == false ) {
				$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticketkanban', SEC_USER_PFA_ID);

				if ( $aUserRotina == false ) {
					$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket', SEC_USER_PFA_ID);

					if ( $aUserRotina == false ) {
						$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('users', SEC_USER_PFA_ID);

						if ( $aUserRotina == false ) {
							echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
							exit;
						}
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

			if ( isset($_GET['pstTK']) ) {
				$aPastaTrabalho = $PastaTrabalhoDAO->buscaByID($_GET['pstTK'], $cWhere);
			} elseif ( isset($_GET['pst_grt_id']) ) {
				$aPastaTrabalho = $PastaTrabalhoDAO->buscaByGrt_id($_GET['pst_grt_id'], $cWhere);
			} elseif(isset($_GET['delete'])) {
				$aPastaTrabalho = $PastaTrabalhoDAO->buscaAllDeleted($cWhere);
			} else {
				$aPastaTrabalho = $PastaTrabalhoDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aPastaTrabalho);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>
