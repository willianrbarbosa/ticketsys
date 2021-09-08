<?php
	session_start();
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);

	include('class/security.class.php');
	include('class/TicketDAO.class.php');
	include('class/TicketApontamentosDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/parametroDAO.class.php');
	include('session_vars.php');

	$security 				= new Security();
	$TicketDAO 				= new TicketDAO();
	$TicketApontamentosDAO 	= new TicketApontamentosDAO();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();
	$parametroDAO 			= new parametroDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket', SEC_USER_PFA_ID);

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		if (!Empty($_GET)) {

			$cWhere			= '';
			if ( (isset($_GET['cFiltro'])) ) {
				$cWhere		= $_GET['cFiltro'];
				$cWhere		= str_replace("*", "%", $cWhere);
			}
			$nUserAdminTkt	= $parametroDAO->getParametro('ADMINTKT');

			if ( isset($_GET['tktTK']) ) {
				$aTicket = $TicketDAO->buscaByID($_GET['tktTK'], $cWhere);
				$aTicket['apontamento_pendente'] = $TicketApontamentosDAO->buscaEmExecucaoTicket($_GET['tktTK']);
			} elseif ( isset($_GET['tkt_pst_id']) ) {
				$aTicket = $TicketDAO->buscaByPst_id($_GET['tkt_pst_id'], $cWhere);
			} elseif ( isset($_GET['tkt_tav_id']) ) {
				$aTicket = $TicketDAO->buscaByTav_id($_GET['tkt_tav_id'], $cWhere);
			} elseif ( isset($_GET['tkt_stt_id']) ) {
				$aTicket = $TicketDAO->buscaByStt_id($_GET['tkt_stt_id'], $cWhere);
			} elseif ( isset($_GET['tkt_abertura_data']) ) {
				$aTicket = $TicketDAO->buscaByAbertura_data($_GET['tkt_abertura_data'], $cWhere);
			} elseif ( isset($_GET['tkt_abertura_user_id']) ) {
				if ( $_GET['tkt_abertura_user_id'] == 'null' ) {
					$aTicket = $TicketDAO->buscaByAbertura_user_id(SEC_USER_ID, $cWhere);
				} else {
					$aTicket = $TicketDAO->buscaByAbertura_user_id($_GET['tkt_abertura_user_id'], $cWhere);
				}
			} elseif ( isset($_GET['tkt_aprovado']) ) {
				$aTicket = $TicketDAO->buscaByAprovado($_GET['tkt_aprovado'], $cWhere);
			} elseif ( isset($_GET['tkt_aprovado_data']) ) {
				$aTicket = $TicketDAO->buscaByAprovado_data($_GET['tkt_aprovado_data'], $cWhere);
			} elseif ( isset($_GET['tkt_aprovado_user_id']) ) {
				if ( $_GET['tkt_aprovado_user_id'] == 'null' ) {
					$aTicket = $TicketDAO->buscaByAprovado_user_id(SEC_USER_ID, $cWhere);
				} else {
					$aTicket = $TicketDAO->buscaByAprovado_user_id($_GET['tkt_aprovado_user_id'], $cWhere);
				}
			} elseif ( isset($_GET['tkt_cgt_id']) ) {
				$aTicket = $TicketDAO->buscaByCgt_id($_GET['tkt_cgt_id'], $cWhere);
			} elseif ( isset($_GET['tkt_prt_id']) ) {
				$aTicket = $TicketDAO->buscaByPrt_id($_GET['tkt_prt_id'], $cWhere);
			} elseif ( isset($_GET['tkt_ort_id']) ) {
				$aTicket = $TicketDAO->buscaByOrt_id($_GET['tkt_ort_id'], $cWhere);
			} elseif ( isset($_GET['tkt_arquivado_user_id']) ) {
				$aTicket = $TicketDAO->buscaByArquivado_user_id($_GET['tkt_arquivado_user_id'], $cWhere);
			} elseif ( isset($_GET['tkt_resp_user_id']) ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ( $_GET['tkt_resp_user_id'] == 'null' ) {
						$aTicket = $TicketDAO->buscaByPendentesResponsavel(SEC_USER_ID, $cWhere);
					} else {
						$aTicket = $TicketDAO->buscaByPendentesResponsavel($_GET['tkt_resp_user_id'], $cWhere);
					}				
				} else {
					$aTicket = $TicketDAO->buscaTodosByUsuario(SEC_USER_ID, $cWhere);
				}
			} elseif ( isset($_GET['tkt_todos_usuario_id']) ) {
				if ( $_GET['tkt_todos_usuario_id'] == 'null' ) {
					$aTicket = $TicketDAO->buscaTodosByUsuario(SEC_USER_ID, $cWhere);
				} else {
					$aTicket = $TicketDAO->buscaTodosByUsuario($_GET['tkt_todos_usuario_id'], $cWhere);
				}
			} elseif ( isset($_GET['lTriagem']) ) {				
				$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('triagemTicket', SEC_USER_PFA_ID);

				if ( $aUserRotina == false ) {
					echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
					exit;
				}
				$aTicket = $TicketDAO->buscaParaTriagem($cWhere);
			} elseif ( isset($_GET['kanban']) ) {	
				if ( $aUserRotina['pta_nivel'] < 3 ) {
					$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;
					$cWhere .= "
						(tkt_abertura_user_id = ".SEC_USER_ID."
							OR tkt_id IN (SELECT tku_tkt_id FROM ticket_usuarios WHERE tku_tipo IN ('S','O') AND tku_user_id = ".SEC_USER_ID.")
	                    )
					";
				}
				$aTicket = $TicketDAO->buscaTicketsKanban($cWhere);
				for ($i=0; $i < count($aTicket); $i++) { 
					$aTicket[$i]['apontamento_pendente'] = $TicketApontamentosDAO->buscaEmExecucaoTicket($aTicket[$i]['tkt_id']);					
				}
			} elseif ( isset($_GET['agenda']) ) {
				// if ( SEC_USER_ID == $nUserAdminTkt ) {
				if ( isset($_GET['userRespTk']) ) {
					if ( !Empty($_GET['userRespTk']) ) {
						$cWhere .= " tkt_id IN (SELECT tku_tkt_id FROM ticket_usuarios WHERE tku_tipo = 'R' AND tku_user_id = ".$_GET['userRespTk'].") ";
					} else {
						$cWhere .= " tkt_id IN (SELECT tku_tkt_id FROM ticket_usuarios WHERE tku_tipo = 'R' AND tku_user_id = ".SEC_USER_ID.") ";
					}
				} else {
					$cWhere .= " tkt_id IN (SELECT tku_tkt_id FROM ticket_usuarios WHERE tku_tipo = 'R' AND tku_user_id = ".SEC_USER_ID.") ";
				}
				// } else {
				// 	$cWhere .= " tkt_id IN (SELECT tku_tkt_id FROM ticket_usuarios WHERE tku_tipo = 'R' AND tku_user_id = ".SEC_USER_ID.") ";
				// }

				$aTicket = $TicketDAO->buscaTicketsAgenda($cWhere);
				for ($i=0; $i < count($aTicket); $i++) { 
					$aTicket[$i]['apontamento_pendente'] = $TicketApontamentosDAO->buscaEmExecucaoTicket($aTicket[$i]['tkt_id']);					
				}
			} elseif(isset($_GET['delete'])) {
				$aTicket = $TicketDAO->buscaAllDeleted($cWhere);
			} else {
				$aTicket = $TicketDAO->buscaAll($cWhere);
			}
		}

		echo json_encode($aTicket);
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>