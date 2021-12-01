<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\UsuarioDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');

	$UsuarioDAO 			= new UsuarioDAO();
	$PerfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $PerfilAcessoRotinaDAO->buscaByPerfilRotina('users', SEC_USER_PFA_ID);
		
		if ( $aUserRotina == false ) {
			$aUserRotina = $PerfilAcessoRotinaDAO->buscaByPerfilRotina('usersaccesslog', SEC_USER_PFA_ID);
		}
		
		if ( $aUserRotina <> false ) {
			if (!Empty($_GET)) {
				if ((isset($_GET['userTK'])) AND (isset($_GET['data_de'])) AND (isset($_GET['data_ate']))) {
					$aUsers = $UsuarioDAO->buscaUsersAcessosByFilter($_GET['userTK'],$_GET['data_de'],$_GET['data_ate']);
				} elseif ( isset($_GET['userTK']) ) {
					if ( $_GET['userTK'] == 'null' ) {
						$aUsers 	= $UsuarioDAO->buscaByToken(SEC_USER_TOKEN);						
					} else {
						$aUsers = $UsuarioDAO->buscaByToken($_GET['userTK']);
					}
				} elseif ( isset($_GET['userEM']) ) {
					$aUsers = $UsuarioDAO->buscaByEmail($_GET['userEM']);
				} elseif ( isset($_GET['usaUserId']) ) {
					$aUsers = $UsuarioDAO->buscaUserAcesso($_GET['usaUserId']);
				}elseif ( isset($_GET['allUsers']) ) {
					$aUsers = $UsuarioDAO->buscaUsersAcessos();
				}elseif ( isset($_GET['userTipo']) ) {
					$aUsers = $UsuarioDAO->buscaByTipo($_GET['userTipo']);
				}elseif ( isset($_GET['userRespTicket']) ) {
					$aUsers = $UsuarioDAO->buscaRespTicket($_GET['userRespTicket']);
				}elseif ( isset($_GET['inativos']) ) {
					$aUsers = $UsuarioDAO->buscaInativos();
				}  
			} else {
				$aUsers = $UsuarioDAO->buscaAll();
			}

			echo json_encode($aUsers);
		} else {
			if ( isset($_GET['userTK']) ) {
				if ( ($_GET['userTK'] == 'null') OR ($_GET['userTK'] == SEC_USER_TOKEN) ) {
					$aUsers = $UsuarioDAO->buscaByToken(SEC_USER_TOKEN);
					echo json_encode($aUsers);
				} else {
					echo json_encode(array('error' => 'Usuario sem acesso a essa rotina.'));
				}
			} elseif ( isset($_GET['userTipo']) ) {
				$aUsers = $UsuarioDAO->buscaByTipo($_GET['userTipo']);
				echo json_encode($aUsers);
			} elseif ( isset($_GET['userRespTicket']) ) {
				$aUsers = $UsuarioDAO->buscaRespTicket($_GET['userRespTicket']);
				echo json_encode($aUsers);
			} else {
				if ( SEC_USER_TIPO <> 3 ) {
					$aUsers = $UsuarioDAO->buscaAll();
				} else {
					$aUsers = $UsuarioDAO->buscaByToken(SEC_USER_TOKEN);
				}
				echo json_encode($aUsers);
			}
		}
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>