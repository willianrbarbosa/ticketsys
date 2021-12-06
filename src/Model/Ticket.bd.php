<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\EmailHelper;
	use TicketSys\Model\Classes\TicketDAO;
	use TicketSys\Model\Classes\Ticket;
	use TicketSys\Model\Classes\TicketApontamentosDAO;
	use TicketSys\Model\Classes\TicketArquivosDAO;
	use TicketSys\Model\Classes\TicketArquivos;
	use TicketSys\Model\Classes\TicketUsuariosDAO;
	use TicketSys\Model\Classes\TicketUsuarios;
	use TicketSys\Model\Classes\TicketHistoricoDAO;
	use TicketSys\Model\Classes\TicketHistorico;
	use TicketSys\Model\Classes\PastaTrabalhoDAO;
	use TicketSys\Model\Classes\TipoAtividadeDAO;
	use TicketSys\Model\Classes\SituacaoTicketDAO;
	use TicketSys\Model\Classes\CategoriaTicketDAO;
	use TicketSys\Model\Classes\PrioridadeTicketDAO;
	use TicketSys\Model\Classes\UsuarioDAO;
	use TicketSys\Model\Classes\NotificacaoDAO;
	use TicketSys\Model\Classes\Notificacao;
	use TicketSys\Model\Classes\ParametroDAO;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;

	$security				= new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();
	$notificacaoDAO 		= new notificacaoDAO();	

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData 				= json_decode($post);

			$TicketDAO 				= new TicketDAO();
			$TicketApontamentosDAO 	= new TicketApontamentosDAO();
			$TicketUsuariosDAO 		= new TicketUsuariosDAO();
			$TicketArquivosDAO 		= new TicketArquivosDAO();
			$TicketHistoricoDAO 	= new TicketHistoricoDAO();
			$PastaTrabalhoDAO 		= new PastaTrabalhoDAO();
			$TipoAtividadeDAO 		= new TipoAtividadeDAO();
			$SituacaoTicketDAO 		= new SituacaoTicketDAO();
			$CategoriaTicketDAO 	= new CategoriaTicketDAO();
			$PrioridadeTicketDAO 	= new PrioridadeTicketDAO();
			$usuarioDAO 			= new UsuarioDAO();
			$parametroDAO 			= new ParametroDAO();

			$lOk  = false;
			$aTicketAnt				= array();
			$tkh_descricao			= '';
			$nUserAdminTkt			= $parametroDAO->getParametro('ADMINTKT');
			
			if ( $postData->ctrlaction == 'alt_data' ) {
				$tkt_id	= $postData->tktId;		
				if ($TicketDAO->AlteraDataEstimada($postData->tktId, $postData->tktDays, $postData->tktTime, $postData->tktAltIni)) {
					$lOk = true;
					$TicketDAO->cReturnMsg = 'Data de estimativa do Ticket alterada com sucesso.';
				} else {
					$lOk = false;
				}
			} elseif ( $postData->ctrlaction == 'new_user_ticket' ) {
				$aUser = $usuarioDAO->buscaByToken($security->getUser_token());

				$tkt_id 				= 0;
				$tkt_pst_id 			= (!Empty($aUser['user_pst_id']) ? $aUser['user_pst_id'] : 1);
				$tkt_titulo 			= $postData->tkt_titulo;
				$tkt_tav_id 			= null;
				$tkt_descricao 			= $postData->tkt_descricao;
				$tkt_abertura_data 		= Date('Y-m-d H:i:s');
				$tkt_abertura_user_id 	= $security->getUser_id();
				$tkt_stt_id 			= 1;
				$tkt_cgt_id 			= null;
				$tkt_prt_id 			= null;
				$tkt_ort_id 			= 3;

				$tkt_data_ini_estim 	= null;
				$tkt_hora_ini_estim 	= null;
				$tkt_data_ini_real 		= null;
				$tkt_hora_ini_real 		= null;
				$tkt_data_fim_estim 	= null;
				$tkt_hora_fim_estim 	= null;
				$tkt_data_fim_real 		= null;
				$tkt_hora_fim_real 		= null;
				$tkt_total_hora_estim 	= 0;
				$tkt_total_hora_real 	= 0;

				$tkt_per_concluido 		= 0;

				$tkt_aprovado 			= 'N';
				$tkt_aprovado_data 		= null;
				$tkt_aprovado_user_id 	= null;

				$tkt_encerrado 			= 'N';
				$tkt_encerrado_data 	= null;
				$tkt_encerrado_user_id 	= null;

				$tkt_ticket_pai 		= null;

				$tkt_arquivado 			= 'N';
				$tkt_arquivado_data 	= null;
				$tkt_arquivado_user_id 	= null;

			} elseif ( $postData->ctrlaction == 'kanban_situacao' ) { 
				$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticketkanban', $security->getUser_pfa_id());

				if ( $aUserRotina == false ) {
					echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
					exit;
				}

				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					$tkt_id 			= $postData->tkt_id;
					$tkt_stt_id 		= $postData->nova_tkt_stt_id;

					$aTicketAnt 		= $TicketDAO->BuscaById($tkt_id);
					$aNovaSitTicket 	= $SituacaoTicketDAO->buscaByID($tkt_stt_id);

					$lAprovou			= false;
					$lEncerrou			= false;
					$lReabriu			= false;

					if ( ($aTicketAnt['stt_encerra_ticket'] == 'N') AND ($aNovaSitTicket['stt_encerra_ticket'] == 'S') ) {
						//Aqui saindo de uma situação em aberto para encerramento
						$lEncerrou		= true;
						
						//Aqui verificar se o Ticket foi aprovado antes de ser encerrado
						if ( $aTicketAnt['tkt_aprovado'] == 'S' ) {
							//Aqui vai validar se não está tentando encerrar o ticket com apontamento em aberto
							$aTicketApontamPend 		= $TicketApontamentosDAO->buscaEmExecucaoTicket($tkt_id);
							if ( Empty($aTicketApontamPend) ) {
								//Aqui valida se o usuário que está tentando mudar para aprovado é o solicitante, o responsável ou o usuário que abriu o ticket
								if ( ($security->getUser_id() == $aTicketAnt['solic_user_id']) OR ($security->getUser_id() == $aTicketAnt['resp_user_id']) OR ($security->getUser_id() == $nUserAdminTkt) ) {
									$tkt_encerrado			= 'S';
									$tkt_encerrado_data		= Date('Y-m-d H:i:s');
									$tkt_encerrado_user_id	= $security->getUser_id();
									
									if ( $TicketDAO->AtualizaSituacaoEncerrado($tkt_id, $tkt_stt_id, $tkt_encerrado, $tkt_encerrado_data, $tkt_encerrado_user_id) ) {
										$lOk = true;
										$TicketDAO->cReturnMsg  = 'Situação do Ticket alterada com sucesso.';
									} else {
										$lOk = false;	
									}
									
									$tkh_descricao			= $security->getUser_nome().' Encerrou esse ticket em '.Date('d/m/Y H:i:s').'.';
								} else {
							 		$lOk = false;
									$TicketDAO->cReturnMsg  = 'Apenas o solicitante ou o responsável podem encerrar um Ticket!!!';
								}
							} else {
						 		$lOk = false;
								$TicketDAO->cReturnMsg  = 'Existe usuários trabalhando nesse Ticket, ele não pode ser encerrado. Verifique!!!';
							}
						} else {
					 		$lOk = false;
							$TicketDAO->cReturnMsg  = 'Antes de encerrar o Ticket deve ser aprovado pelo requerente ou pelo solicitante!!!';
						}
					} elseif ( ($aTicketAnt['stt_encerra_ticket'] == 'S') AND ($aTicketAnt['tkt_encerrado'] == 'S') AND ($aNovaSitTicket['stt_aprova_ticket'] == 'S') ) { 
						//Aqui saindo de uma situação encerrada e tentando mudar para uma aprovada
						$lOk = false;	
						$TicketDAO->cReturnMsg  = 'O Ticket não pode sair de uma situação encerrada para uma aprovada. Verifique!';

					} elseif ( ($aTicketAnt['stt_encerra_ticket'] == 'S') AND ($aTicketAnt['tkt_encerrado'] == 'S') AND ($aNovaSitTicket['stt_encerra_ticket'] == 'N') ) { 
						//Aqui saindo de uma situação encerrada para uma em aberto
						$lReabriu		= true;
						if ( $TicketDAO->reabreTicket($tkt_id, $tkt_stt_id) ) {
							$lOk = true;
							$TicketDAO->cReturnMsg  = 'Situação do Ticket alterada com sucesso.';
							// $TicketDAO->AtualizaDataFimReal($tkt_id, null, null);
						} else {
							$lOk = false;	
						}
							
						$tkh_descricao			= $security->getUser_nome().' Reabriu esse Ticket que foi encerrado pelo '.$aTicketAnt['enc_user_nome'].' em '.Date('d/m/Y H:i:s', strtotime($aTicketAnt['tkt_encerrado_data'])).'.';
					} elseif ( ($aTicketAnt['stt_aprova_ticket'] == 'N') AND ($aNovaSitTicket['stt_aprova_ticket'] == 'S') ) {
						//Aqui saindo de uma situação sem aprovação para aprovação
						$lAprovou		= true;

						//Aqui valida se o usuário que está tentando mudar para aprovado é o solicitante ou o usuário que abriu o ticket
						if ( ($security->getUser_id() == $aTicketAnt['solic_user_id']) OR ($security->getUser_id() == $nUserAdminTkt) ) {
							//Aqui vai validar se não está tentando aprovar o ticket com apontamento em aberto
							$aTicketApontamPend 		= $TicketApontamentosDAO->buscaEmExecucaoTicket($tkt_id);
							if ( Empty($aTicketApontamPend) ) {
								$tkt_data_fim_real		= Date('Y-m-d');
								$tkt_hora_fim_real		= Date('H:i:s');
								$tkt_aprovado			= 'S';
								$tkt_aprovado_data		= Date('Y-m-d H:i:s');
								$tkt_aprovado_user_id	= $security->getUser_id();
								if ( $TicketDAO->AtualizaSituacaoAprovado($tkt_id, $tkt_stt_id, $tkt_data_fim_real, $tkt_hora_fim_real, $tkt_aprovado, $tkt_aprovado_data, $tkt_aprovado_user_id) ) {
									$lOk = true;
									$TicketDAO->cReturnMsg  = 'Situação do Ticket alterada com sucesso.';
								} else {
									$lOk = false;	
								}
								
								$tkh_descricao			= $security->getUser_nome().' Aprovavou a solução desse ticket em '.Date('d/m/Y H:i:s').'.';
							} else {
						 		$lOk = false;
								$TicketDAO->cReturnMsg  = 'Existe usuários trabalhando nesse Ticket, ele não pode ser aprovado. Verifique!!!';
							}
						} else {
					 		$lOk = false;
							$TicketDAO->cReturnMsg  = 'Apenas o solicitante pode aprovar um Ticket!!!';
						}

					} elseif ( ($aTicketAnt['stt_aprova_ticket'] == 'S') AND ($aTicketAnt['tkt_aprovado'] == 'S') AND ($aNovaSitTicket['stt_aprova_ticket'] == 'N') ) { 
						//Aqui saindo de uma situação aprovada para uma sem aprovação
						if ( $TicketDAO->AtualizaSituacaoAprovado($tkt_id, $tkt_stt_id, null, null, null, null, null) ) {
							$lOk = true;
							$TicketDAO->cReturnMsg  = 'Situação do Ticket alterada com sucesso.';
						} else {
							$lOk = false;	
						}
							
						$tkh_descricao			= $security->getUser_nome().' Removeu a aprovação desse ticket feita pelo '.$aTicketAnt['aprov_user_nome'].' em '.Date('d/m/Y H:i:s', strtotime($aTicketAnt['tkt_aprovado_data'])).'.';

					} else {
						if ( $TicketDAO->AtualizaSituacaoPadrao($tkt_id, $tkt_stt_id) ) {
							$lOk = true;
							$TicketDAO->cReturnMsg  = 'Situação do Ticket alterada com sucesso.';
						} else {
							$lOk = false;	
						}
						$tkh_descricao			= $security->getUser_nome().' Mudou esse ticket de '.$aTicketAnt['stt_descricao'].' para '.$aNovaSitTicket['stt_descricao'].'.';
					}

					if ( $lOk ) {
						// - Aqui vai enviar o e-mail ao solicitante avisando da nova situação
						$cCorpoEmail = file_get_contents('../formato_email/ticket_status.html', true);					
						if ( $lAprovou ) {
							$cAssunto	= 'Ticket #'.$aTicketAnt['tkt_id'].' - Solucao Aprovada';
							$cAlert		= 'success';
						} elseif ( $lEncerrou ) {
							$cAssunto	= 'Ticket #'.$aTicketAnt['tkt_id'].' - Encerrado';
							$cAlert		= 'success';
						} elseif ( $lReabriu ) {
							$cAssunto	= 'Ticket #'.$aTicketAnt['tkt_id'].' - Reaberto';
							$cAlert		= 'danger';
						} else {
							$cAssunto	= 'Ticket #'.$aTicketAnt['tkt_id'].' - Nova Situacao';
							$cAlert		= 'warning';
						}

						if ( !Empty($cCorpoEmail) ) {
							$aNotificaTkt	= array();
							$aEmailsDest	= null;
							$aNomesDest		= null;
							$aEmailsCopia	= null;
							$aNomesCopia	= null;
							// - Vai buscar os usuários observadores para enviar.
							$aTicketObserv	= $TicketUsuariosDAO->buscaByTipo($aTicketAnt['tkt_id'], 'O');
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

							if ( $aTicketAnt['solic_tku_notif_email'] == 'S' ) {
								$aEmailsDest	.= $aTicketAnt['solic_user_email'].';';
								$aNomesDest		.= $aTicketAnt['solic_user_nome'].';';
							}
							if ( $aTicketAnt['solic_tku_notif_sistema'] == 'S' ) {
								array_push($aNotificaTkt, $aTicketAnt['solic_user_id']);
							}
							if ( $aTicketAnt['resp_tku_notif_email'] == 'S' ) {
								$aEmailsDest	.= $aTicketAnt['resp_user_email'].';';
								$aNomesDest		.= $aTicketAnt['resp_user_nome'].';';
							}
							if ( $aTicketAnt['resp_tku_notif_sistema'] == 'S' ) {
								array_push($aNotificaTkt, $aTicketAnt['resp_user_id']);
							}

							$cCorpoEmail = str_replace('$solicitante_nome',		$aTicketAnt['solic_user_nome'], 	$cCorpoEmail);
							$cCorpoEmail = str_replace('$tkt_id',				$aTicketAnt['tkt_id'], 				$cCorpoEmail);
							$cCorpoEmail = str_replace('$tkt_titulo',			$aTicketAnt['tkt_titulo'], 			$cCorpoEmail);
							$cCorpoEmail = str_replace('$tkt_abertura_data',	Date('d/m/Y H:i', strtotime($aTicketAnt['tkt_abertura_data'])), 	$cCorpoEmail);
							$cCorpoEmail = str_replace('$tkt_situacao',			$aTicketAnt['stt_descricao'],		$cCorpoEmail);
							$cCorpoEmail = str_replace('$situacao_nova',		$aNovaSitTicket['stt_descricao'],	$cCorpoEmail);
							$cCorpoEmail = str_replace('$tkt_responsavel',		$aTicketAnt['resp_user_nome'],		$cCorpoEmail);
							$cCorpoEmail = str_replace('$tkt_descricao',		$aTicketAnt['tkt_descricao'],		$cCorpoEmail);

							$lAuditor	= false;
							if ( strstr($aTicketAnt['pst_descricao'], 'Auditor') ) {
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
								$EmailHelper->EmailTicket($aEmailsDest,$aNomesDest,$aEmailsCopia,$aNomesCopia,$cCorpoEmail,$cAssunto,null,null,$lAuditor,$cMsgRetEmail);
							} elseif ( (!Empty($aEmailsCopia)) ) {
								// - Aqui significa que nem o resp e nem o solic. preencheram para receber os e-mails mas os observadores SIM.
								// - então envia para os observadores como destinatários para evitar erro de envio.
								$EmailHelper->EmailTicket($aEmailsCopia,$aNomesCopia,null,null,$cCorpoEmail,$cAssunto,null,null,$lAuditor,$cMsgRetEmail);
							}
							$TicketDAO->cReturnMsg  .= '<br/>'.$cMsgRetEmail;

							// - Aqui vai gerar as notificações do sistema
							if ( (!Empty($aNotificaTkt)) ) {
								for ($u=0; $u < count($aNotificaTkt); $u++) {
	
									$ntf_id 			= 0;
									$ntf_dest_user_id	= $aNotificaTkt[$u];
									$ntf_data_hora		= Date('Y-m-d H:i:s');
									$ntf_tipo_alerta	= $cAlert;
									$ntf_notificacao	= $cAssunto;
									$ntf_url			= 'detalheticket/'.$aTicketAnt['tkt_id'];
									$ntf_lida			= 'N';
	
									$Notificacao 		= new Notificacao($ntf_id,$ntf_dest_user_id,$ntf_data_hora,$ntf_tipo_alerta,$ntf_notificacao,$ntf_url,$ntf_lida);
	
									$notificacaoDAO->Insere($Notificacao);
								}
							}
						}
					}
				} else {
			 		$lOk = false;
					$TicketDAO->cReturnMsg  = 'Usuário sem acesso para alterar situação dos Tickets no Kanban.';
				}
			} else {
				$tkt_aprovado 	= (!Empty($postData->tkt_aprovado) ? $postData->tkt_aprovado : 'N');
				$tkt_encerrado 	= (!Empty($postData->tkt_encerrado) ? $postData->tkt_encerrado : 'N');
				$tkt_arquivado 	= (!Empty($postData->tkt_arquivado) ? $postData->tkt_arquivado : 'N');

				if ( $postData->ctrlaction == 'new' ) {
					$tkt_id 				= 0;
					if ( $tkt_aprovado == 'S' ) {
						$tkt_aprovado_data 		= Date('Y-m-d H:i:s');
						$tkt_aprovado_user_id 	= $security->getUser_id();
					} else {
						$tkt_aprovado_data 		= null;
						$tkt_aprovado_user_id 	= null;
					}
					if ( $tkt_encerrado == 'S' ) {
						$tkt_encerrado_data 		= Date('Y-m-d H:i:s');
						$tkt_encerrado_user_id 	= $security->getUser_id();
					} else {
						$tkt_encerrado_data 		= null;
						$tkt_encerrado_user_id 	= null;
					}
					if ( $tkt_arquivado == 'S' ) {
						$tkt_arquivado_data 		= Date('Y-m-d H:i:s');
						$tkt_arquivado_user_id 	= $security->getUser_id();
					} else {
						$tkt_arquivado_data 		= null;
						$tkt_arquivado_user_id 	= null;
					}
					$tkt_abertura_user_id 	= $security->getUser_id();
				} else {
					$aTicketAnt 			= $TicketDAO->BuscaById($postData->tkt_id);
					$tkt_id 				= $postData->tkt_id;
					if ( $tkt_aprovado == 'S' ) {
						$tkt_aprovado_data 		= (!Empty($postData->tkt_aprovado_data) ? implode("-", array_reverse(explode("/", $postData->tkt_aprovado_data))) : null);
						$tkt_aprovado_user_id 	= (!Empty($postData->tkt_aprovado_user_id) ? $postData->tkt_aprovado_user_id : null);
					} else {
						$tkt_aprovado_data 		= null;
						$tkt_aprovado_user_id 	= null;
					}
					if ( $tkt_encerrado == 'S' ) {
						$tkt_encerrado_data 		= (!Empty($postData->tkt_encerrado_data) ? implode("-", array_reverse(explode("/", $postData->tkt_encerrado_data))) : null);
						$tkt_encerrado_user_id 	= (!Empty($postData->tkt_encerrado_user_id) ? $postData->tkt_encerrado_user_id : null);
					} else {
						$tkt_encerrado_data 		= null;
						$tkt_encerrado_user_id 	= null;
					}
					if ( $tkt_arquivado == 'S' ) {
						$tkt_arquivado_data 		= (!Empty($postData->tkt_arquivado_data) ? implode("-", array_reverse(explode("/", $postData->tkt_arquivado_data))) : null);
						$tkt_arquivado_user_id 	= (!Empty($postData->tkt_arquivado_user_id) ? $postData->tkt_arquivado_user_id : null);
					} else {
						$tkt_arquivado_data 		= null;
						$tkt_arquivado_user_id 	= null;
					}
					$tkt_abertura_user_id 	= $postData->tkt_abertura_user_id;
				}
				$tkt_pst_id 			= $postData->tkt_pst_id;
				$tkt_titulo 			= $postData->tkt_titulo;
				$tkt_tav_id 			= $postData->tkt_tav_id;
				$tkt_descricao 			= (!Empty($postData->tkt_descricao) ? $postData->tkt_descricao : null);
				$aDataView				= explode(" ", $postData->tkt_abertura_data);				
				$tkt_abertura_data 		= (!Empty($postData->tkt_abertura_data) ? implode("-", array_reverse(explode("/", $aDataView[0]))).' '.(!Empty($aDataView[1]) ? $aDataView[1] : Date('H:i:s')) : Date('Y-m-d H:i:s'));
				$tkt_stt_id 			= (!Empty($postData->tkt_stt_id) ? $postData->tkt_stt_id : 2);
				$tkt_cgt_id 			= $postData->tkt_cgt_id;
				$tkt_prt_id 			= $postData->tkt_prt_id;
				$tkt_ort_id 			= $postData->tkt_ort_id;

				$tkt_data_ini_estim 	= (!Empty($postData->tkt_data_ini_estim) ? implode("-", array_reverse(explode("/", $postData->tkt_data_ini_estim))) : null);
				$tkt_hora_ini_estim 	= (!Empty($postData->tkt_hora_ini_estim) ? implode("-", array_reverse(explode("/", $postData->tkt_hora_ini_estim))) : null);
				$tkt_data_ini_real 		= (!Empty($postData->tkt_data_ini_real) ? implode("-", array_reverse(explode("/", $postData->tkt_data_ini_real))) : null);
				$tkt_hora_ini_real 		= (!Empty($postData->tkt_hora_ini_real) ? implode("-", array_reverse(explode("/", $postData->tkt_hora_ini_real))) : null);
				$tkt_data_fim_estim 	= (!Empty($postData->tkt_data_fim_estim) ? implode("-", array_reverse(explode("/", $postData->tkt_data_fim_estim))) : null);
				$tkt_hora_fim_estim 	= (!Empty($postData->tkt_hora_fim_estim) ? implode("-", array_reverse(explode("/", $postData->tkt_hora_fim_estim))) : null);
				$tkt_data_fim_real 		= (!Empty($postData->tkt_data_fim_real) ? implode("-", array_reverse(explode("/", $postData->tkt_data_fim_real))) : null);
				$tkt_hora_fim_real 		= (!Empty($postData->tkt_hora_fim_real) ? implode("-", array_reverse(explode("/", $postData->tkt_hora_fim_real))) : null);
				$tkt_total_hora_estim 	= (!Empty($postData->tkt_total_hora_estim) ? (strpos( $postData->tkt_total_hora_estim, "," ) ? str_replace(",", ".", str_replace(".", "", $postData->tkt_total_hora_estim)) : $postData->tkt_total_hora_estim) : null);
				$tkt_total_hora_real 	= (!Empty($postData->tkt_total_hora_real) ? (strpos( $postData->tkt_total_hora_real, "," ) ? str_replace(",", ".", str_replace(".", "", $postData->tkt_total_hora_real)) : $postData->tkt_total_hora_real) : null);

				if ( $postData->ctrlaction == 'edit' ) {
					$nEsforcoEstimado	= (float)$tkt_total_hora_estim;
					$nEsforcoReal		= (float)$tkt_total_hora_real;
					if ( $nEsforcoEstimado > 0 ) {
						$tkt_per_concluido = (($nEsforcoReal) * 100) / $nEsforcoEstimado;
					} else {
						$tkt_per_concluido = 100;
					}
				} else {
					$tkt_per_concluido 	= (!Empty($postData->tkt_per_concluido) ? (strpos( $postData->tkt_per_concluido, "," ) ? str_replace(",", ".", str_replace(".", "", $postData->tkt_per_concluido)) : $postData->tkt_per_concluido) : null);
				}
				$tkt_per_concluido		= ($tkt_per_concluido > 100 ? 100 : $tkt_per_concluido);
				
				$tkt_ticket_pai 		= (!Empty($postData->tkt_ticket_pai) ? $postData->tkt_ticket_pai : null);

			}

			if ( ($postData->ctrlaction <> 'kanban_situacao') AND ($postData->ctrlaction <> 'alt_data') ) { 
				$Ticket = new Ticket($tkt_id,$tkt_pst_id,$tkt_titulo,$tkt_tav_id,$tkt_descricao,$tkt_abertura_data,$tkt_abertura_user_id,$tkt_stt_id,$tkt_cgt_id,$tkt_prt_id,
									$tkt_ort_id,$tkt_data_ini_estim,$tkt_hora_ini_estim,$tkt_data_ini_real,$tkt_hora_ini_real,$tkt_data_fim_estim,$tkt_hora_fim_estim,$tkt_data_fim_real,$tkt_hora_fim_real,$tkt_total_hora_estim,
									$tkt_total_hora_real,$tkt_per_concluido,
									$tkt_aprovado,$tkt_aprovado_data,$tkt_aprovado_user_id,
									$tkt_encerrado,$tkt_encerrado_data,$tkt_encerrado_user_id,
									$tkt_arquivado,$tkt_arquivado_data,$tkt_arquivado_user_id,
									$tkt_ticket_pai);

			}
			$nRetId = $tkt_id;

			if ( ($postData->ctrlaction == 'new') OR ($postData->ctrlaction == 'new_user_ticket') ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($TicketDAO->Insere($Ticket)) {
				 		$lOk = true;
						$nRetId = $TicketDAO->id_inserido;
						$TicketDAO->cReturnMsg  = 'Ticket inserido com sucesso.';

						//Vai inserir o registro do arquivo caso tenha anexado
						if ( isset($postData->ticket_file) ) {
							$cPrefixArq			= $security->getUser_id().'_'.Date('Y-m-d-H-m').'_';
							$tka_arquivo_nome 	= strtolower($security->sanitizeString((!Empty($postData->ticket_file) ? $cPrefixArq.$postData->ticket_file : null)));
							$tka_arquivo_local 	= $security->base_patch.'/assets/importacoes/tickets/';
							$tka_arquivo_tipo 	= pathinfo ( $tka_arquivo_nome, PATHINFO_EXTENSION );;

							
							$TicketArquivos 		= new TicketArquivos(0,$nRetId,$tkt_abertura_user_id,Date('Y-m-d H:i:s'),$tka_arquivo_nome,$tka_arquivo_local,$tka_arquivo_tipo);
							$TicketArquivosDAO->Insere($TicketArquivos);
						}

						
						if ( $postData->ctrlaction == 'new' ) {
							//Aqui vai inserir o solicitante
							if ( isset($postData->aSolicitante) ) {
								$tku_user_id 			= $postData->aSolicitante->tku_user_id;
								$tku_notif_email		= (isset($postData->aSolicitante->tku_notif_email) ? ($postData->aSolicitante->tku_notif_email ? 'S' : 'N') : 'N');
								$tku_notif_sistema		= (isset($postData->aSolicitante->tku_notif_sistema) ? ($postData->aSolicitante->tku_notif_sistema ? 'S' : 'N') : 'N');

								$TicketUsuarios 		= new TicketUsuarios(0,$nRetId,$tku_user_id,'S',$tku_notif_email,$tku_notif_sistema);
								$TicketUsuariosDAO->Insere($TicketUsuarios);
							}

							//Aqui vai inserir os Observadores
							if ( isset($postData->aObservadores) ) {							
								for ($i=0; $i < count($postData->aObservadores); $i++) { 
									$tku_user_id 			= $postData->aObservadores[$i]->tku_user_id;
									$tku_notif_email		= (isset($postData->aObservadores[$i]->tku_notif_email) ? ($postData->aObservadores[$i]->tku_notif_email ? 'S' : 'N') : 'N');
									$tku_notif_sistema		= (isset($postData->aObservadores[$i]->tku_notif_sistema) ? ($postData->aObservadores[$i]->tku_notif_sistema ? 'S' : 'N') : 'N');

									$TicketUsuarios 		= new TicketUsuarios(0,$nRetId,$tku_user_id,'O',$tku_notif_email,$tku_notif_sistema);
									$TicketUsuariosDAO->Insere($TicketUsuarios);
								}
							}

							//Aqui vai inserir o Responsável
							if ( isset($postData->aResponsavel) ) {
								$tku_user_id 			= $postData->aResponsavel->tku_user_id;
								$tku_notif_email		= (isset($postData->aResponsavel->tku_notif_email) ? ($postData->aResponsavel->tku_notif_email ? 'S' : 'N') : 'N');
								$tku_notif_sistema		= (isset($postData->aResponsavel->tku_notif_sistema) ? ($postData->aResponsavel->tku_notif_sistema ? 'S' : 'N') : 'N');

								$TicketUsuarios 		= new TicketUsuarios(0,$nRetId,$tku_user_id,'R',$tku_notif_email,$tku_notif_sistema);
								$TicketUsuariosDAO->Insere($TicketUsuarios);
							}
						} elseif ( $postData->ctrlaction == 'new_user_ticket' ) {							
							$tku_user_id 			= $tkt_abertura_user_id;
							$tku_notif_email		= 'N';
							$tku_notif_sistema		= 'S';

							$TicketUsuarios 		= new TicketUsuarios(0,$nRetId,$tku_user_id,'S',$tku_notif_email,$tku_notif_sistema);
							$TicketUsuariosDAO->Insere($TicketUsuarios);
						}

						if ( $tkt_stt_id == 1 ) {
							$tkh_descricao			= $security->getUser_nome().' Adicionou esse ticket para TRIAGEM.';
						} else {
							$tkh_descricao			= $security->getUser_nome().' Adicionou esse ticket.';
						}
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($TicketDAO->Altera($Ticket)) {
				 		$lOk = true;
						$TicketDAO->cReturnMsg  = 'Ticket alterado com sucesso.';

						//Aqui vai inserir o solicitante
						if ( isset($postData->aSolicitante) ) {
							$TicketUsuariosDAO->ApagaPorTicketTipo($tkt_id, 'S');

							$tku_user_id 			= $postData->aSolicitante->tku_user_id;
							$tku_tkt_id 			= $tkt_id;
							$tku_notif_email		= (isset($postData->aSolicitante->tku_notif_email) ? ($postData->aSolicitante->tku_notif_email ? 'S' : 'N') : 'N');
							$tku_notif_sistema		= (isset($postData->aSolicitante->tku_notif_sistema) ? ($postData->aSolicitante->tku_notif_sistema ? 'S' : 'N') : 'N');

							$TicketUsuarios 		= new TicketUsuarios(0,$tku_tkt_id,$tku_user_id,'S',$tku_notif_email,$tku_notif_sistema);
							$TicketUsuariosDAO->Insere($TicketUsuarios);
						}

						//Aqui vai inserir os Observadores
						if ( isset($postData->aObservadores) ) {
							$TicketUsuariosDAO->ApagaPorTicketTipo($tkt_id, 'O');

							for ($i=0; $i < count($postData->aObservadores); $i++) { 
								$tku_user_id 			= $postData->aObservadores[$i]->tku_user_id;
								$tku_tkt_id 			= $tkt_id;
								$tku_notif_email		= (isset($postData->aObservadores[$i]->tku_notif_email) ? ($postData->aObservadores[$i]->tku_notif_email ? 'S' : 'N') : 'N');
								$tku_notif_sistema		= (isset($postData->aObservadores[$i]->tku_notif_sistema) ? ($postData->aObservadores[$i]->tku_notif_sistema ? 'S' : 'N') : 'N');
								
								$TicketUsuarios 		= new TicketUsuarios(0,$tku_tkt_id,$tku_user_id,'O',$tku_notif_email,$tku_notif_sistema);
								$TicketUsuariosDAO->Insere($TicketUsuarios);
							}
						}

						//Aqui vai inserir o Responsável
						if ( isset($postData->aResponsavel) ) {
							$TicketUsuariosDAO->ApagaPorTicketTipo($tkt_id, 'R');

							$tku_user_id 			= $postData->aResponsavel->tku_user_id;
							$tku_tkt_id 			= $tkt_id;
							$tku_notif_email		= (isset($postData->aResponsavel->tku_notif_email) ? ($postData->aResponsavel->tku_notif_email ? 'S' : 'N') : 'N');
							$tku_notif_sistema		= (isset($postData->aResponsavel->tku_notif_sistema) ? ($postData->aResponsavel->tku_notif_sistema ? 'S' : 'N') : 'N');

							$TicketUsuarios 		= new TicketUsuarios(0,$tku_tkt_id,$tku_user_id,'R',$tku_notif_email,$tku_notif_sistema);
							$TicketUsuariosDAO->Insere($TicketUsuarios);
						}

						if ( $aTicketAnt['tkt_pst_id'] <> $tkt_pst_id ) {
							$aPastaTrabalho 		= $PastaTrabalhoDAO->buscaByID($tkt_pst_id);
							if ( !Empty($aPastaTrabalho) ) {
								$tkh_descricao			.= ' | Pasta de Trabalho: '.$aTicketAnt['pst_descricao'].' >> '.$aPastaTrabalho['pst_descricao'];
							}
						}
						if ( $aTicketAnt['tkt_tav_id'] <> $tkt_tav_id ) {
							$aTipoAtividade 		= $TipoAtividadeDAO->buscaByID($tkt_tav_id);
							if ( !Empty($aTipoAtividade) ) {
								$tkh_descricao			.= ' | Tipo de Atividade: '.$aTicketAnt['tav_descricao'].' >> '.$aTipoAtividade['tav_descricao'];
							}
						}
						if ( $aTicketAnt['tkt_stt_id'] <> $tkt_stt_id ) {
							$aSituacaoTicket 		= $SituacaoTicketDAO->buscaByID($tkt_stt_id);
							if ( !Empty($aSituacaoTicket) ) {
								$tkh_descricao			.= ' | Situação: '.$aTicketAnt['stt_descricao'].' >> '.$aSituacaoTicket['stt_descricao'];
							}
						}
						if ( $aTicketAnt['tkt_cgt_id'] <> $tkt_cgt_id ) {
							$aCategoriaTicket 		= $CategoriaTicketDAO->buscaByID($tkt_cgt_id);
							if ( !Empty($aCategoriaTicket) ) {
								$tkh_descricao			.= ' | Categoria: '.$aTicketAnt['cgt_descricao'].' >> '.$aCategoriaTicket['cgt_descricao'];
							}
						}
						if ( $aTicketAnt['tkt_prt_id'] <> $tkt_prt_id ) {
							$aPrioridadeTicket 		= $PrioridadeTicketDAO->buscaByID($tkt_prt_id);
							if ( !Empty($aPrioridadeTicket) ) {
								$tkh_descricao			.= ' | Prioridade: '.$aTicketAnt['prt_descricao'].' >> '.$aPrioridadeTicket['prt_descricao'];
							}
						}

						if ( isset($postData->aSolicitante) ) {
							if ( $aTicketAnt['solic_user_id'] <> $postData->aSolicitante->tku_user_id ) {
								$aUserAlter			= $usuarioDAO->buscaById($postData->aSolicitante->tku_user_id);
								$tkh_descricao		.= ' | Solicitante: '.$aTicketAnt['solic_user_nome'].' >> '.$aUserAlter['user_nome'];
							}
						}
						if ( isset($postData->aResponsavel) ) {
							if ( $aTicketAnt['resp_user_id'] <> $postData->aResponsavel->tku_user_id ) {
								$aUserAlter			= $usuarioDAO->buscaById($postData->aResponsavel->tku_user_id);
								$tkh_descricao		.= ' | Responsável: '.$aTicketAnt['resp_user_nome'].' >> '.$aUserAlter['user_nome'];
							}
						}
						$tkh_descricao			= $security->getUser_nome().' Alterou esse ticket.'.$tkh_descricao;
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketDAO->Deleta($Ticket)) {
				 		$lOk = true;
						$TicketDAO->cReturnMsg  = 'Ticket excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Excluiu esse ticket.';
				} else {
			 		$lOk = false;
					$TicketDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketDAO->Restaura($Ticket)) {
				 		$lOk = true;
						$TicketDAO->cReturnMsg  = 'Ticket restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Restaurou esse ticket.';
				} else {
			 		$lOk = false;
					$TicketDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			if ( $lOk ) {
				$logEdicaoDAO 	= new LogEdicaoDAO();
				$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'ticket', $nRetId, $postData->ctrlaction, 'ticket', Date('Y-m-d H:i:s'));
				$logEdicaoDAO->Insere($logEdicao);


				$TicketHistorico = new TicketHistorico(0,$nRetId,$security->getUser_id(),Date('Y-m-d H:i:s'),$tkh_descricao);
				$TicketHistoricoDAO->Insere($TicketHistorico);
			}

			echo json_encode(array("return"=>$lOk, "msg"=>$TicketDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
