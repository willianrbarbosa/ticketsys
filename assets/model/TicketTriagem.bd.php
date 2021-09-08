<?php
	session_start();

	include('class/security.class.php');
	include('class/TicketDAO.class.php');
	include('class/Ticket.class.php');
	include('class/TicketArquivosDAO.class.php');
	include('class/TicketArquivos.class.php');
	include('class/TicketUsuariosDAO.class.php');
	include('class/TicketUsuarios.class.php');
	include('class/TicketHistoricoDAO.class.php');
	include('class/TicketHistorico.class.php');
	include('class/PastaTrabalhoDAO.class.php');
	include('class/TipoAtividadeDAO.class.php');
	include('class/SituacaoTicketDAO.class.php');
	include('class/CategoriaTicketDAO.class.php');
	include('class/PrioridadeTicketDAO.class.php');
	include('class/usuarioDAO.class.php');
	include('class/perfilAcessoRotinaDAO.class.php');
	include('class/logEdicao.class.php');
	include('class/logEdicaoDAO.class.php');

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new perfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('triagemTicket', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array("return"=>false, "msg"=>'Usuário sem acesso a essa rotina.', "id"=>0));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData 				= json_decode($post);
			$TicketDAO 				= new TicketDAO();
			$TicketUsuariosDAO 		= new TicketUsuariosDAO();
			$TicketHistoricoDAO 	= new TicketHistoricoDAO();
			$PastaTrabalhoDAO 		= new PastaTrabalhoDAO();
			$TipoAtividadeDAO 		= new TipoAtividadeDAO();
			$SituacaoTicketDAO 		= new SituacaoTicketDAO();
			$CategoriaTicketDAO 	= new CategoriaTicketDAO();
			$PrioridadeTicketDAO 	= new PrioridadeTicketDAO();
			$usuarioDAO 			= new usuarioDAO();
			$lOk  = false;
			$tkh_descricao			= '';
						
			$tkt_id 				= $postData->tkt_id;
			$tkt_pst_id 			= $postData->tkt_pst_id;
			$tkt_titulo 			= $postData->tkt_titulo;
			$tkt_tav_id 			= $postData->tkt_tav_id;
			$tkt_descricao 			= $postData->tkt_descricao;
			$tkt_abertura_data 		= implode("-", array_reverse(explode("/", explode(" ", $postData->tkt_abertura_data)[0]))).' '.explode(" ", $postData->tkt_abertura_data)[1];
			$tkt_abertura_user_id 	= $postData->tkt_abertura_user_id;				
			$tkt_stt_id 			= 2;
			$tkt_cgt_id 			= $postData->tkt_cgt_id;
			$tkt_prt_id 			= $postData->tkt_prt_id;
			$tkt_ort_id 			= $postData->tkt_ort_id;

			$tkt_data_ini_estim 	= (!Empty($postData->tkt_data_ini_estim) ? implode("-", array_reverse(explode("/", $postData->tkt_data_ini_estim))) : null);
			$tkt_hora_ini_estim 	= (!Empty($postData->tkt_hora_ini_estim) ? implode("-", array_reverse(explode("/", $postData->tkt_hora_ini_estim))) : null);
			$tkt_data_ini_real 		= null;
			$tkt_hora_ini_real 		= null;
			$tkt_data_fim_estim 	= (!Empty($postData->tkt_data_fim_estim) ? implode("-", array_reverse(explode("/", $postData->tkt_data_fim_estim))) : null);
			$tkt_hora_fim_estim 	= (!Empty($postData->tkt_hora_fim_estim) ? implode("-", array_reverse(explode("/", $postData->tkt_hora_fim_estim))) : null);
			$tkt_data_fim_real 		= null;
			$tkt_hora_fim_real 		= null;
			$tkt_total_hora_estim 	= (!Empty($postData->tkt_total_hora_estim) ? (strpos( $postData->tkt_total_hora_estim, "," ) ? str_replace(",", ".", str_replace(".", "", $postData->tkt_total_hora_estim)) : $postData->tkt_total_hora_estim) : null);
			$tkt_total_hora_real 	= 0;				

			$tkt_per_concluido 		= 0;

			$tkt_aprovado 			= 'N';
			$tkt_aprovado_data 		= null;
			$tkt_aprovado_user_id 	= null;

			$tkt_encerrado 			= 'N';
			$tkt_encerrado_data 	= null;
			$tkt_encerrado_user_id 	= null;

			$tkt_ticket_pai 		= (!Empty($postData->tkt_ticket_pai) ? $postData->tkt_ticket_pai : null);

			$tkt_arquivado 			= 'N';
			$tkt_arquivado_data 	= null;
			$tkt_arquivado_user_id 	= null;

			$Ticket = new Ticket($tkt_id,$tkt_pst_id,$tkt_titulo,$tkt_tav_id,$tkt_descricao,$tkt_abertura_data,$tkt_abertura_user_id,$tkt_stt_id,$tkt_cgt_id,$tkt_prt_id,
								$tkt_ort_id,$tkt_data_ini_estim,$tkt_hora_ini_estim,$tkt_data_ini_real,$tkt_hora_ini_real,$tkt_data_fim_estim,$tkt_hora_fim_estim,$tkt_data_fim_real,$tkt_hora_fim_real,$tkt_total_hora_estim,
								$tkt_total_hora_real,$tkt_per_concluido,
								$tkt_aprovado,$tkt_aprovado_data,$tkt_aprovado_user_id,
								$tkt_encerrado,$tkt_encerrado_data,$tkt_encerrado_user_id,
								$tkt_arquivado,$tkt_arquivado_data,$tkt_arquivado_user_id,
								$tkt_ticket_pai);

			if ( $aUserRotina['pta_nivel'] >= 3 ) {
				if ($TicketDAO->Altera($Ticket)) {
			 		$lOk = true;
					$TicketDAO->cReturnMsg  = 'Triagem do Ticket realizada com sucesso.';

					//Aqui vai inserir o solicitante
					if ( isset($postData->aSolicitante) ) {

						$tku_user_id 			= $postData->aSolicitante->tku_user_id;
						$tku_tkt_id 			= $tkt_id;
						$tku_notif_email		= (isset($postData->aSolicitante->tku_notif_email) ? ($postData->aSolicitante->tku_notif_email ? 'S' : 'N') : 'N');
						$tku_notif_sistema		= (isset($postData->aSolicitante->tku_notif_sistema) ? ($postData->aSolicitante->tku_notif_sistema ? 'S' : 'N') : 'N');

						$aTicketSolicitante 	= $TicketUsuariosDAO->buscaByTipo($tku_tkt_id, 'S');
						if ( !Empty($aTicketSolicitante) ) {
							$TicketUsuarios 		= new TicketUsuarios($aTicketSolicitante[0]['tku_id'],$tku_tkt_id,$tku_user_id,'S',$tku_notif_email,$tku_notif_sistema);
							$TicketUsuariosDAO->Altera($TicketUsuarios);
						} else {
							$TicketUsuarios 		= new TicketUsuarios(0,$tku_tkt_id,$tku_user_id,'S',$tku_notif_email,$tku_notif_sistema);
							$TicketUsuariosDAO->Insere($TicketUsuarios);
						}
					}

					//Aqui vai inserir os Observadores
					if ( isset($postData->aObservadores) ) {							
						for ($i=0; $i < count($postData->aObservadores); $i++) { 
							$tku_user_id 			= $postData->aObservadores[$i]->tku_user_id;
							$tku_tkt_id 			= $tkt_id;
							$tku_notif_email		= (isset($postData->aObservadores[$i]->tku_notif_email) ? ($postData->aObservadores[$i]->tku_notif_email ? 'S' : 'N') : 'N');
							$tku_notif_sistema		= (isset($postData->aObservadores[$i]->tku_notif_sistema) ? ($postData->aObservadores[$i]->tku_notif_sistema ? 'S' : 'N') : 'N');

							$aTicketObservador 		= $TicketUsuariosDAO->buscaByTicketUserIDTipo($tku_tkt_id, $tku_user_id, 'O');
							if ( !Empty($aTicketObservador) ) {
								$TicketUsuarios 		= new TicketUsuarios($aTicketObservador['tku_id'],$tku_tkt_id,$tku_user_id,'O',$tku_notif_email,$tku_notif_sistema);
								$TicketUsuariosDAO->Altera($TicketUsuarios);
							} else {
								$TicketUsuarios 		= new TicketUsuarios(0,$tku_tkt_id,$tku_user_id,'O',$tku_notif_email,$tku_notif_sistema);
								$TicketUsuariosDAO->Insere($TicketUsuarios);
							}
						}
					}

					//Aqui vai inserir o Responsável
					if ( isset($postData->aResponsavel) ) {
						$tku_user_id 			= $postData->aResponsavel->tku_user_id;
						$tku_tkt_id 			= $tkt_id;
						$tku_notif_email		= (isset($postData->aResponsavel->tku_notif_email) ? ($postData->aResponsavel->tku_notif_email ? 'S' : 'N') : 'N');
						$tku_notif_sistema		= (isset($postData->aResponsavel->tku_notif_sistema) ? ($postData->aResponsavel->tku_notif_sistema ? 'S' : 'N') : 'N');

						$aTicketResponsavel 	= $TicketUsuariosDAO->buscaByTipo($tku_tkt_id, 'R');
						if ( !Empty($aTicketResponsavel) ) {
							$TicketUsuarios 		= new TicketUsuarios($aTicketResponsavel[0]['tku_id'],$tku_tkt_id,$tku_user_id,'R',$tku_notif_email,$tku_notif_sistema);
							$TicketUsuariosDAO->Altera($TicketUsuarios);
						} else {
							$TicketUsuarios 		= new TicketUsuarios(0,$tku_tkt_id,$tku_user_id,'R',$tku_notif_email,$tku_notif_sistema);
							$TicketUsuariosDAO->Insere($TicketUsuarios);
						}
					}
		 		} else {
		 			$lOk = false;
				}
			} else {
		 		$lOk = false;
				$TicketDAO->cReturnMsg  = 'Usuário sem acesso para fazer triagem de Tickets.';
			}

			if ( $lOk ) {
				$logEdicaoDAO 	= new logEdicaoDAO();
				$logEdicao 		= new logEdicao(0, $security->getUser_id(), 'triagemTicket', $tkt_id, 'triagem', 'ticket', Date('Y-m-d H:i:s'));
				$logEdicaoDAO->Insere($logEdicao);

				$aPastaTrabalho 		= $PastaTrabalhoDAO->buscaByID($tkt_pst_id);
				if ( !Empty($aPastaTrabalho) ) {
					$tkh_descricao			.= ' | Pasta de Trabalho: '.$aPastaTrabalho['pst_descricao'];
				}

				$aTipoAtividade 		= $TipoAtividadeDAO->buscaByID($tkt_tav_id);
				if ( !Empty($aTipoAtividade) ) {
					$tkh_descricao			.= ' | Tipo de Atividade: '.$aTipoAtividade['tav_descricao'];
				}

				$aSituacaoTicket 		= $SituacaoTicketDAO->buscaByID($tkt_stt_id);
				if ( !Empty($aSituacaoTicket) ) {
					$tkh_descricao			.= ' | Situação: '.$aSituacaoTicket['stt_descricao'];
				}

				$aCategoriaTicket 		= $CategoriaTicketDAO->buscaByID($tkt_cgt_id);
				if ( !Empty($aCategoriaTicket) ) {
					$tkh_descricao			.= ' | Categoria: '.$aCategoriaTicket['cgt_descricao'];
				}

				$aPrioridadeTicket 		= $PrioridadeTicketDAO->buscaByID($tkt_prt_id);
				if ( !Empty($aPrioridadeTicket) ) {
					$tkh_descricao			.= ' | Prioridade: '.$aPrioridadeTicket['prt_descricao'];
				}

				
				if ( isset($postData->aResponsavel) ) {
					$aUser 				= $usuarioDAO->buscaById($postData->aResponsavel->tku_user_id);
					$tkh_descricao		.= ' | Responsável: '.$aUser['user_nome'].'.';
				}

				$tkh_descricao		= $security->getUser_nome().' Fez a triagem desse ticket.'.$tkh_descricao;
				$TicketHistorico 	= new TicketHistorico(0,$tkt_id,$security->getUser_id(),Date('Y-m-d H:i:s'),$tkh_descricao);
				$TicketHistoricoDAO->Insere($TicketHistorico);
			}

			echo json_encode(array("return"=>$lOk, "msg"=>$TicketDAO->cReturnMsg, "id"=>$tkt_id));
		}
	}
?>
