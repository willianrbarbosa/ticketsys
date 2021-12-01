<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\PerfilAcessoDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');
	
	$PerfilAcessoDAO = new PerfilAcessoDAO();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('accessprofiles', SEC_USER_PFA_ID);
		
		if ( $aUserRotina <> false ) {
			if (!Empty($_GET)) {
				if((isset($_GET['cFiltro'])) AND (isset($_GET['deleted']))){
					$cWhere = str_replace("*", "%", $_GET['cFiltro']);
					$aPerfisAcesso = $PerfilAcessoDAO->buscaDeletedByCondicao($cWhere);
				}elseif ( isset($_GET['pfaTk']) ) {
					$aPerfisAcesso = $PerfilAcessoDAO->buscaById($_GET['pfaTk']);
				}elseif(isset($_GET['cFiltro'])){
					$cWhere = str_replace("*", "%", $_GET['cFiltro']);
					$aPerfisAcesso = $PerfilAcessoDAO->buscaByCondicao($cWhere);
				}elseif(isset($_GET['cli_id'])){
					$aPerfisAcesso = $PerfilAcessoDAO->buscaPerfilByCliPlano($_GET['cli_id']);
				}
			} else {
				$aPerfisAcesso = $PerfilAcessoDAO->buscaAll();
			}
			echo json_encode($aPerfisAcesso);
		} else {
			echo json_encode(array('error' => 'Usuario sem acesso a essa rotina.'));			
		}
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>