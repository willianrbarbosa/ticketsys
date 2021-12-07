<?php 
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\TicketDAO;
	use TicketSys\Model\Classes\TicketApontamentosDAO;
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;
	require_once('session_vars.php');

	$TicketDAO 				= new TicketDAO();
	$TicketApontamentosDAO 	= new TicketApontamentosDAO();
	$perfilAcessoRotinaDAO 	= New perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_rel_gerencial', SEC_USER_PFA_ID);

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}
		
		$post = file_get_contents("php://input");

		if ( $post ) {
			$postData 		= json_decode($post);


			$cDataDe		= (!Empty($postData->tkt_rel_gen_data_de) ? implode("-", array_reverse(explode("/", $postData->tkt_rel_gen_data_de))) : Date('Y-m-d'));
			$cDataAte		= (!Empty($postData->tkt_rel_gen_data_ate) ? implode("-", array_reverse(explode("/", $postData->tkt_rel_gen_data_ate))) : Date('Y-m-d'));
			
			$nTipoRel		= (isset($postData->tipo_rel) ? $postData->tipo_rel : null);			
			// nTipoRel: 1 => Novos Tickets por Solicitante
			// nTipoRel: 2 => Novos Tickets por Responsável
			// nTipoRel: 3 => Novos Tickets por Dia
			// nTipoRel: 4 => Tickets encerradas por Solicitante
			// nTipoRel: 5 => Tickets encerradas por Responsável
			// nTipoRel: 6 => Tickets encerradas por Dia
			// nTipoRel: 7 => Horas apontadas por Solicitante
			// nTipoRel: 8 => Horas apontadas por Responsável
			// nTipoRel: 9 => Horas apontadas por Dia

			$nPastaID		= (isset($postData->fil_tkt_pst_id) ? $postData->fil_tkt_pst_id : null);
			$nSituacaoID	= (isset($postData->fil_tkt_stt_id) ? $postData->fil_tkt_stt_id : null);
			$nTipoAtvID		= (isset($postData->fil_tkt_tav_id) ? $postData->fil_tkt_tav_id : null);
			$nCategID		= (isset($postData->fil_tkt_cgt_id) ? $postData->fil_tkt_cgt_id : null);
			$nPrioridadeID	= (isset($postData->fil_tkt_prt_id) ? $postData->fil_tkt_prt_id : null);
			$nOrigemID		= (isset($postData->fil_tkt_ort_id) ? $postData->fil_tkt_ort_id : null);

			$nSolicID		= (isset($postData->fil_tkt_solic_id) ? $postData->fil_tkt_solic_id : null);
			$nRespID		= (isset($postData->fil_tkt_resp_id) ? $postData->fil_tkt_resp_id : null);

			$aErrorMsg		= array();

			$nContTickets		= 0;
			$aReport 		= array();
			$aHTMLRet 		= array();

			$cRptErro		= '';
			$cRptLegenda 	= '';
			$cReportPDF 	= '
			<style>
				.bgdv { background-color: #ffEEAA !important; font-weight: bold !important; }
				.ljt{ text-align: left; }
				.cjt{ text-align: center; }
				.rjt{ text-align: right; }
				.bdl { border-left: 3px solid #000 !important; }
				.bdd { border-left: 1px solid #CC0000 !important; border-top: 1px solid #CC0000 !important; border-right: 1px solid #CC0000 !important; border-bottom: 1px solid #CC0000 !important; }
				.fs-S { color: #ff4444 !important; font-weight: bold; }
				.fs-B { color: #00C851 !important; font-weight: bold; }
				.bdh { border: 1px solid #FFF; }
				.table-rpt { width: 100%; }
				.table-rpt thead th { background-color: #DEDEDE; color: #000; font-size: 10px; font-family: Calibri; }
				.table-rpt tbody td { font-size: 10px; font-family: Calibri; }
				.text-nowrap { white-space: nowrap !important; }
				.circle {	border-radius: 50%!important; }
				.w100-rpt { width: 100% !important; }
				.lbtit { font-family: Calibri; font-weight: bold !important; font-size: 11px !important; }
				.bgtot { background-color: #E3F2FD !important; }
				.lbtot { color: #1266F1 !important; font-weight: bold !important; font-size: 12px !important; }
				.lbbold { font-weight: bold !important; vertical-align: middle !important; }
				.new-page {	page-break-before: always;	}
			</style>';

			$cWhere 	= " AND tkt_stt_id > 1 "; //Desconsiderar Tickets com aSituação em triagem
			if ( !Empty($nPastaID) ) {
				$cWhere 	.= " AND tkt_pst_id = ".$nPastaID;
			}
			if ( !Empty($nSituacaoID) ) {
				$cWhere 	.= " AND tkt_stt_id = ".$nSituacaoID;
			}
			if ( !Empty($nTipoAtvID) ) {
				$cWhere 	.= " AND tkt_tav_id = ".$nTipoAtvID;
			}
			if ( !Empty($nCategID) ) {
				$cWhere 	.= " AND tkt_cgt_id = ".$nCategID;
			}
			if ( !Empty($nPrioridadeID) ) {
				$cWhere 	.= " AND tkt_prt_id = ".$nPrioridadeID;
			}
			if ( !Empty($nOrigemID) ) {
				$cWhere 	.= " AND tkt_ort_id = ".$nOrigemID;
			}

			if ( !Empty($nSolicID) ) {
				$cWhere 	.= " AND usr_solic.user_id = ".$nSolicID;
			}
			if ( !Empty($nRespID) ) {
				if ( $nTipoRel < 7 ) {
					$cWhere 	.= " AND usr_resp.user_id = ".$nRespID;
				} elseif ( $nTipoRel >= 7 ) {
					$cWhere 	.= " AND usr_apt.user_id = ".$nRespID;
				}
			}

			$cOrderby 	= "";
			$cAuxTotaliza	= '';
			
			$cCabecalho = '
			<table class="table table-striped table-rpt" cellpadding="0" cellspacing="0" width="100%">
	  			<thead class="thead">';

			switch ($nTipoRel) {
				case 1: //1 => Novos Tickets por Solicitante
					$cAuxTotaliza	= 'Solicitante';
					$cWhere		= " AND tkt_abertura_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " solic_user_nome,tkt_abertura_data DESC,tkt_data_ini_estim,tkt_data_fim_estim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';
					break;
				case 2: //2 => Novos Tickets por Responsável
					$cAuxTotaliza	= 'Responsável';
					$cWhere		= " AND tkt_abertura_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " resp_user_nome,tkt_abertura_data DESC,tkt_data_ini_estim,tkt_data_fim_estim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';
					break;
				case 3: //3 => Novos Tickets por Dia
					$cAuxTotaliza	= 'Dia';
					$cWhere		= " AND tkt_abertura_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " tkt_abertura_data DESC,tkt_data_ini_estim,tkt_data_fim_estim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';
					break;
				case 4: //4 => Tickets encerradas por Solicitante
					$cAuxTotaliza	= 'Solicitante';
					$cWhere		= " AND tkt_encerrado = 'S' AND tkt_encerrado_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " solic_user_nome,tkt_encerrado_data DESC,tkt_data_ini_estim,tkt_data_fim_estim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';
					break;
				case 5: //5 => Tickets encerradas por Responsável
					$cAuxTotaliza	= 'Responsável';
					$cWhere		= " AND tkt_encerrado = 'S' AND tkt_encerrado_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " resp_user_nome,tkt_encerrado_data DESC,tkt_data_ini_estim,tkt_data_fim_estim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';
					break;
				case 6: //6 => Tickets encerradas por Dia
					$cAuxTotaliza	= 'Dia';
					$cWhere		= " AND tkt_encerrado = 'S' AND tkt_encerrado_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " tkt_encerrado_data DESC,tkt_data_ini_estim,tkt_data_fim_estim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';
					break;
				case 7: //7 => Horas apontadas por Solicitante
					$cAuxTotaliza	= 'Solicitante';
					$cWhere		= " AND tkp_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " solic_user_nome,tkp_data DESC,tkp_hora_exec_ini,tkp_hora_exec_fim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit">Usuário</th>
						<th class="lbtit text-nowrap">Data Apont.</th>
						<th class="lbtit text-nowrap rjt">Total horas</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';	
					break;
				case 8: //8 => Horas apontadas por Responsável
					$cAuxTotaliza	= 'Responsável';
					$cWhere		= " AND tkp_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " apont_user_nome,tkp_data DESC,tkp_hora_exec_ini,tkp_hora_exec_fim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit">Usuário</th>
						<th class="lbtit text-nowrap">Data Apont.</th>
						<th class="lbtit text-nowrap rjt">Total horas</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';		
					break;
				case 9: //9 => Horas apontadas por Dia
					$cAuxTotaliza	= 'Dia';
					$cWhere		= " AND tkp_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " tkp_data DESC,tkp_hora_exec_ini,tkp_hora_exec_fim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit">Usuário</th>
						<th class="lbtit text-nowrap">Data Apont.</th>
						<th class="lbtit text-nowrap rjt">Total horas</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap cjt">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit rjt">% EXR</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap">Aprovação</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">T. Pai</th>
					<tr/>';		
					break;
			}

			if ( $nTipoRel <= 6 ) {
				$aTickets 	= $TicketDAO->buscaByCondicao($cOrderby, $cWhere, 'all');
			} else {
				$aTickets 	= $TicketApontamentosDAO->buscaByCondicao($cOrderby, $cWhere, 'all');
			}
			
			$nSumGroup		= 0;			
			$cKeyGroup		= null;

			$cCabecalho	.= '	
					</thead>
					<tbody>';
			for ($t=0; $t < count($aTickets); $t++) { 
				switch ($nTipoRel) {
					case 1: //1 => Novos Tickets por Solicitante
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> $aTickets[$t]['solic_user_nome']) ) {
							if ( $t > 0 ) {
								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="11" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= $aTickets[$t]['solic_user_nome'];
						}

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>
							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_encerrado'] == 'S' ? 
									$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup ++;
						break;
					case 2: //2 => Novos Tickets por Responsável
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> $aTickets[$t]['resp_user_nome']) ) {
							if ( $t > 0 ) {
								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="11" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= $aTickets[$t]['resp_user_nome'];
						}

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>
							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_encerrado'] == 'S' ? 
									$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup ++;
						break;
					case 3: //3 => Novos Tickets por Dia
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data']))) ) {
							if ( $t > 0 ) {
								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="11" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data']));
						}
						
						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_encerrado'] == 'S' ? 
									$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup ++;
						break;
					case 4: //4 => Tickets encerradas por Solicitante
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> $aTickets[$t]['solic_user_nome']) ) {
							if ( $t > 0 ) {
								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="11" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= $aTickets[$t]['solic_user_nome'];
						}

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>
							<td class="text-nowrap">'.
								$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data'])).
							'</td>
							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup ++;
						break;
					case 5: //5 => Tickets encerradas por Responsável
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> $aTickets[$t]['resp_user_nome']) ) {
							if ( $t > 0 ) {
								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="11" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= $aTickets[$t]['resp_user_nome'];
						}

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>
							<td class="text-nowrap">'.
								$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data'])).
							'</td>
							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup ++;
						break;
					case 6: //6 => Tickets encerradas por Dia
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> Date('d/m/Y', strtotime($aTickets[$t]['tkt_encerrado_data']))) ) {
							if ( $t > 0 ) {
								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="11" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= Date('d/m/Y', strtotime($aTickets[$t]['tkt_encerrado_data']));
						}
						
						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>
							<td class="text-nowrap">'.
								$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data'])).
							'</td>
							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup ++;
						break;
					case 7: //7 => Horas apontadas por Solicitante
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> $aTickets[$t]['solic_user_nome']) ) {
							if ( $t > 0 ) {
								$aTempo 		= explode('.', $nSumGroup);
								$nHora			= $aTempo[0];
								$nMinuto		= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$aMinutos		= explode('.', $nMinuto);
								$nMinuto		= $aMinutos[0];
								$nSegundo		= round((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$nTotalReal 	= str_pad($nHora, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinuto, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundo, 2, '0', STR_PAD_LEFT);

								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="14" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Horas apontadas<br/>'.$nTotalReal.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= $aTickets[$t]['solic_user_nome'];
						}

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>

							<td class="text-nowrap">'.$aTickets[$t]['apont_user_nome'].'</td>
							<td class="text-nowrap">'.
								Date('d/m/Y', strtotime($aTickets[$t]['tkp_data'])).'<br/>'.
								Date('H:i:s', strtotime($aTickets[$t]['tkp_hora_exec_ini'])).' até '.Date('H:i:s', strtotime($aTickets[$t]['tkp_hora_exec_fim'])).
							'</td>
							<td class="text-nowrap rjt">'.Date('H:i:s', strtotime($aTickets[$t]['tkp_horas_total_comp'])).'</td>

							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_encerrado'] == 'S' ? 
									$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup 	+= $aTickets[$t]['tkp_horas_total'];
						break;
					case 8: //8 => Horas apontadas por Responsável
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> $aTickets[$t]['apont_user_nome']) ) {
							if ( $t > 0 ) {
								$aTempo 		= explode('.', $nSumGroup);
								$nHora			= $aTempo[0];
								$nMinuto		= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$aMinutos		= explode('.', $nMinuto);
								$nMinuto		= $aMinutos[0];
								$nSegundo		= round((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$nTotalReal 	= str_pad($nHora, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinuto, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundo, 2, '0', STR_PAD_LEFT);

								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="14" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Horas apontadas<br/>'.$nTotalReal.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= $aTickets[$t]['apont_user_nome'];
						}

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>

							<td class="text-nowrap">'.$aTickets[$t]['apont_user_nome'].'</td>
							<td class="text-nowrap">'.
								Date('d/m/Y', strtotime($aTickets[$t]['tkp_data'])).'<br/>'.
								Date('H:i:s', strtotime($aTickets[$t]['tkp_hora_exec_ini'])).' até '.Date('H:i:s', strtotime($aTickets[$t]['tkp_hora_exec_fim'])).
							'</td>
							<td class="text-nowrap rjt">'.Date('H:i:s', strtotime($aTickets[$t]['tkp_horas_total_comp'])).'</td>


							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_encerrado'] == 'S' ? 
									$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup 	+= $aTickets[$t]['tkp_horas_total'];
						break;
					case 9: //9 => Horas apontadas por Dia
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> Date('d/m/Y', strtotime($aTickets[$t]['tkp_data']))) ) {
							if ( $t > 0 ) {
								$aTempo 		= explode('.', $nSumGroup);
								$nHora			= $aTempo[0];
								$nMinuto		= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$aMinutos		= explode('.', $nMinuto);
								$nMinuto		= $aMinutos[0];
								$nSegundo		= round((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$nTotalReal 	= str_pad($nHora, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinuto, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundo, 2, '0', STR_PAD_LEFT);

								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="14" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="lbtot rjt">Horas apontadas<br/>'.$nTotalReal.'</td>
								</tr>';

							}							
							$nSumGroup		= 0;
							$cKeyGroup		= Date('d/m/Y', strtotime($aTickets[$t]['tkp_data']));
						}

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>

							<td class="text-nowrap">'.$aTickets[$t]['apont_user_nome'].'</td>
							<td class="text-nowrap">'.
								Date('d/m/Y', strtotime($aTickets[$t]['tkp_data'])).'<br/>'.
								Date('H:i:s', strtotime($aTickets[$t]['tkp_hora_exec_ini'])).' até '.Date('H:i:s', strtotime($aTickets[$t]['tkp_hora_exec_fim'])).
							'</td>
							<td class="text-nowrap rjt">'.Date('H:i:s', strtotime($aTickets[$t]['tkp_horas_total_comp'])).'</td>


							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_aprovado'] == 'S' ? 
									$aTickets[$t]['aprov_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_aprovado_data']))
									: '').
							'</td>
							<td class="text-nowrap">'.
								($aTickets[$t]['tkt_encerrado'] == 'S' ? 
									$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data']))
									: '').
							'</td>
							<td>'.$aTickets[$t]['tkt_ticket_pai'].'</td>
						</tr>';
						$nSumGroup 	+= $aTickets[$t]['tkp_horas_total'];
						break;
				}
			}			
			
			if ( $nTipoRel <= 6 ) {
				$cReportPDF .= '
				<tr class="bgtot">
					<td colspan="11" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
					<td colspan="2" class="lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
				</tr>';
			} else {	
				$aTempo 		= explode('.', $nSumGroup);
				$nHora			= $aTempo[0];
				$nMinuto		= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
				$aMinutos		= explode('.', $nMinuto);
				$nMinuto		= $aMinutos[0];
				$nSegundo		= round((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
				$nTotalReal 	= str_pad($nHora, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinuto, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundo, 2, '0', STR_PAD_LEFT);

				$cReportPDF .= '
				<tr class="bgtot">
					<td colspan="14" class="lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
					<td colspan="2" class="lbtot rjt">Horas apontadas<br/>'.$nTotalReal.'</td>
				</tr>';
			}

			$cReportPDF .= '
	  			</tbody> 
			</table>';

			$cRptLegenda .= '
			<div class="new-page"></div>
			<div class="row cjt">
				<label class="lbtit">Legendas</label>
			</div>
			<table class="table table-striped table-rpt" cellpadding="0" cellspacing="0" width="100%">
	  			<thead>
					<tr class="bgtot">
						<th width="10%" class="ljt lbtit" >TIPO</th>
						<th width="10%" class="ljt lbtit" >SIGLA</th>
						<th width="80%" colspan="6" class="ljt lbtit" >DESCRIÇÃO</th>
					</tr>
				</thead>
	  			<tbody>
					<tr>
						<td width="10%" class="ljt">AGENDA</td>
						<td width="10%" class="ljt">B</td>
						<td width="80%" colspan="6" class="ljt" >Data de início estimado não informado</td>
					</tr>
					<tr style="background-color: #EEEEEE !important;">
						<td width="10%" class="ljt">AGENDA</td>
						<td width="10%" class="ljt">N</td>
						<td width="80%" colspan="6" class="ljt">Iniciado depois do agendado</td>
					</tr>
					<tr>
						<td width="10%" class="ljt">AGENDA</td>
						<td width="10%" class="ljt">P</td>
						<td width="80%" colspan="6" class="ljt">Iniciado antes do agendado</td>
					</tr>
					<tr style="background-color: #EEEEEE !important;">
						<td width="10%" class="ljt">AGENDA</td>
						<td width="10%" class="ljt">S</td>
						<td width="80%" colspan="6" class="ljt">No início agendado</td>
					</tr>
					
					<tr>
						<td width="10%" class="ljt">PRAZO</td>
						<td width="10%" class="ljt">B</td>
						<td width="80%" colspan="6" class="ljt" >Data de término estimado não informado</td>
					</tr>
					<tr style="background-color: #EEEEEE !important;">
						<td width="10%" class="ljt">PRAZO</td>
						<td width="10%" class="ljt">N</td>
						<td width="80%" colspan="6" class="ljt">Fora do prazo</td>
					</tr>
					<tr>
						<td width="10%" class="ljt">PRAZO</td>
						<td width="10%" class="ljt">D</td>
						<td width="80%" colspan="6" class="ljt">Encerra HOJE</td>
					</tr>
					<tr style="background-color: #EEEEEE !important;">
						<td width="10%" class="ljt">PRAZO</td>
						<td width="10%" class="ljt">S</td>
						<td width="80%" colspan="6" class="ljt">No prazo</td>
					</tr>

					<tr style="background-color: #EEEEEE !important;">
						<td width="10%" class="ljt">TÍTULO</td>
						<td width="10%" class="ljt">EXR</td>
						<td width="80%" colspan="6" class="ljt">Índice de horas estimadas X Horas apontadas pelo responsável</td>
					</tr>
	  			</tbody> 
			</table>';

			$cReportPDF	= $cCabecalho.$cReportPDF.$cRptLegenda;

			if ( count($aTickets) > 0 ) {
				$cArqHTML = $_SERVER['DOCUMENT_ROOT'].$security->base_patch.'/src/relatorios/ticketGerencialReport_'.Date('Y-m-d_H-s-i').'_'.SEC_USER_NOME.'.html';
				$fp = fopen($cArqHTML, 'w');
				fwrite($fp, $cReportPDF);
				fclose($fp);
				$cArqHTML = $security->base_patch.'/src/relatorios/ticketGerencialReport_'.Date('Y-m-d_H-s-i').'_'.SEC_USER_NOME.'.html';
				// array_push($aReport, array("HTMLFILE"=>$cArqHTML));
				$aHTMLRet['HTMLFILE']	= $cArqHTML;
			}
			echo json_encode($aHTMLRet);

		} //if ( $post ) {
	} //if ( SESSION_EXISTS ) {
?>