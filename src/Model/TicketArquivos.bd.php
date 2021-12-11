<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\EmailHelper;
	use TicketSys\Model\Classes\TicketDAO;
	use TicketSys\Model\Classes\TicketUsuariosDAO;
	use TicketSys\Model\Classes\TicketArquivosDAO;
	use TicketSys\Model\Classes\TicketArquivos;
	use TicketSys\Model\Classes\TicketHistoricoDAO;
	use TicketSys\Model\Classes\TicketHistorico;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;
	use TicketSys\Model\Classes\NotificacaoDAO;
	use TicketSys\Model\Classes\Notificacao;

	$security 				= new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();
	$notificacaoDAO 		= new NotificacaoDAO();	

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_arquivos', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData 				= json_decode($post);
			$TicketDAO 				= new TicketDAO();
			$TicketHistoricoDAO 	= new TicketHistoricoDAO();
			$TicketUsuariosDAO 		= new TicketUsuariosDAO();
			$TicketArquivosDAO 		= new TicketArquivosDAO();
			$lOk  = false;

			$aTicket = $TicketDAO->buscaByID($postData->tka_tkt_id, '');

			if ( $postData->ctrlaction == 'new' ) {
				$tka_id 		= 0;
				$tka_user_id	= $security->getUser_id();
				$tka_data_hora	= Date('Y-m-d H:m:s');
			} else {
				$tka_id 		= $postData->tka_id;
				$tka_user_id 	= $postData->tka_user_id;
				$tka_data_hora 	= (!Empty($postData->tka_data_hora) ? implode("-", array_reverse(explode("/", explode(" ", $postData->tka_data_hora)[0]))).' '.explode(" ", $postData->tka_data_hora)[1] : Date('Y-m-d H:i:s'));
			}

			$tka_tkt_id			= $postData->tka_tkt_id;

			$cPrefixArq			= $security->getUser_id().'_'.Date('Y-m-d-H-m').'_';
			$tka_arquivo_nome 	= strtolower($security->sanitizeString((!Empty($postData->tka_arquivo_nome) ? $cPrefixArq.$postData->tka_arquivo_nome : null)));
			$tka_arquivo_local 	= $security->base_patch.'/src/importacoes/tickets/';
			$tka_arquivo_tipo 	= pathinfo ( $tka_arquivo_nome, PATHINFO_EXTENSION );;
			
			$TicketArquivos 		= new TicketArquivos($tka_id,$tka_tkt_id,$tka_user_id,$tka_data_hora,$tka_arquivo_nome,$tka_arquivo_local,$tka_arquivo_tipo);

			$nRetId = $tka_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($TicketArquivosDAO->Insere($TicketArquivos)) {
				 		$lOk = true;
						$nRetId = $TicketArquivosDAO->id_inserido;
						$TicketArquivosDAO->cReturnMsg  = 'TicketArquivos inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Anexou o arquivo '.$tka_arquivo_nome.' a esse ticket.';

					// - Aqui vai enviar o e-mail ao solicitante avisando do novo arquivo anexado
					$cCorpoEmail = file_get_contents('../formato_email/ticket_arquivo.html', true);					
					$cAssunto	= 'Ticket #'.$aTicket['tkt_id'].' - Novo arquivo anexado';

					if ( !Empty($cCorpoEmail) ) {
						$cCamArquivo 	= '../importacoes/tickets/';
						$cNomeArquivo	= $tka_arquivo_nome;

						$aNotificaTkt	= array();
						$aEmailsDest	= null;
						$aNomesDest		= null;
						$aEmailsCopia	= null;
						$aNomesCopia	= null;
						// - Vai buscar os usuários observadores para enviar.
						$aTicketObserv	= $TicketUsuariosDAO->buscaByTipo($aTicket['tkt_id'], 'O');
						if ( !Empty($aTicketObserv) ) {
							for ($o=0; $o < count($aTicketObserv); $o++) { 
								if ( $aTicketObserv[$o]['tku_notif_email'] == 'S' ) {
									$aEmailsCopia	.= $aTicketObserv[$o]['user_email'].';';
									$aNomesCopia	.= $aTicketObserv[$o]['user_nome'].';';
								}
								if ( $aTicketObserv[$o]['tku_notif_sistema'] == 'S' ) {
									array_push($aNotificaTkt, $aTicketObserv[$o]['tku_user_id']);
								}
							}
						}

						if ( $aTicket['solic_tku_notif_email'] == 'S' ) {
							$aEmailsDest	.= $aTicket['solic_user_email'].';';
							$aNomesDest		.= $aTicket['solic_user_nome'].';';
						}
						if ( $aTicket['solic_tku_notif_sistema'] == 'S' ) {
							array_push($aNotificaTkt, $aTicket['solic_user_id']);
						}
						if ( $aTicket['resp_tku_notif_email'] == 'S' ) {
							$aEmailsDest	.= $aTicket['resp_user_email'].';';
							$aNomesDest		.= $aTicket['resp_user_nome'].';';
						}
						if ( $aTicket['resp_tku_notif_sistema'] == 'S' ) {
							array_push($aNotificaTkt, $aTicket['resp_user_id']);
						}

						$cCorpoEmail = str_replace('$solicitante_nome',		$aTicket['solic_user_nome'], 	$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_id',				$aTicket['tkt_id'], 			$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_titulo',			$aTicket['tkt_titulo'], 		$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_abertura_data',	Date('d/m/Y H:i', strtotime($aTicket['tkt_abertura_data'])), 	$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_situacao',			$aTicket['stt_descricao'],		$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_responsavel',		$aTicket['resp_user_nome'],		$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_descricao',		$aTicket['tkt_descricao'],		$cCorpoEmail);

						$cCorpoEmail = str_replace('$tkt_arquivo_user',		$security->getUser_nome(),		$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_arquivo_data',		Date('d/m/Y H:i'),				$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_arquivo_nome',		$cNomeArquivo,					$cCorpoEmail);

						$lAuditor	= false;
						if ( strstr($aTicket['pst_descricao'], 'Auditor') ) {
							$cCorpoEmail = str_replace('$display_ticket',	'none',			$cCorpoEmail);
							$cCorpoEmail = str_replace('$display_auditor',	'block',		$cCorpoEmail);
							$lAuditor	= true;
						} else {
							$cCorpoEmail = str_replace('$display_ticket',	'block',		$cCorpoEmail);
							$cCorpoEmail = str_replace('$display_auditor',	'none',			$cCorpoEmail);
						}

						$cCorpoEmail = str_replace('$data_envio',		Date('d/m/Y'), 							$cCorpoEmail);
						$cCorpoEmail = str_replace('$hora_envio',		Date('H:i:s'), 							$cCorpoEmail);
						
						$EmailHelper 	= new EmailHelper();

						$cMsgRetEmail	= '';

						if ( (!Empty($aEmailsDest)) ) {
							// - Envia e-mail para destinatários (solic e resp) e para os observadores
							$EmailHelper->EmailTicket($aEmailsDest,$aNomesDest,$aEmailsCopia,$aNomesCopia,$cCorpoEmail,$cAssunto,$cCamArquivo,$cNomeArquivo,$lAuditor,$cMsgRetEmail);
						} elseif ( (!Empty($aEmailsCopia)) ) {
							// - Aqui significa que nem o resp e nem o solic. preencheram para receber os e-mails mas os observadores SIM.
							// - então envia para os observadores como destinatários para evitar erro de envio.
							$EmailHelper->EmailTicket($aEmailsCopia,$aNomesCopia,null,null,$cCorpoEmail,$cAssunto,$cCamArquivo,$cNomeArquivo,$lAuditor,$cMsgRetEmail);
						}
						$TicketArquivosDAO->cReturnMsg  .= '<br/>'.$cMsgRetEmail;

						// - Aqui vai gerar as notificações do sistema
						if ( (!Empty($aNotificaTkt)) ) {
							for ($u=0; $u < count($aNotificaTkt); $u++) {

								$ntf_id 			= 0;
								$ntf_dest_user_id	= $aNotificaTkt[$u];
								$ntf_data_hora		= Date('Y-m-d H:i:s');
								$ntf_tipo_alerta	= 'primary';
								$ntf_notificacao	= $cAssunto;
								$ntf_url			= 'detalheticket/'.$aTicket['tkt_id'];
								$ntf_lida			= 'N';

								$Notificacao 		= new Notificacao($ntf_id,$ntf_dest_user_id,$ntf_data_hora,$ntf_tipo_alerta,$ntf_notificacao,$ntf_url,$ntf_lida);

								$notificacaoDAO->Insere($Notificacao);
							}
						}
					}
				} else {
			 		$lOk = false;
					$TicketArquivosDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketArquivosDAO->Deleta($TicketArquivos)) {
				 		$lOk = true;
						$TicketArquivosDAO->cReturnMsg  = 'TicketArquivos excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Excluiu o arquivo '.$tka_arquivo_nome.' desse ticket.';
				} else {
			 		$lOk = false;
					$TicketArquivosDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketArquivosDAO->Restaura($TicketArquivos)) {
				 		$lOk = true;
						$TicketArquivosDAO->cReturnMsg  = 'TicketArquivos restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Restaurou o arquivo '.$tka_arquivo_nome.' desse ticket.';
				} else {
			 		$lOk = false;
					$TicketArquivosDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			if ( $lOk ) {
				$logEdicaoDAO 	= new LogEdicaoDAO();
				$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'ticket_arquivos', $nRetId, $postData->ctrlaction, 'ticket_arquivos', Date('Y-m-d H:i:s'));
				$logEdicaoDAO->Insere($logEdicao);


				$TicketHistorico = new TicketHistorico(0,$tka_tkt_id,$security->getUser_id(),Date('Y-m-d H:i:s'),$tkh_descricao);
				$TicketHistoricoDAO->Insere($TicketHistorico);
			}

			echo json_encode(array("return"=>$lOk, "msg"=>$TicketArquivosDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
