<?php
	session_start();
	
		ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);
	
	include('class/security.class.php');
	include('class/usuarioDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('session_vars.php');

	$security 				= new Security();	
	$usuarioDAO 			= new usuarioDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('users', SEC_USER_PFA_ID);
		
		if ( $aUserRotina == false ) {
			$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('usersaccesslog', SEC_USER_PFA_ID);
		}
		
		if ( $aUserRotina <> false ) {
			if (!Empty($_GET)) {
				if ((isset($_GET['userTK'])) AND (isset($_GET['data_de'])) AND (isset($_GET['data_ate']))) {
					$aUsers = $usuarioDAO->buscaUsersAcessosByFilter($_GET['userTK'],$_GET['data_de'],$_GET['data_ate']);
				} elseif ( isset($_GET['userTK']) ) {
					if ( $_GET['userTK'] == 'null' ) {
						$aUsers 	= $usuarioDAO->buscaByToken(SEC_USER_TOKEN);						
					} else {
						$aUsers = $usuarioDAO->buscaByToken($_GET['userTK']);
					}
				} elseif ( isset($_GET['userEM']) ) {
					$aUsers = $usuarioDAO->buscaByEmail($_GET['userEM']);
				} elseif ( isset($_GET['usaUserId']) ) {
					$aUsers = $usuarioDAO->buscaUserAcesso($_GET['usaUserId']);
				} elseif ( isset($_GET['fileStatus']) ) {
					$aUsers = $usuarioDAO->buscaByPrcFiles($_GET['fileStatus']);
				}elseif ( isset($_GET['allUsers']) ) {
					$aUsers = $usuarioDAO->buscaUsersAcessos();
				}elseif ( isset($_GET['userTipo']) ) {
					$aUsers = $usuarioDAO->buscaByTipo($_GET['userTipo']);
				}elseif ( isset($_GET['userRespTicket']) ) {
					$aUsers = $usuarioDAO->buscaRespTicket($_GET['userRespTicket']);
				}elseif ( isset($_GET['inativos']) ) {
					$aUsers = $usuarioDAO->buscaInativos();
				} elseif ( isset($_GET['prodpenddesempenho']) ) {
					$aUsers = $usuarioDAO->buscaDesempenhoProdPendentesUsuarioDia(SEC_USER_TOKEN, Date('Y-m-d'));
				}  
			} else {
				$aUsers = $usuarioDAO->buscaAll();
			}

			echo json_encode($aUsers);
		} else {
			if ( isset($_GET['userTK']) ) {
				if ( ($_GET['userTK'] == 'null') OR ($_GET['userTK'] == SEC_USER_TOKEN) ) {
					$aUsers = $usuarioDAO->buscaByToken(SEC_USER_TOKEN);
					echo json_encode($aUsers);
				} else {
					echo json_encode(array('error' => 'Usuario sem acesso a essa rotina.'));
				}
			} elseif ( isset($_GET['fileStatus']) ) {
				$aUsers = $usuarioDAO->buscaByPrcFiles($_GET['fileStatus']);
				echo json_encode($aUsers);
			} elseif ( isset($_GET['prodpenddesempenho']) ) {
				$aUserDesempenho = $usuarioDAO->buscaDesempenhoProdPendentesUsuarioDia(SEC_USER_TOKEN, Date('Y-m-d'));
				echo json_encode($aUserDesempenho);
			}elseif ( isset($_GET['userTipo']) ) {
				$aUsers = $usuarioDAO->buscaByTipo($_GET['userTipo']);
				echo json_encode($aUsers);
			}elseif ( isset($_GET['userRespTicket']) ) {
				$aUsers = $usuarioDAO->buscaRespTicket($_GET['userRespTicket']);
				echo json_encode($aUsers);
			} else {
				if ( SEC_USER_TIPO <> 3 ) {
					$aUsers = $usuarioDAO->buscaAll();
				} else {
					$aUsers = $usuarioDAO->buscaByToken(SEC_USER_TOKEN);
				}
				echo json_encode($aUsers);
			}
		}
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>