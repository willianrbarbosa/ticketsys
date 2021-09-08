<?php
	session_start();

	include('class/security.class.php');
	include('class/EmailHelper.class.php');
	include('class/TicketComentariosDAO.class.php');
	include('class/TicketComentarios.class.php');
	include('class/TicketDAO.class.php');
	include('class/TicketHistoricoDAO.class.php');
	include('class/TicketHistorico.class.php');
	include('class/TicketUsuariosDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security 		= new Security();	
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_comentarios', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData 				= json_decode($post);
			$TicketDAO 				= new TicketDAO();			
			$TicketHistoricoDAO 	= new TicketHistoricoDAO();
			$TicketComentariosDAO 	= new TicketComentariosDAO();
			$TicketUsuariosDAO 		= new TicketUsuariosDAO();
			$lOk  = false;

			$aTicket = $TicketDAO->buscaByID($postData->tkc_tkt_id, '');

			if ( $postData->ctrlaction == 'new' ) {
				$tkc_id 		= 0;
				$tkc_user_id 	= $security->getUser_id();
				$tkc_data_hora 	= Date('Y-m-d H:i:s');

				if ( $tkc_user_id == $aTicket['resp_user_id'] ) {
					$tkc_tipo 	= 'R';
				} elseif ( $tkc_user_id == $aTicket['solic_user_id'] ) {
					$tkc_tipo 	= 'S';
				} else {
					$tkc_tipo 	= 'O';
				}
			} else {
				$tkc_id 		= $postData->tkc_id;
				$tkc_user_id 	= $postData->tkc_user_id;
				$tkc_data_hora 	= (!Empty($postData->tkc_data_hora) ? implode("-", array_reverse(explode("/", explode(" ", $postData->tkc_data_hora)[0]))).' '.explode(" ", $postData->tkc_data_hora)[1] : Date('Y-m-d H:i:s'));
				$tkc_tipo 		= (!Empty($postData->tkc_tipo) ? $postData->tkc_tipo : null);
			}
			$tkc_tkt_id 		= $postData->tkc_tkt_id;
			$tkc_descricao 		= (!Empty($postData->tkc_descricao) ? $postData->tkc_descricao : null);

			$TicketComentarios 	= new TicketComentarios($tkc_id,$tkc_tkt_id,$tkc_user_id,$tkc_data_hora,$tkc_descricao,$tkc_tipo);

			$nRetId = $tkc_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					if ($TicketComentariosDAO->Insere($TicketComentarios)) {
				 		$lOk = true;
						$nRetId = $TicketComentariosDAO->id_inserido;
						$TicketComentariosDAO->cReturnMsg  = 'Comentários do Ticket inserido com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Adicionou um comentário a esse ticket.';

					// - Aqui vai enviar o e-mail ao solicitante avisando do novo comentário
					$cCorpoEmail = file_get_contents('../formato_email/ticket_comentario.html', true);					
					$cAssunto	= 'Ticket #'.$aTicket['tkt_id'].' - Novo comentario';

					if ( !Empty($cCorpoEmail) ) {
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
							}
						}

						if ( $aTicket['solic_tku_notif_email'] == 'S' ) {
							$aEmailsDest	.= $aTicket['solic_user_email'].';';
							$aNomesDest		.= $aTicket['solic_user_nome'].';';
						}
						if ( $aTicket['resp_tku_notif_email'] == 'S' ) {
							$aEmailsDest	.= $aTicket['resp_user_email'].';';
							$aNomesDest		.= $aTicket['resp_user_nome'].';';
						}

						$cCorpoEmail = str_replace('$solicitante_nome',		$aTicket['solic_user_nome'], 	$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_id',				$aTicket['tkt_id'], 			$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_titulo',			$aTicket['tkt_titulo'], 		$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_abertura_data',	Date('d/m/Y H:i', strtotime($aTicket['tkt_abertura_data'])), 	$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_situacao',			$aTicket['stt_descricao'],		$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_responsavel',		$aTicket['resp_user_nome'],		$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_descricao',		$aTicket['tkt_descricao'],		$cCorpoEmail);

						$cCorpoEmail = str_replace('$tkt_comentario_user',	$security->getUser_nome(),		$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_comentario_data',	Date('d/m/Y H:i'),				$cCorpoEmail);
						$cCorpoEmail = str_replace('$tkt_comentario',		$tkc_descricao,					$cCorpoEmail);

						$lAuditor	= false;
						if ( strstr($aTicket['pst_descricao'], 'Auditor') ) {
							$cCorpoEmail = str_replace('$display_ticket',	'none',							$cCorpoEmail);
							$cCorpoEmail = str_replace('$display_auditor',	'block',						$cCorpoEmail);
							$lAuditor	= true;
						} else {
							$cCorpoEmail = str_replace('$display_ticket',	'block',						$cCorpoEmail);
							$cCorpoEmail = str_replace('$display_auditor',	'none',							$cCorpoEmail);
						}

						$cCorpoEmail = str_replace('$data_envio',		Date('d/m/Y'), 						$cCorpoEmail);
						$cCorpoEmail = str_replace('$hora_envio',		Date('H:i:s'), 						$cCorpoEmail);
						
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
						$TicketComentariosDAO->cReturnMsg  .= '<br/>'.$cMsgRetEmail;
					}
				} else {
			 		$lOk = false;
					$TicketComentariosDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($TicketComentariosDAO->Altera($TicketComentarios)) {
				 		$lOk = true;
						$TicketComentariosDAO->cReturnMsg  = 'Comentários do Ticket alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Alterou um comentário desse ticket.';
				} else {
			 		$lOk = false;
					$TicketComentariosDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketComentariosDAO->Deleta($TicketComentarios)) {
				 		$lOk = true;
						$TicketComentariosDAO->cReturnMsg  = 'Comentários do Ticket excluído com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Excluiu um comentário desse ticket.';
				} else {
			 		$lOk = false;
					$TicketComentariosDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketComentariosDAO->Restaura($TicketComentarios)) {
				 		$lOk = true;
						$TicketComentariosDAO->cReturnMsg  = 'Comentários do Ticket restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Restaurou um comentário desse ticket.';
				} else {
			 		$lOk = false;
					$TicketComentariosDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}


			if ( $lOk ) {
				$logEdicaoDAO 	= new logEdicaoDAO();
				$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'ticket_comentarios', $nRetId, $postData->ctrlaction, 'ticket_comentarios', Date('Y-m-d H:i:s'));
				$logEdicaoDAO->Insere($logEdicao);


				$TicketHistorico = new TicketHistorico(0,$tkc_tkt_id,$security->getUser_id(),Date('Y-m-d H:i:s'),$tkh_descricao);
				$TicketHistoricoDAO->Insere($TicketHistorico);
			}

			echo json_encode(array("return"=>$lOk, "msg"=>$TicketComentariosDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
