<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\PerfilAcessoDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');

	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();
	$perfilAcessoDAO 		= new PerfilAcessoDAO();

	if ( SESSION_EXISTS ) {
		if (!Empty($_GET)) {
			if ( (isset($_GET['rtuTK'])) AND (isset($_GET['userTK'])) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina($_GET['rtuTK'], $_GET['userTK']);
			} elseif ( isset($_GET['verifTipo']) ) {
				/*if(SEC_USER_TIPO == 3){
					$perfilAcesso  = $perfilAcessoDAO->buscaPerfilByCliPlano($security->getUserCliente());
					$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfil($perfilAcesso['pfa_id']);
					$security->setUser_pfa_id($perfilAcesso['pfa_id']);
    				define("SEC_USER_PFA_ID", $perfilAcesso['pfa_id']);
				}else{
					$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfil(SEC_USER_PFA_ID);
				}*/
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfil(SEC_USER_PFA_ID);
			} elseif ( isset($_GET['rot']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaRotinas();
			} elseif ( isset($_GET['ptapfaTK']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->loadRotinaSelecionadasPerfil($_GET['ptapfaTK']);
			} elseif ( isset($_GET['pfaTK']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfil($_GET['pfaTK']);
			} elseif ( isset($_GET['rtuTK']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByRotina($_GET['rtuTK']);
			} elseif ( isset($_GET['lgdrTK']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->CheckUserLoggedRotina($_GET['lgdrTK'],SEC_USER_PFA_ID);
			}
		} else {
			$aPerfilRotina = $perfilAcessoRotinaDAO->buscaAll();
		}

		echo json_encode($aPerfilRotina);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>