<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\TicketApontamentosDAO;
	use TicketSys\Model\Classes\TicketApontamentos;
	use TicketSys\Model\Classes\TicketDAO;
	use TicketSys\Model\Classes\TicketHistoricoDAO;
	use TicketSys\Model\Classes\TicketHistorico;
	use TicketSys\Model\Classes\LogEdicaoDAO;
	use TicketSys\Model\Classes\LogEdicao;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;
	use DateTime;

	$security = new Security();
	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( $security->Exist() ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_apontamentos', $security->getUser_pfa_id());

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}

		$post = file_get_contents("php://input");

		if( $post ) {
			$postData 				= json_decode($post);
			$TicketDAO 				= new TicketDAO();
			$TicketHistoricoDAO 	= new TicketHistoricoDAO();
			$TicketApontamentosDAO 	= new TicketApontamentosDAO();
			$lOk  = false;

			if ( $postData->ctrlaction == 'new' ) {
				$tkp_id 		= 0;
				$tkp_user_id 	= $security->getUser_id();
			} else {
				$tkp_id 		= $postData->tkp_id;
				$tkp_user_id 	= $postData->tkp_user_id;
			}
			$tkp_data 			= (!Empty($postData->tkp_data) ? implode("-", array_reverse(explode("/", $postData->tkp_data))) : Date('Y-m-d'));
			$tkp_tkt_id 		= $postData->tkp_tkt_id;
			$tkp_hora_exec_ini 	= (!Empty($postData->tkp_hora_exec_ini) ? $postData->tkp_hora_exec_ini : Date('H:i:s'));
			if ( $postData->ctrlaction == 'stop' ) {
				$tkp_hora_exec_fim 	= Date('H:i:s');
			} else {
				$tkp_hora_exec_fim 	= (!Empty($postData->tkp_hora_exec_fim) ? $postData->tkp_hora_exec_fim : null);
			}

			if ( !Empty($tkp_hora_exec_fim) ) {
				if ( $security->OnlyNumbers($tkp_hora_exec_ini) > $security->OnlyNumbers($tkp_hora_exec_fim) ) {
			 		$lOk = false;
					$TicketApontamentosDAO->cReturnMsg  = 'Hora do apontamento inicial maior que final. Verifique!';
					echo json_encode(array("return"=>$lOk, "msg"=>$TicketApontamentosDAO->cReturnMsg, "id"=>0));
					exit();
				}


				$dDataInicio 	= new DateTime( implode("-", array_reverse(explode("/", $tkp_data))).' '.$tkp_hora_exec_ini);
				$dDataFim 		= new DateTime( implode("-", array_reverse(explode("/", $tkp_data))).' '.$tkp_hora_exec_fim);
				$aDiferenca		= $dDataInicio->diff($dDataFim);
				$tkp_horas_total= $aDiferenca->h + ($aDiferenca->i / 60);
			} else {
				$tkp_horas_total= 0;
			}

			$TicketApontamentos = new TicketApontamentos($tkp_id,$tkp_tkt_id,$tkp_user_id,$tkp_data,$tkp_hora_exec_ini,$tkp_hora_exec_fim,$tkp_horas_total);

			$nRetId = $tkp_id;

			if ( $postData->ctrlaction == 'new' ) {
				if ( $aUserRotina['pta_nivel'] >= 1 ) {
					//Aqui valida se clicou no "play" e já tem algum outro apontamento em aberto
					$aApontamentoPendenteUser = $TicketApontamentosDAO->buscaEmExecucaoUsuario($security->getUser_id());
					if ( (!Empty($aApontamentoPendenteUser)) AND (Empty($tkp_hora_exec_fim)) ) {
				 		$lOk = false;
						$TicketApontamentosDAO->cReturnMsg  = 'Você já tem apontamento em execução.<br/><b>Ticket: '.$aApontamentoPendenteUser['tkt_id'].'</b><br/>'.$aApontamentoPendenteUser['tkt_titulo'];
					} else {
						if ($TicketApontamentosDAO->Insere($TicketApontamentos)) {
					 		$lOk = true;
							$nRetId = $TicketApontamentosDAO->id_inserido;
							$TicketApontamentosDAO->cReturnMsg  = (!Empty($tkp_hora_exec_fim) ? 'Apontamento do Ticket inserido com sucesso.' : 'Apontamento do Ticket iniciado.');

							//Aqui vai apontar a data de início do ticket e percentual concluído
							$aTicket = $TicketDAO->buscaByID($tkp_tkt_id, '');
							if ( !Empty($aTicket) ) {
								$nEsforcoEstimado	= (float)(!Empty($aTicket['tkt_total_hora_estim']) ? $aTicket['tkt_total_hora_estim'] : 0);
								$nEsforcoReal		= (float)(!Empty($aTicket['tkt_total_hora_real']) ? $aTicket['tkt_total_hora_real'] : 0) + $tkp_horas_total;
								if ( $nEsforcoEstimado > 0 ) {
									$tkt_per_concluido 	= ($nEsforcoReal * 100) / $nEsforcoEstimado;
									// $tkt_per_concluido	= ($tkt_per_concluido > 100 ? 100 : $tkt_per_concluido);
								} else {
									$tkt_per_concluido 	= 0;
								}

								if ( !Empty($tkp_hora_exec_fim) ) {
									if ( Empty($aTicket['tkt_data_ini_real']) ) {
										$TicketDAO->AtualizaDataInicioEsforcoReaPerConcluidol($aTicket['tkt_id'], $tkp_data, $tkp_hora_exec_ini, $nEsforcoReal, $tkt_per_concluido);
									} else {
										$TicketDAO->AtualizaEsforcoReal($aTicket['tkt_id'], $nEsforcoReal);
										$TicketDAO->AtualizaPerConcluido($aTicket['tkt_id'], $tkt_per_concluido);
									}

									$tkh_descricao			= $security->getUser_nome().' Fez um apontamento manual nesse ticket. Data: '.Date('d/m/Y', strtotime($tkp_data)).' das '.$tkp_hora_exec_ini.' ás '.$tkp_hora_exec_fim;
								} else {
									if ( Empty($aTicket['tkt_data_ini_real']) ) {
										$TicketDAO->AtualizaDataInicioReal($aTicket['tkt_id'], $tkp_data, $tkp_hora_exec_ini);
									}

									$tkh_descricao			= $security->getUser_nome().' Iniciou um apontamento nesse ticket. Data: '.Date('d/m/Y', strtotime($tkp_data)).' iníciado ás '.$tkp_hora_exec_ini.'.';
								}
							}
				 		} else {
				 			$lOk = false;
						}
					}
				} else {
			 		$lOk = false;
					$TicketApontamentosDAO->cReturnMsg  = 'Usuário sem acesso para incluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'stop' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($TicketApontamentosDAO->Altera($TicketApontamentos)) {
				 		$lOk = true;
						$TicketApontamentosDAO->cReturnMsg  = 'Apontamento do Ticket finalizado.';

						//Aqui vai apontar a data de fim do ticket e percentual concluído
						$aTicket = $TicketDAO->buscaByID($tkp_tkt_id, '');
						if ( !Empty($aTicket) ) {
							$nEsforcoEstimado	= (float)(!Empty($aTicket['tkt_total_hora_estim']) ? $aTicket['tkt_total_hora_estim'] : 0);
							$nEsforcoReal		= (float)(!Empty($aTicket['tkt_total_hora_real']) ? $aTicket['tkt_total_hora_real'] : 0) + $tkp_horas_total;
							if ( $nEsforcoEstimado > 0 ) {
								$tkt_per_concluido = ($nEsforcoReal * 100) / $nEsforcoEstimado;
								// $tkt_per_concluido	= ($tkt_per_concluido > 100 ? 100 : $tkt_per_concluido);
							} else {
								$tkt_per_concluido 	= 0;
							}

							$TicketDAO->AtualizaEsforcoReal($aTicket['tkt_id'], $nEsforcoReal);
							$TicketDAO->AtualizaPerConcluido($aTicket['tkt_id'], $tkt_per_concluido);
							// if ( $tkt_per_concluido >= 100 ) {
							// 	$TicketDAO->AtualizaDataFimReal($aTicket['tkt_id'], $tkp_data, $tkp_hora_exec_fim);
							// }

							$tkh_descricao			= $security->getUser_nome().' Finalizou um apontamento nesse ticket. Data: '.Date('d/m/Y', strtotime($tkp_data)).' finalizado ás '.$tkp_hora_exec_fim.'.';
						}
			 		} else {
			 			$lOk = false;
					}
				} else {
			 		$lOk = false;
					$TicketApontamentosDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'edit' ) {
				if ( $aUserRotina['pta_nivel'] >= 2 ) {
					if ($TicketApontamentosDAO->Altera($TicketApontamentos)) {
				 		$lOk = true;
						$TicketApontamentosDAO->cReturnMsg  = 'Apontamento do Ticket alterado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Alterou um apontamento desse ticket.';
				} else {
			 		$lOk = false;
					$TicketApontamentosDAO->cReturnMsg  = 'Usuário sem acesso para alterar registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'delete' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketApontamentosDAO->Deleta($TicketApontamentos)) {
				 		$lOk = true;
						$TicketApontamentosDAO->cReturnMsg  = 'Apontamento do Ticket excluído com sucesso.';

						//Aqui vai estornar o apontamento do ticket
						$aTicket = $TicketDAO->buscaByID($tkp_tkt_id, '');
						if ( !Empty($aTicket) ) {
							$nEsforcoEstimado	= (float)(!Empty($aTicket['tkt_total_hora_estim']) ? $aTicket['tkt_total_hora_estim'] : 0);
							$nEsforcoReal		= (float)(!Empty($aTicket['tkt_total_hora_real']) ? $aTicket['tkt_total_hora_real'] : 0) - $tkp_horas_total;
							$nEsforcoReal		= ($nEsforcoReal > 0 ? $nEsforcoReal : 0);
							if ( $nEsforcoEstimado > 0 ) {
								$tkt_per_concluido = ($nEsforcoReal * 100) / $nEsforcoEstimado;
								// $tkt_per_concluido	= ($tkt_per_concluido > 100 ? 100 : $tkt_per_concluido);
							} else {
								$tkt_per_concluido 	= 0;
							}

							$aTicketApontamentos = $TicketApontamentosDAO->buscaByTicket($aTicket['tkt_id']);
							if ( Empty($aTicketApontamentos) ) {
								$TicketDAO->AtualizaDataInicioEsforcoReaPerConcluidol($aTicket['tkt_id'], null, null, $nEsforcoReal, $tkt_per_concluido);
							} else {
								$TicketDAO->AtualizaEsforcoReal($aTicket['tkt_id'], $nEsforcoReal);
								$TicketDAO->AtualizaPerConcluido($aTicket['tkt_id'], $tkt_per_concluido);
							}
						}
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Excluiu um apontamento desse ticket.';
				} else {
			 		$lOk = false;
					$TicketApontamentosDAO->cReturnMsg  = 'Usuário sem acesso para excluir registro nessa rotina.';
				}
			} elseif ( $postData->ctrlaction == 'restore' ) {
				if ( $aUserRotina['pta_nivel'] >= 3 ) {
					if ($TicketApontamentosDAO->Restaura($TicketApontamentos)) {
				 		$lOk = true;
						$TicketApontamentosDAO->cReturnMsg  = 'Apontamento do Ticket restaurado com sucesso.';
			 		} else {
			 			$lOk = false;
					}
					$tkh_descricao			= $security->getUser_nome().' Restaurou um apontamento desse ticket.';
				} else {
			 		$lOk = false;
					$TicketApontamentosDAO->cReturnMsg  = 'Usuário sem acesso para restaurar registro nessa rotina.';
				}
			}

			if ( $lOk ) {
				$logEdicaoDAO 	= new LogEdicaoDAO();
				$logEdicao 		= new LogEdicao(0, $security->getUser_id(), 'ticket_apontamentos', $nRetId, $postData->ctrlaction, 'ticket_apontamentos', Date('Y-m-d H:i:s'));
				$logEdicaoDAO->Insere($logEdicao);

				$TicketHistorico = new TicketHistorico(0,$tkp_tkt_id,$security->getUser_id(),Date('Y-m-d H:i:s'),$tkh_descricao);
				$TicketHistoricoDAO->Insere($TicketHistorico);
			}

			echo json_encode(array("return"=>$lOk, "msg"=>$TicketApontamentosDAO->cReturnMsg, "id"=>$nRetId));
		}
	}
?>
