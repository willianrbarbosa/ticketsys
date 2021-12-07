<?php 
	session_start();

	ini_set('max_execution_time', 2400);
	set_time_limit(2400);
	
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);

	require_once('class/security.class.php');
	require_once('class/TicketDAO.class.php');
	require_once('class/TicketApontamentosDAO.class.php');
	require_once('class/perfilAcessoRotinaDAO.class.php');
	require_once('session_vars.php');

	$security 				= new Security();
	$TicketDAO 				= new TicketDAO();
	$TicketApontamentosDAO 	= new TicketApontamentosDAO();
	$perfilAcessoRotinaDAO 	= New perfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('ticket_rel_desemp', SEC_USER_PFA_ID);

		if ( $aUserRotina == false ) {
			echo json_encode(array('error' => 'Usuário sem acesso a essa rotina.'));
			exit;
		}
		
		$post = file_get_contents("php://input");

		if ( $post ) {
			$postData 		= json_decode($post);


			$cDataDe		= (!Empty($postData->tkt_rel_desemp_data_de) ? implode("-", array_reverse(explode("/", $postData->tkt_rel_desemp_data_de))) : Date('Y-m-d'));
			$cDataAte		= (!Empty($postData->tkt_rel_desemp_data_ate) ? implode("-", array_reverse(explode("/", $postData->tkt_rel_desemp_data_ate))) : Date('Y-m-d'));
			
			$nTipoRel		= (isset($postData->tipo_rel) ? $postData->tipo_rel : null);			
			// nTipoRel: 1 => Assertividade Prazo por Responsável
			// nTipoRel: 2 => Assertividade Prazo por Dia
			// nTipoRel: 3 => Índice EXR por Responsável
			// nTipoRel: 4 => Índice EXR por Dia

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
			$cWhere 	.= " AND tkt_encerrado = 'S' "; //Apenas tickets encerrados
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
				$cWhere 	.= " AND usr_resp.user_id = ".$nRespID;
			}

			$cOrderby 	= "";
			
			$cCabecalho = '
			<table class="table table-striped table-rpt" cellpadding="0" cellspacing="0" width="100%">
	  			<thead class="thead">';

			switch ($nTipoRel) {
				case 1: //1 => Assertividade Prazo por Responsável
					$cAuxTotaliza	= 'Responsável';
					$cWhere		= " AND tkt_data_ini_estim >= '".$cDataDe."' AND tkt_data_fim_estim <= '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " resp_user_nome,tkt_data_ini_estim DESC,tkt_data_fim_estim DESC ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap">Prioridade</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">Início Estimado</th>
						<th class="lbtit text-nowrap">Início Real</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap">Fim estimado</th>
						<th class="lbtit text-nowrap">Fim Real</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap cjt">Assertividade</th>
					<tr/>';
					break;
				case 2: //2 => Assertividade Prazo por Dia
					$cAuxTotaliza	= 'Dia';
					$cWhere		= " AND tkt_data_ini_estim >= '".$cDataDe."' AND tkt_data_fim_estim <= '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " tkt_encerrado_data DESC,tkt_data_ini_estim DESC,tkt_data_fim_estim DESC ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap">Início Estimado</th>
						<th class="lbtit text-nowrap">Início Real</th>
						<th class="lbtit text-nowrap cjt">Agenda</th>
						<th class="lbtit text-nowrap">Fim estimado</th>
						<th class="lbtit text-nowrap">Fim Real</th>
						<th class="lbtit text-nowrap cjt">Prazo</th>
						<th class="lbtit text-nowrap cjt">Assertividade</th>
					<tr/>';
					break;
				case 3: //3 => Índice EXR por Responsável
					$cAuxTotaliza	= 'Responsável';
					$cWhere		= " AND tkt_encerrado_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " resp_user_nome,tkt_encerrado_data DESC,tkt_data_ini_estim,tkt_data_fim_estim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap rjt">Horas Estimadas</th>
						<th class="lbtit text-nowrap rjt">Apontam. Real</th>
						<th class="lbtit text-nowrap rjt">% EXR</th>
					<tr/>';
					break;
				case 4: //4 => Índice EXR por Dia
					$cAuxTotaliza	= 'Dia';
					$cWhere		= " AND tkt_encerrado_data BETWEEN '".$cDataDe."' AND '".$cDataAte."' ".$cWhere;
					$cOrderby 	= " tkt_encerrado_data DESC,tkt_data_ini_estim,tkt_data_fim_estim ";
					$cCabecalho	.= '
					<tr class="bgtot">
						<th class="lbtit text-nowrap">Nº.</th>
						<th class="lbtit text-nowrap">Pasta</th>
						<th class="lbtit text-nowrap">Título</th>
						<th class="lbtit text-nowrap">Data Abertura</th>
						<th class="lbtit text-nowrap">Situação</th>
						<th class="lbtit text-nowrap">Prioridade</th>
						<th class="lbtit text-nowrap">Solic./Resp.</th>
						<th class="lbtit text-nowrap">Encerrado</th>
						<th class="lbtit text-nowrap rjt">Horas Estimadas</th>
						<th class="lbtit text-nowrap rjt">Apontam. Real</th>
						<th class="lbtit text-nowrap rjt">% EXR</th>
					<tr/>';
					break;
			}

			$aTickets 	= $TicketDAO->buscaByCondicao($cOrderby, $cWhere, 'all');
			
			$nSumGroup		= 0;
			$cKeyGroup		= null;

			$nNoPrazo		= 0;
			$nForaPrazo		= 0;
			$nPontosPrazo	= 0;

			$nNaAgenda		= 0;
			$nForaAgenda	= 0;
			$nPontosAgenda	= 0;

			$nTotHorasEst	= 0;
			$nTotHorasReal	= 0;
			$nTotPerEXR		= 0;

			$nPontosTicket 	= 4;

			$cCabecalho	.= '	
					</thead>
					<tbody>';
			for ($t=0; $t < count($aTickets); $t++) { 
				switch ($nTipoRel) {
					case 1: //1 => Assertividade Prazo por Responsável
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> $aTickets[$t]['resp_user_nome']) ) {
							if ( $t > 0 ) {

								$nAssertTotal 		= ($nSumGroup*$nPontosTicket);
								$nAssertGeral		= ($nPontosAgenda+$nPontosPrazo);
								$nPerAssertGeral	= round(((($nPontosAgenda+$nPontosPrazo)*100) / $nAssertTotal), 0);

								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="3" class="text-nowrap lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="text-nowrap lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
									<td colspan="2" class="text-nowrap lbtot rjt">Iniciado na Agenda<br/><span class="fs-B">'.$nNaAgenda.'</span></td>
									<td colspan="2" class="text-nowrap lbtot rjt">Iniciado fora da Agenda<br/><span class="fs-S">'.$nForaAgenda.'</span></td>
									<td colspan="2" class="text-nowrap lbtot rjt">Encerrados no Prazo<br/><span class="fs-B">'.$nNoPrazo.'</span></td>
									<td colspan="2" class="text-nowrap lbtot rjt">Encerrados fora do Prazo<br/><span class="fs-S">'.$nForaPrazo.'</span></td>
									<td colspan="2" class="text-nowrap lbtot rjt">Assertividade Geral<br/><span class="'.($nPerAssertGeral >= 75 ? 'fs-B' : 'fs-S').'">'.$nAssertGeral.'/'.$nAssertTotal.' ('.$nPerAssertGeral.'%)'.'</span></td>
								</tr>';

							}							
							$cKeyGroup		= $aTickets[$t]['resp_user_nome'];
							$nSumGroup		= 0;
							$nNoPrazo		= 0;
							$nForaPrazo		= 0;
							$nPontosPrazo	= 0;
							$nNaAgenda		= 0;
							$nForaAgenda	= 0;
							$nPontosAgenda	= 0;
						}

						$nSumPtsAgenda		= 0;
						$nSumPtsPrazo		= 0;
						if ( $aTickets[$t]['AGENDA'] == 'P' ) {
							$nNaAgenda ++;
							$nSumPtsAgenda = 1;
						} elseif ( $aTickets[$t]['AGENDA'] == 'S' ) {
							$nNaAgenda ++;
							$nSumPtsAgenda = 2;
						} else {
							$nForaAgenda ++;
						}
						if ( $aTickets[$t]['PRAZO'] == 'S' ){
							$nNoPrazo ++;
							$nSumPtsPrazo = 2;
						} else {
							$nForaPrazo ++;
						}

						$nAssertTicket		= ($nSumPtsAgenda+$nSumPtsPrazo);
						$nPerAssertTicket	= round(((($nSumPtsAgenda+$nSumPtsPrazo)*100) / $nPontosTicket), 0);

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>
							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">'.$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data'])).'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_data_ini_estim'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_hora_ini_estim'])).'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_data_ini_real'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_hora_ini_real'])).'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_data_fim_estim'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_hora_fim_estim'])).'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_data_fim_real'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_hora_fim_real'])).'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap cjt">'.$nAssertTicket.'/'.$nPontosTicket.' ('.$nPerAssertTicket.'%)'.'</td>
						</tr>';

						$nSumGroup ++;
						$nPontosAgenda += $nSumPtsAgenda;
						$nPontosPrazo += $nSumPtsPrazo;
						break;
					case 2: //2 => Assertividade Prazo por Dia
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> Date('d/m/Y', strtotime($aTickets[$t]['tkt_encerrado_data']))) ) {
							if ( $t > 0 ) {

								$nAssertTotal 		= ($nSumGroup*$nPontosTicket);
								$nAssertGeral		= ($nPontosAgenda+$nPontosPrazo);
								$nPerAssertGeral	= round(((($nPontosAgenda+$nPontosPrazo)*100) / $nAssertTotal), 0);

								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="3" class="text-nowrap lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
									<td colspan="2" class="text-nowrap lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
									<td colspan="2" class="text-nowrap lbtot rjt">Iniciado na Agenda<br/><span class="fs-B">'.$nNaAgenda.'</span></td>
									<td colspan="2" class="text-nowrap lbtot rjt">Iniciado fora da Agenda<br/><span class="fs-S">'.$nForaAgenda.'</span></td>
									<td colspan="2" class="text-nowrap lbtot rjt">Encerrados no Prazo<br/><span class="fs-B">'.$nNoPrazo.'</span></td>
									<td colspan="2" class="text-nowrap lbtot rjt">Encerrados fora do Prazo<br/><span class="fs-S">'.$nForaPrazo.'</span></td>
									<td colspan="2" class="text-nowrap lbtot rjt">Assertividade Geral<br/><span class="'.($nPerAssertGeral >= 75 ? 'fs-B' : 'fs-S').'">'.$nAssertGeral.'/'.$nAssertTotal.' ('.$nPerAssertGeral.'%)'.'</span></td>
								</tr>';

							}							
							$cKeyGroup		= Date('d/m/Y', strtotime($aTickets[$t]['tkt_encerrado_data']));
							$nSumGroup		= 0;
							$nNoPrazo		= 0;
							$nForaPrazo		= 0;
							$nPontosPrazo	= 0;
							$nNaAgenda		= 0;
							$nForaAgenda	= 0;
							$nPontosAgenda	= 0;
						}

						$nSumPtsAgenda		= 0;
						$nSumPtsPrazo		= 0;
						if ( $aTickets[$t]['AGENDA'] == 'P' ) {
							$nNaAgenda ++;
							$nSumPtsAgenda = 1;
						} elseif ( $aTickets[$t]['AGENDA'] == 'S' ) {
							$nNaAgenda ++;
							$nSumPtsAgenda = 2;
						} else {
							$nForaAgenda ++;
						}
						if ( $aTickets[$t]['PRAZO'] == 'S' ){
							$nNoPrazo ++;
							$nSumPtsPrazo = 2;
						} else {
							$nForaPrazo ++;
						}

						$nAssertTicket		= ($nSumPtsAgenda+$nSumPtsPrazo);
						$nPerAssertTicket	= round(((($nSumPtsAgenda+$nSumPtsPrazo)*100) / $nPontosTicket), 0);

						$cReportPDF .= '
						<tr '.($t % 2 ? 'style="background-color: #E7E7E7 !important;"' : '').'>
							<td><a href="#/detalheticket/'.$aTickets[$t]['tkt_id'].'" target="_Blank">'.$aTickets[$t]['tkt_id'].'</a></td>
							<td class="text-nowrap">'.$aTickets[$t]['grt_descricao'].' ><br/>'.$aTickets[$t]['pst_descricao'].'</td>
							<td>'.$aTickets[$t]['tkt_titulo'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_abertura_data'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_abertura_data'])).'</td>
							<td class="text-nowrap">S: '.$aTickets[$t]['solic_user_nome'].'<br/>R: '.$aTickets[$t]['resp_user_nome'].'</td>
							<td class="text-nowrap">'.$aTickets[$t]['stt_descricao'].'</td>
							<td class="text-nowrap cjt"><label class="w100-rpt lbbold" style="color: #FFF; padding: 5px !important; background-color: '.$aTickets[$t]['prt_cor'].' !important;">'.$aTickets[$t]['prt_descricao'].'</label></td>
							<td class="text-nowrap">'.$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data'])).'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_data_ini_estim'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_hora_ini_estim'])).'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_data_ini_real'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_hora_ini_real'])).'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['AGENDA'].'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_data_fim_estim'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_hora_fim_estim'])).'</td>
							<td class="text-nowrap">'.Date('d/m/Y', strtotime($aTickets[$t]['tkt_data_fim_real'])).'<br/>ás '.Date('H:i', strtotime($aTickets[$t]['tkt_hora_fim_real'])).'</td>
							<td class="text-nowrap cjt">'.$aTickets[$t]['PRAZO'].'</td>
							<td class="text-nowrap cjt">'.$nAssertTicket.'/'.$nPontosTicket.' ('.$nPerAssertTicket.'%)'.'</td>
						</tr>';

						$nSumGroup ++;
						$nPontosAgenda += $nSumPtsAgenda;
						$nPontosPrazo += $nSumPtsPrazo;
						break;
					case 3: //3 => Índice EXR por Responsável
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> $aTickets[$t]['resp_user_nome']) ) {
							if ( $t > 0 ) {
								if ( $nTotHorasEst > 0 ) {
									$nTotPerEXR 	= round(($nTotHorasReal * 100) / $nTotHorasEst,2);
								} else {
									$nTotPerEXR 	= 0;
								}

								$aTempoE 		= explode('.', number_format($nTotHorasEst,2));
								$nHoraE			= $aTempoE[0];
								$nMinutoE		= round((str_pad($aTempoE[1], 2, '0', STR_PAD_RIGHT)/100)*60,2);
								$aMinutosE		= explode('.', $nMinutoE);
								$nMinutoE		= $aMinutosE[0];
								$nSegundoE		= round((isset($aMinutosE[1]) ? ((str_pad($aMinutosE[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$nTotalEst 		= str_pad($nHoraE, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinutoE, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundoE, 2, '0', STR_PAD_LEFT);

								$aTempoR 		= explode('.', number_format($nTotHorasReal,2));
								$nHoraR			= $aTempoR[0];
								$nMinutoR		= round((str_pad($aTempoR[1], 2, '0', STR_PAD_RIGHT)/100)*60,2);
								$aMinutosR		= explode('.', $nMinutoR);
								$nMinutoR		= $aMinutosR[0];
								$nSegundoR		= round((isset($aMinutosR[1]) ? ((str_pad($aMinutosR[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$nTotalReal 	= str_pad($nHoraR, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinutoR, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundoR, 2, '0', STR_PAD_LEFT);

								// $cReportPDF .= '
								// <tr class="bgtot">
								// 	<td colspan="5" class="text-nowrap lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
								// 	<td colspan="2" class="text-nowrap lbtot rjt">Total de horas Estimadas<br/>'.$nTotalEst.'</td>
								// 	<td colspan="2" class="text-nowrap lbtot rjt">Total de Apontamento Real<br/><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.$nTotalReal.'</span></td>
								// 	<td colspan="2" class="text-nowrap lbtot rjt">% EXR Total<br/><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.number_format($nTotPerEXR, 2, ',', '.').' %</span></td>
								// </tr>';
								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="8" class="text-nowrap lbtot rjt">'.$cKeyGroup.'</td>
									<td colspan="1" class="text-nowrap lbtot rjt">'.$nTotalEst.'</td>
									<td colspan="1" class="text-nowrap lbtot rjt"><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.$nTotalReal.'</span></td>
									<td colspan="1" class="text-nowrap lbtot rjt"><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.number_format($nTotPerEXR, 2, ',', '.').' %</span></td>
								</tr>';

							}
							$cKeyGroup		= $aTickets[$t]['resp_user_nome'];
							$nTotHorasEst	= 0;
							$nTotHorasReal	= 0;
							$nTotPerEXR		= 0;
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
							<td class="text-nowrap">'.$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data'])).'</td>
							<td class="text-nowrap rjt">'.Date('H:i:s', strtotime($aTickets[$t]['tkt_total_hora_estim_comp'])).'</td>
							<td class="text-nowrap rjt">'.Date('H:i:s', strtotime($aTickets[$t]['tkt_total_hora_real_comp'])).'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
						</tr>';

						$nTotHorasEst 	+= $aTickets[$t]['tkt_total_hora_estim'];
						$nTotHorasReal 	+= $aTickets[$t]['tkt_total_hora_real'];
						break;
					case 4: //4 => Índice EXR por Dia
						// - Aqui gera os totalizadores
						if ( ($cKeyGroup <> Date('d/m/Y', strtotime($aTickets[$t]['tkt_encerrado_data']))) ) {
							if ( $t > 0 ) {
								if ( $nTotHorasEst > 0 ) {
									$nTotPerEXR 	= round(($nTotHorasReal * 100) / $nTotHorasEst,2);
								} else {
									$nTotPerEXR 	= 0;
								}

								$aTempoE 		= explode('.', number_format($nTotHorasEst,2));
								$nHoraE			= $aTempoE[0];
								$nMinutoE		= round((str_pad($aTempoE[1], 2, '0', STR_PAD_RIGHT)/100)*60,2);
								$aMinutosE		= explode('.', $nMinutoE);
								$nMinutoE		= $aMinutosE[0];
								$nSegundoE		= round((isset($aMinutosE[1]) ? ((str_pad($aMinutosE[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$nTotalEst 		= str_pad($nHoraE, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinutoE, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundoE, 2, '0', STR_PAD_LEFT);

								$aTempoR 		= explode('.', number_format($nTotHorasReal,2));
								$nHoraR			= $aTempoR[0];
								$nMinutoR		= round((str_pad($aTempoR[1], 2, '0', STR_PAD_RIGHT)/100)*60,2);
								$aMinutosR		= explode('.', $nMinutoR);
								$nMinutoR		= $aMinutosR[0];
								$nSegundoR		= round((isset($aMinutosR[1]) ? ((str_pad($aMinutosR[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
								$nTotalReal 	= str_pad($nHoraR, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinutoR, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundoR, 2, '0', STR_PAD_LEFT);

								// $cReportPDF .= '
								// <tr class="bgtot">
								// 	<td colspan="5" class="text-nowrap lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
								// 	<td colspan="2" class="text-nowrap lbtot rjt">Total de horas Estimadas<br/>'.$nTotalEst.'</td>
								// 	<td colspan="2" class="text-nowrap lbtot rjt">Total de Apontamento Real<br/><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.$nTotalReal.'</span></td>
								// 	<td colspan="2" class="text-nowrap lbtot rjt">% EXR Total<br/><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.number_format($nTotPerEXR, 2, ',', '.').' %</span></td>
								// </tr>';
								$cReportPDF .= '
								<tr class="bgtot">
									<td colspan="8" class="text-nowrap lbtot rjt">'.$cKeyGroup.'</td>
									<td colspan="1" class="text-nowrap lbtot rjt">'.$nTotalEst.'</td>
									<td colspan="1" class="text-nowrap lbtot rjt"><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.$nTotalReal.'</span></td>
									<td colspan="1" class="text-nowrap lbtot rjt"><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.number_format($nTotPerEXR, 2, ',', '.').' %</span></td>
								</tr>';

							}
							$cKeyGroup		= Date('d/m/Y', strtotime($aTickets[$t]['tkt_encerrado_data']));
							$nTotHorasEst	= 0;
							$nTotHorasReal	= 0;
							$nTotPerEXR		= 0;
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
							<td class="text-nowrap">'.$aTickets[$t]['enc_user_nome'].'<br/>'.Date('d/m/Y H:i', strtotime($aTickets[$t]['tkt_encerrado_data'])).'</td>
							<td class="text-nowrap rjt">'.Date('H:i:s', strtotime($aTickets[$t]['tkt_total_hora_estim_comp'])).'</td>
							<td class="text-nowrap rjt">'.Date('H:i:s', strtotime($aTickets[$t]['tkt_total_hora_real_comp'])).'</td>
							<td class="text-nowrap rjt">'.number_format($aTickets[$t]['tkt_per_concluido'], 2, ',', '.').' %</td>
						</tr>';

						$nTotHorasEst 	+= $aTickets[$t]['tkt_total_hora_estim'];
						$nTotHorasReal 	+= $aTickets[$t]['tkt_total_hora_real'];
						break;
				}
			}			
			
			if ( $nTipoRel <= 2 ) {
				$nAssertTotal 		= ($nSumGroup*$nPontosTicket);
				$nAssertGeral		= ($nPontosAgenda+$nPontosPrazo);
				$nPerAssertGeral	= round(((($nPontosAgenda+$nPontosPrazo)*100) / $nAssertTotal), 0);

				$cReportPDF .= '
				<tr class="bgtot">
					<td colspan="3" class="text-nowrap lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
					<td colspan="2" class="text-nowrap lbtot rjt">Qtde. Tickets<br/>'.$nSumGroup.'</td>
					<td colspan="2" class="text-nowrap lbtot rjt">Iniciado na Agenda<br/><span class="fs-B">'.$nNaAgenda.'</span></td>
					<td colspan="2" class="text-nowrap lbtot rjt">Iniciado fora da Agenda<br/><span class="fs-S">'.$nForaAgenda.'</span></td>
					<td colspan="2" class="text-nowrap lbtot rjt">Encerrados no Prazo<br/><span class="fs-B">'.$nNoPrazo.'</span></td>
					<td colspan="2" class="text-nowrap lbtot rjt">Encerrados fora do Prazo<br/><span class="fs-S">'.$nForaPrazo.'</span></td>
					<td colspan="2" class="text-nowrap lbtot rjt">Assertividade Geral<br/><span class="'.($nPerAssertGeral >= 75 ? 'fs-B' : 'fs-S').'">'.$nAssertGeral.'/'.$nAssertTotal.' ('.$nPerAssertGeral.'%)'.'</span></td>
				</tr>';
			} else {
				if ( $nTotHorasEst > 0 ) {
					$nTotPerEXR 	= round(($nTotHorasReal * 100) / $nTotHorasEst,2);
				} else {
					$nTotPerEXR 	= 0;
				}

				$aTempoE 		= explode('.', number_format($nTotHorasEst,2));
				$nHoraE			= $aTempoE[0];
				$nMinutoE		= round((str_pad($aTempoE[1], 2, '0', STR_PAD_RIGHT)/100)*60,2);
				$aMinutosE		= explode('.', $nMinutoE);
				$nMinutoE		= $aMinutosE[0];
				$nSegundoE		= round((isset($aMinutosE[1]) ? ((str_pad($aMinutosE[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
				$nTotalEst 		= str_pad($nHoraE, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinutoE, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundoE, 2, '0', STR_PAD_LEFT);

				$aTempoR 		= explode('.', number_format($nTotHorasReal,2));
				$nHoraR			= $aTempoR[0];
				$nMinutoR		= round((str_pad($aTempoR[1], 2, '0', STR_PAD_RIGHT)/100)*60,2);
				$aMinutosR		= explode('.', $nMinutoR);
				$nMinutoR		= $aMinutosR[0];
				$nSegundoR		= round((isset($aMinutosR[1]) ? ((str_pad($aMinutosR[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
				$nTotalReal 	= str_pad($nHoraR, 2, '0', STR_PAD_LEFT).':'.str_pad($nMinutoR, 2, '0', STR_PAD_LEFT).':'.str_pad($nSegundoR, 2, '0', STR_PAD_LEFT);

				// $cReportPDF .= '
				// <tr class="bgtot">
				// 	<td colspan="5" class="text-nowrap lbtot rjt">Totalizador do '.$cAuxTotaliza.'<br/>'.$cKeyGroup.'</td>
				// 	<td colspan="2" class="text-nowrap lbtot rjt">Total de horas Estimadas<br/>'.$nTotalEst.'</td>
				// 	<td colspan="2" class="text-nowrap lbtot rjt">Total de Apontamento Real<br/><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.$nTotalReal.'</span></td>
				// 	<td colspan="2" class="text-nowrap lbtot rjt">% EXR Total<br/><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.number_format($nTotPerEXR, 2, ',', '.').' %</span></td>
				// </tr>';
				$cReportPDF .= '
				<tr class="bgtot">
					<td colspan="8" class="text-nowrap lbtot rjt">'.$cKeyGroup.'</td>
					<td colspan="1" class="text-nowrap lbtot rjt">'.$nTotalEst.'</td>
					<td colspan="1" class="text-nowrap lbtot rjt"><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.$nTotalReal.'</span></td>
					<td colspan="1" class="text-nowrap lbtot rjt"><span class="'.($nTotPerEXR > 100 ? 'fs-S' : 'fs-B').'">'.number_format($nTotPerEXR, 2, ',', '.').' %</span></td>
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
				$cArqHTML = $_SERVER['DOCUMENT_ROOT'].$security->base_patch.'/src/relatorios/ticketDesempenhoReport_'.Date('Y-m-d_H-s-i').'_'.SEC_USER_NOME.'.html';
				$fp = fopen($cArqHTML, 'w');
				fwrite($fp, $cReportPDF);
				fclose($fp);
				$cArqHTML = $security->base_patch.'/src/relatorios/ticketDesempenhoReport_'.Date('Y-m-d_H-s-i').'_'.SEC_USER_NOME.'.html';
				// array_push($aReport, array("HTMLFILE"=>$cArqHTML));
				$aHTMLRet['HTMLFILE']	= $cArqHTML;
			}
			echo json_encode($aHTMLRet);

		} //if ( $post ) {
	} //if ( SESSION_EXISTS ) {
?>