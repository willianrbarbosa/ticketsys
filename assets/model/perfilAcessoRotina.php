<?php
	session_start();
	
	include('class/security.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/perfilAcessoDAO.class.php');
	include('class/usuarioDAO.class.php');
	// include('session_vars.php');

	$security 				= new Security();	
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();
	$perfilAcessoDAO 		= new perfilAcessoDAO();
	$usuarioDAO				= new usuarioDAO();

	if ( $security->Exist() ) {
		if (!Empty($_GET)) {
			if ( (isset($_GET['rtuTK'])) AND (isset($_GET['userTK'])) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina($_GET['rtuTK'], $_GET['userTK']);
			} elseif ( isset($_GET['verifTipo']) ) {
				if($security->getUser_tipo() == 3){
					$perfilAcesso  = $perfilAcessoDAO->buscaPerfilByCliPlano($security->getUserCliente());
					$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfil($perfilAcesso['pfa_id']);
					$security->setUser_pfa_id($perfilAcesso['pfa_id']);
    				define("SEC_USER_PFA_ID", $perfilAcesso['pfa_id']);
				}else{
					$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfil($security->getUser_pfa_id());
				}
			} elseif ( isset($_GET['rot']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaRotinas();
			} elseif ( isset($_GET['ptapfaTK']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->loadRotinaSelecionadasPerfil($_GET['ptapfaTK']);
			} elseif ( isset($_GET['pfaTK']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByPerfil($_GET['pfaTK']);
			} elseif ( isset($_GET['rtuTK']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->buscaByRotina($_GET['rtuTK']);
			} elseif ( isset($_GET['lgdrTK']) ) {
				$aPerfilRotina = $perfilAcessoRotinaDAO->CheckUserLoggedRotina($_GET['lgdrTK'],$security->getUser_pfa_id());
			}
		} else {
			$aPerfilRotina = $perfilAcessoRotinaDAO->buscaAll();
		}

		echo json_encode($aPerfilRotina);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>