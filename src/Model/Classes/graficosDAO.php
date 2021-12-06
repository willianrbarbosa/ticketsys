<?php	
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class GraficosDAO extends Security {
		 
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function getTotaisTickets($cDataDe, $cDataAte){
			$aTotaisTickets	= array();

			try{
				$this->sql = "SELECT
								tkt_id,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',
								tkt_encerrado,tkt_aprovado,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								cgt_id,cgt_descricao,

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome'
							FROM ticket
							LEFT JOIN pasta_trabalho ON
								pst_id = tkt_pst_id
								AND pst_delete = ''
							LEFT JOIN grupo_trabalho ON 
								grt_id = pst_grt_id
								ANd grt_delete = ''
							LEFT JOIN situacao_ticket ON
								stt_id = tkt_stt_id
								AND stt_delete = ''
							LEFT JOIN categoria_ticket ON
								cgt_id = tkt_cgt_id
								AND cgt_delete = ''
							LEFT JOIN ticket_usuarios AS tkt_usr_solic ON
								tkt_usr_solic.tku_tkt_id = tkt_id
								AND tkt_usr_solic.tku_tipo = 'S'
								AND tkt_usr_solic.tku_delete = ''
							LEFT JOIN usuario AS usr_solic ON
								usr_solic.user_id = tkt_usr_solic.tku_user_id
								AND usr_solic.user_delete = ''
							LEFT JOIN ticket_usuarios AS tkt_usr_resp ON
								tkt_usr_resp.tku_tkt_id = tkt_id
								AND tkt_usr_resp.tku_tipo = 'R'
								AND tkt_usr_resp.tku_delete = ''
							LEFT JOIN usuario AS usr_resp ON
								usr_resp.user_id = tkt_usr_resp.tku_user_id
								AND usr_resp.user_resp_ticket 	= 'S'
								AND usr_resp.user_delete 		= ''
							WHERE tkt_delete 			= ''
								AND tkt_stt_id 			> 1
								AND tkt_abertura_data BETWEEN ? AND ?
							ORDER BY tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $cDataDe	, PDO::PARAM_STR);
				$stmt->bindValue(2,  $cDataAte	, PDO::PARAM_STR);
				$stmt->execute();
				$aTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$aQtdeResp 		= array();
				$aQtdeSolic 	= array();
				$aQtdeCateg 	= array();
				$aQtdePasta 	= array();
				$aQtdeSituacao 	= array();

				for ($t=0; $t < count($aTickets); $t++) { 
					$nSomaPend	= ($aTickets[$t]['tkt_encerrado'].$aTickets[$t]['tkt_aprovado'] == 'NN' ? 1 : 0);
					$nSomaAprov	= ($aTickets[$t]['tkt_encerrado'].$aTickets[$t]['tkt_aprovado'] == 'NS' ? 1 : 0);
					$nSomaEnc	= ($aTickets[$t]['tkt_encerrado'] == 'S' ? 1 : 0);

					// - Pega por responsável
					$nIdxResp	= -1;
					for ($r=0; $r < count($aQtdeResp); $r++) { 
						if ( $aQtdeResp[$r]["resp_id"] == $aTickets[$t]['resp_user_id'] ) {
							$nIdxResp	= $r;
							break;
						}
					}
					if ( $nIdxResp >= 0 ) {
						$aQtdeResp[$nIdxResp]["qtde_pend"]	+= $nSomaPend;
						$aQtdeResp[$nIdxResp]["qtde_aprov"]	+= $nSomaAprov;
						$aQtdeResp[$nIdxResp]["qtde_enc"]	+= $nSomaEnc;
						$aQtdeResp[$nIdxResp]["qtde_total"]++;
					} else {
						array_push($aQtdeResp, Array(
								"resp_id"=>$aTickets[$t]['resp_user_id'],
								"resp_nome"=>$aTickets[$t]['resp_user_nome'],
								"qtde_pend"=>$nSomaPend,
								"qtde_aprov"=>$nSomaAprov,
								"qtde_enc"=>$nSomaEnc,
								"qtde_total"=>1
							)
						);
					}

					// - Pega por Solicitante
					$nIdxSol	= -1;
					for ($s=0; $s < count($aQtdeSolic); $s++) { 
						if ( $aQtdeSolic[$s]["solic_id"] == $aTickets[$t]['solic_user_id'] ) {
							$nIdxSol	= $s;
							break;
						}
					}
					if ( $nIdxSol >= 0 ) {
						$aQtdeSolic[$nIdxSol]["qtde_pend"]	+= $nSomaPend;
						$aQtdeSolic[$nIdxSol]["qtde_aprov"]	+= $nSomaAprov;
						$aQtdeSolic[$nIdxSol]["qtde_enc"]	+= $nSomaEnc;
						$aQtdeSolic[$nIdxSol]["qtde_total"]++;
					} else {
						array_push($aQtdeSolic, Array(
								"solic_id"=>$aTickets[$t]['solic_user_id'],
								"solic_nome"=>$aTickets[$t]['solic_user_nome'],
								"qtde_pend"=>$nSomaPend,
								"qtde_aprov"=>$nSomaAprov,
								"qtde_enc"=>$nSomaEnc,
								"qtde_total"=>1
							)
						);
					}

					// - Pega por Categoria
					$nIdxCat	= -1;
					for ($c=0; $c < count($aQtdeCateg); $c++) { 
						if ( $aQtdeCateg[$c]["cgt_id"] == $aTickets[$t]['cgt_id'] ) {
							$nIdxCat	= $c;
							break;
						}
					}
					if ( $nIdxCat >= 0 ) {
						$aQtdeCateg[$nIdxCat]["qtde_pend"]	+= $nSomaPend;
						$aQtdeCateg[$nIdxCat]["qtde_aprov"]	+= $nSomaAprov;
						$aQtdeCateg[$nIdxCat]["qtde_enc"]	+= $nSomaEnc;
						$aQtdeCateg[$nIdxCat]["qtde_total"]++;
					} else {
						array_push($aQtdeCateg, Array(
								"cgt_id"=>$aTickets[$t]['cgt_id'],
								"cgt_descricao"=>$aTickets[$t]['cgt_descricao'],
								"qtde_pend"=>$nSomaPend,
								"qtde_aprov"=>$nSomaAprov,
								"qtde_enc"=>$nSomaEnc,
								"qtde_total"=>1
							)
						);
					}

					// - Pega por Pasta de Trabalho
					$nIdxPasta	= -1;
					for ($p=0; $p < count($aQtdePasta); $p++) { 
						if ( $aQtdePasta[$p]["pst_id"] == $aTickets[$t]['pst_id'] ) {
							$nIdxPasta	= $p;
							break;
						}
					}
					if ( $nIdxPasta >= 0 ) {
						$aQtdePasta[$nIdxPasta]["qtde_pend"]	+= $nSomaPend;
						$aQtdePasta[$nIdxPasta]["qtde_aprov"]	+= $nSomaAprov;
						$aQtdePasta[$nIdxPasta]["qtde_enc"]		+= $nSomaEnc;
						$aQtdePasta[$nIdxPasta]["qtde_total"]++;
					} else {
						array_push($aQtdePasta, Array(
								"pst_id"=>$aTickets[$t]['pst_id'],
								"pst_descricao"=>$aTickets[$t]['pst_descricao'],
								"qtde_pend"=>$nSomaPend,
								"qtde_aprov"=>$nSomaAprov,
								"qtde_enc"=>$nSomaEnc,
								"qtde_total"=>1
							)
						);
					}

					// - Pega por Situação
					$nIdxSit	= -1;
					for ($p=0; $p < count($aQtdeSituacao); $p++) { 
						if ( $aQtdeSituacao[$p]["stt_id"] == $aTickets[$t]['stt_id'] ) {
							$nIdxSit	= $p;
							break;
						}
					}
					if ( $nIdxSit >= 0 ) {
						$aQtdeSituacao[$nIdxSit]["qtde_total"]++;
					} else {
						array_push($aQtdeSituacao, Array(
								"stt_id"=>$aTickets[$t]['stt_id'],
								"stt_descricao"=>$aTickets[$t]['stt_descricao'],
								"qtde_total"=>1
							)
						);
					}

				}
				
				$aTotaisTickets["RESP"]		= $aQtdeResp;
				$aTotaisTickets["SOLIC"]	= $aQtdeSolic;
				$aTotaisTickets["CATEG"]	= $aQtdeCateg;
				$aTotaisTickets["PASTA"]	= $aQtdePasta;
				$aTotaisTickets["SITUACAO"]	= $aQtdeSituacao;
									
				return $aTotaisTickets;

			}catch ( PDOException $ex ){ 
				return false;
			}
		}

		public function getTotaisHorasTickets($cDataDe, $cDataAte){
			$aTotalHorasTickets	= array();

			try{
				$this->sql = "SELECT
								tkt_id,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',
								tkt_encerrado,tkt_aprovado,

								tkp_data,tkp_user_id,tkp_horas_total,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								cgt_id,cgt_descricao,

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome'
							FROM ticket_apontamentos
							INNER JOIN ticket ON
								tkt_id = tkp_tkt_id
								AND tkt_delete 		= ''
							LEFT JOIN pasta_trabalho ON
								pst_id = tkt_pst_id
								AND pst_delete = ''
							LEFT JOIN grupo_trabalho ON 
								grt_id = pst_grt_id
								ANd grt_delete = ''
							LEFT JOIN situacao_ticket ON
								stt_id = tkt_stt_id
								AND stt_delete = ''
							LEFT JOIN categoria_ticket ON
								cgt_id = tkt_cgt_id
								AND cgt_delete = ''
							LEFT JOIN ticket_usuarios AS tkt_usr_solic ON
								tkt_usr_solic.tku_tkt_id = tkt_id
								AND tkt_usr_solic.tku_tipo = 'S'
								AND tkt_usr_solic.tku_delete = ''
							LEFT JOIN usuario AS usr_solic ON
								usr_solic.user_id = tkt_usr_solic.tku_user_id
								AND usr_solic.user_delete = ''
							LEFT JOIN usuario AS usr_resp ON
								usr_resp.user_id = tkp_user_id
								AND usr_resp.user_resp_ticket 	= 'S'
								AND usr_resp.user_delete 		= ''
							WHERE tkp_delete 			= ''
								AND tkp_data BETWEEN ? AND ?
							ORDER BY tkp_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $cDataDe	, PDO::PARAM_STR);
				$stmt->bindValue(2,  $cDataAte	, PDO::PARAM_STR);
				$stmt->execute();
				$aTicketApontamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$aTotHorasResp 			= array();
				$aTotHorasSolic 		= array();
				$aTotHorasCateg 		= array();
				$aTotHorasPasta 		= array();
				$aTotHorasSit 			= array();
				$aTotHorasEsfoXRealResp	= array();
				$aTotHorasRespDIA		= array();
				
				for ($t=0; $t < count($aTicketApontamentos); $t++) { 

					$nTotalReal 		= $aTicketApontamentos[$t]['tkp_horas_total'];
					$nSomaPend			= ($aTicketApontamentos[$t]['tkt_encerrado'] == 'N' ? $nTotalReal : 0);
					$nSomaEnc			= ($aTicketApontamentos[$t]['tkt_encerrado'] == 'S' ? $nTotalReal : 0);

					// - Pega por responsável
					$nIdxResp	= -1;
					for ($r=0; $r < count($aTotHorasResp); $r++) { 
						if ( $aTotHorasResp[$r]["resp_id"] == $aTicketApontamentos[$t]['resp_user_id'] ) {
							$nIdxResp	= $r;
							break;
						}
					}
					if ( $nIdxResp >= 0 ) {
						$aTotHorasResp[$nIdxResp]["tot_hora_pend"]	= round($aTotHorasResp[$nIdxResp]["tot_hora_pend"] + $nSomaPend, 2);
						$aTotHorasResp[$nIdxResp]["tot_hora_enc"]	= round($aTotHorasResp[$nIdxResp]["tot_hora_enc"] + $nSomaEnc, 2);
						$aTotHorasResp[$nIdxResp]["tot_hora_total"]	= round($aTotHorasResp[$nIdxResp]["tot_hora_total"] + $nTotalReal, 2);
					} else {
						array_push($aTotHorasResp, Array(
								"resp_id"=>$aTicketApontamentos[$t]['resp_user_id'],
								"resp_nome"=>$aTicketApontamentos[$t]['resp_user_nome'],
								"tot_hora_pend"=>round($nSomaPend,2),
								"tot_hora_enc"=>round($nSomaEnc,2),
								"tot_hora_total"=>round($nTotalReal,2)
							)
						);
					}

					// - Pega por Solicitante
					$nIdxSol	= -1;
					for ($s=0; $s < count($aTotHorasSolic); $s++) { 
						if ( $aTotHorasSolic[$s]["solic_id"] == $aTicketApontamentos[$t]['solic_user_id'] ) {
							$nIdxSol	= $s;
							break;
						}
					}
					if ( $nIdxSol >= 0 ) {
						$aTotHorasSolic[$nIdxSol]["tot_hora_pend"]	= round($aTotHorasSolic[$nIdxSol]["tot_hora_pend"] + $nSomaPend, 2);
						$aTotHorasSolic[$nIdxSol]["tot_hora_enc"]	= round($aTotHorasSolic[$nIdxSol]["tot_hora_enc"] + $nSomaEnc, 2);
						$aTotHorasSolic[$nIdxSol]["tot_hora_total"]	= round($aTotHorasSolic[$nIdxSol]["tot_hora_total"] + $nTotalReal, 2);
					} else {
						array_push($aTotHorasSolic, Array(
								"solic_id"=>$aTicketApontamentos[$t]['solic_user_id'],
								"solic_nome"=>$aTicketApontamentos[$t]['solic_user_nome'],
								"tot_hora_pend"=>round($nSomaPend,2),
								"tot_hora_enc"=>round($nSomaEnc,2),
								"tot_hora_total"=>round($nTotalReal,2)
							)
						);
					}

					// - Pega por Categoria
					$nIdxCat	= -1;
					for ($c=0; $c < count($aTotHorasCateg); $c++) { 
						if ( $aTotHorasCateg[$c]["cgt_id"] == $aTicketApontamentos[$t]['cgt_id'] ) {
							$nIdxCat	= $c;
							break;
						}
					}
					if ( $nIdxCat >= 0 ) {
						$aTotHorasCateg[$nIdxCat]["tot_hora_pend"]	= round($aTotHorasCateg[$nIdxCat]["tot_hora_pend"] + $nSomaPend, 2);
						$aTotHorasCateg[$nIdxCat]["tot_hora_enc"]	= round($aTotHorasCateg[$nIdxCat]["tot_hora_enc"] + $nSomaEnc, 2);
						$aTotHorasCateg[$nIdxCat]["tot_hora_total"]	= round($aTotHorasCateg[$nIdxCat]["tot_hora_total"] + $nTotalReal, 2);
					} else {
						array_push($aTotHorasCateg, Array(
								"cgt_id"=>$aTicketApontamentos[$t]['cgt_id'],
								"cgt_descricao"=>$aTicketApontamentos[$t]['cgt_descricao'],
								"tot_hora_pend"=>round($nSomaPend,2),
								"tot_hora_enc"=>round($nSomaEnc,2),
								"tot_hora_total"=>round($nTotalReal,2)
							)
						);
					}

					// - Pega por Pasta de Trabalho
					$nIdxPasta	= -1;
					for ($p=0; $p < count($aTotHorasPasta); $p++) { 
						if ( $aTotHorasPasta[$p]["pst_id"] == $aTicketApontamentos[$t]['pst_id'] ) {
							$nIdxPasta	= $p;
							break;
						}
					}
					if ( $nIdxPasta >= 0 ) {
						$aTotHorasPasta[$nIdxPasta]["tot_hora_pend"]	= round($aTotHorasPasta[$nIdxPasta]["tot_hora_pend"] + $nSomaPend, 2);
						$aTotHorasPasta[$nIdxPasta]["tot_hora_enc"]		= round($aTotHorasPasta[$nIdxPasta]["tot_hora_enc"] + $nSomaEnc, 2);
						$aTotHorasPasta[$nIdxPasta]["tot_hora_total"]	= round($aTotHorasPasta[$nIdxPasta]["tot_hora_total"] + $nTotalReal, 2);
					} else {
						array_push($aTotHorasPasta, Array(
								"pst_id"=>$aTicketApontamentos[$t]['pst_id'],
								"pst_descricao"=>$aTicketApontamentos[$t]['pst_descricao'],
								"tot_hora_pend"=>round($nSomaPend,2),
								"tot_hora_enc"=>round($nSomaEnc,2),
								"tot_hora_total"=>round($nTotalReal,2)
							)
						);
					}

					// - Pega por Situação
					$nIdxSit	= -1;
					for ($st=0; $st < count($aTotHorasSit); $st++) { 
						if ( $aTotHorasSit[$st]["stt_id"] == $aTicketApontamentos[$t]['stt_id'] ) {
							$nIdxSit	= $st;
							break;
						}
					}
					if ( $nIdxSit >= 0 ) {
						$aTotHorasSit[$nIdxSit]["tot_hora_total"]	= round($aTotHorasSit[$nIdxSit]["tot_hora_total"] + $nTotalReal, 2);
					} else {
						array_push($aTotHorasSit, Array(
								"stt_id"=>$aTicketApontamentos[$t]['stt_id'],
								"stt_descricao"=>$aTicketApontamentos[$t]['stt_descricao'],
								"tot_hora_total"=>round($nTotalReal,2)
							)
						);
					}
				}

				// - Aqui vai converter os números em horas
				for ($r=0; $r < count($aTotHorasResp); $r++) { 
					// echo 'RESP.: '.$aTotHorasResp[$r]['resp_nome'].
					// 	' <br/>PEND.: '.$aTotHorasResp[$r]['tot_hora_pend'].
					// 	' <br/>ENC.: '.$aTotHorasResp[$r]['tot_hora_enc'].
					// 	' <br/>TOT.: '.$aTotHorasResp[$r]['tot_hora_total'].'<hr/>';
					if ( $aTotHorasResp[$r]['tot_hora_pend'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasResp[$r]['tot_hora_pend']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasResp[$r]['tot_hora_pend']		= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}
					
					if ( $aTotHorasResp[$r]['tot_hora_enc'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasResp[$r]['tot_hora_enc']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasResp[$r]['tot_hora_enc']		= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}
					
					if ( $aTotHorasResp[$r]['tot_hora_total'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasResp[$r]['tot_hora_total']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasResp[$r]['tot_hora_total']	= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}
				}
				for ($s=0; $s < count($aTotHorasSolic); $s++) { 
					if ( $aTotHorasSolic[$s]['tot_hora_pend'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasSolic[$s]['tot_hora_pend']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasSolic[$s]['tot_hora_pend']	= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}

					if ( $aTotHorasSolic[$s]['tot_hora_enc'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasSolic[$s]['tot_hora_enc']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasSolic[$s]['tot_hora_enc']		= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}

					if ( $aTotHorasSolic[$s]['tot_hora_total'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasSolic[$s]['tot_hora_total']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasSolic[$s]['tot_hora_total']	= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}
				}
				for ($c=0; $c < count($aTotHorasCateg); $c++) { 
					if ( $aTotHorasCateg[$c]['tot_hora_pend'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasCateg[$c]['tot_hora_pend']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasCateg[$c]['tot_hora_pend']	= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}

					if ( $aTotHorasCateg[$c]['tot_hora_enc'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasCateg[$c]['tot_hora_enc']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasCateg[$c]['tot_hora_enc']		= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}

					if ( $aTotHorasCateg[$c]['tot_hora_total'] > 0 ) {	
						$aTempo 								= explode('.', $aTotHorasCateg[$c]['tot_hora_total']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasCateg[$c]['tot_hora_total']	= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}
				}
				for ($p=0; $p < count($aTotHorasPasta); $p++) { 
					if ( $aTotHorasPasta[$p]['tot_hora_pend'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasPasta[$p]['tot_hora_pend']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasPasta[$p]['tot_hora_pend']	= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}

					if ( $aTotHorasPasta[$p]['tot_hora_enc'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasPasta[$p]['tot_hora_enc']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasPasta[$p]['tot_hora_enc']		= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}

					if ( $aTotHorasPasta[$p]['tot_hora_enc'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasPasta[$p]['tot_hora_total']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasPasta[$p]['tot_hora_total']	= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}
				}
				for ($st=0; $st < count($aTotHorasSit); $st++) {
					if ( $aTotHorasSit[$st]['tot_hora_total'] > 0 ) {
						$aTempo 								= explode('.', $aTotHorasSit[$st]['tot_hora_total']);
						$nHora									= $aTempo[0];
						$nMinuto								= round((isset($aTempo[1]) ? ((str_pad($aTempo[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0),2);
						$aMinutos								= explode('.', $nMinuto);
						$nMinuto								= str_pad($aMinutos[0], 2, '0', STR_PAD_LEFT);
						$nSegundo								= str_pad((isset($aMinutos[1]) ? ((str_pad($aMinutos[1], 2, '0', STR_PAD_RIGHT)/100)*60) : 0), 2, '0', STR_PAD_LEFT);
						$aTotHorasSit[$st]['tot_hora_total']	= round($nHora.'.'.$nMinuto,2);//.','.$nSegundo;
					}
				}
				
				$aTotalHorasTickets["RESP"]			= $aTotHorasResp;
				$aTotalHorasTickets["SOLIC"]		= $aTotHorasSolic;
				$aTotalHorasTickets["CATEG"]		= $aTotHorasCateg;
				$aTotalHorasTickets["PASTA"]		= $aTotHorasPasta;
				$aTotalHorasTickets["SITUACAO"]		= $aTotHorasSit;

				
				$this->sql = "SELECT
								tkp_data,tkp_user_id,
								SUM(tkp_horas_total) AS TOT_HORA,
								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome'
							FROM ticket_apontamentos
							INNER JOIN ticket ON
								tkt_id = tkp_tkt_id
								AND tkt_delete 		= ''
							INNER JOIN usuario AS usr_resp ON
								usr_resp.user_id = tkp_user_id
								AND usr_resp.user_resp_ticket 	= 'S'
								AND usr_resp.user_delete 		= ''
							WHERE tkp_delete 			= ''
								AND tkp_data BETWEEN ? AND ?
							GROUP BY tkp_data,tkp_user_id
							ORDER BY tkp_data,tkp_user_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $cDataDe						, PDO::PARAM_STR);
				$stmt->bindValue(2,  $cDataAte						, PDO::PARAM_STR);
				$stmt->execute();
				$aTotHorasResp = $stmt->fetchAll(PDO::FETCH_ASSOC);

				for ($h=0; $h < count($aTotHorasResp); $h++) { 						
					$nIdxDia  = -1;
					for ($d=0; $d < count($aTotHorasRespDIA); $d++) { 
						if ( $aTotHorasRespDIA[$d]["dia"] == Date('d/m/Y', strtotime($aTotHorasResp[$h]['tkp_data'])) ) {
							$nIdxDia	= $d;
							break;
						}
					}

					$aTempo 		= explode('.', $aTotHorasResp[$h]['TOT_HORA']);
					$nHora			= $aTempo[0];
					$nMinuto		= round(($aTempo[1]/100)*60, 0);
					$nTotalReal 	= str_pad($nHora, 2, '0', STR_PAD_LEFT).'.'.str_pad($nMinuto, 2, '0', STR_PAD_LEFT);

					if ( $nIdxDia >= 0 ) {
						$aTotHorasRespDIA[$nIdxDia]["resp_".$aTotHorasResp[$h]['resp_user_id']]	= round($nTotalReal,2);
					} else {
						array_push($aTotHorasRespDIA, Array(
								"dia"=>Date('d/m/Y', strtotime($aTotHorasResp[$h]['tkp_data'])),
								"resp_".$aTotHorasResp[$h]['resp_user_id']=>round($nTotalReal,2)
							)
						);
					}
				}
				
				$aTotalHorasTickets["HORAS_DIA"]		= $aTotHorasRespDIA;

				$this->sql = "SELECT                                 
								user_id,user_nome
							FROM usuario
							WHERE user_resp_ticket 	= 'S'
								AND user_delete 	= ''
							ORDER BY user_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $cDataDe	, PDO::PARAM_STR);
				$stmt->bindValue(2,  $cDataAte	, PDO::PARAM_STR);
				$stmt->execute();
				$aUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$aUsuariosResp = array();
				for ($u=0; $u < count($aUsers); $u++) { 	
					array_push($aUsuariosResp, array("valueField"=>'resp_'.$aUsers[$u]['user_id'],"name"=>$aUsers[$u]['user_nome']));
				}

				$aTotalHorasTickets["USERS_RESP"]	= $aUsuariosResp;


				for ($r=0; $r < count($aUsers); $r++) { 
					
					$this->sql = "SELECT SUM(tkt_total_hora_estim) AS TOT_ESFORCO
								FROM ticket
								INNER JOIN ticket_usuarios ON
									tku_tkt_id		= tkt_id
									AND tku_tipo 	= 'R'
									AND tku_delete 	= ''
								WHERE tkt_delete = ''
									AND tkt_stt_id 			> 1
									AND tku_user_id 		= ?
									AND tkt_data_ini_estim >= ?
									AND tkt_data_fim_estim <= ?
								GROUP BY tku_user_id
								ORDER BY tku_user_id";
					$stmt = $this->conex->prepare($this->sql);
					$stmt->bindValue(1,  $aUsers[$r]['user_id']			, PDO::PARAM_INT);
					$stmt->bindValue(2,  $cDataDe						, PDO::PARAM_STR);
					$stmt->bindValue(3,  $cDataAte						, PDO::PARAM_STR);
					$stmt->execute();
					$aTotHorasEsforcoResp = $stmt->fetch(PDO::FETCH_ASSOC);

					
					$this->sql = "SELECT SUM(tkp_horas_total) AS TOT_HORA
								FROM ticket_apontamentos
								WHERE tkp_delete 		= ''
									AND tkp_user_id 	= ?
									AND tkp_data BETWEEN ? AND ?
								GROUP BY tkp_user_id
								ORDER BY tkp_user_id";
					$stmt = $this->conex->prepare($this->sql);
					$stmt->bindValue(1,  $aUsers[$r]['user_id']			, PDO::PARAM_INT);
					$stmt->bindValue(2,  $cDataDe						, PDO::PARAM_STR);
					$stmt->bindValue(3,  $cDataAte						, PDO::PARAM_STR);
					$stmt->execute();
					$aTotHorasRealResp = $stmt->fetch(PDO::FETCH_ASSOC);

					if ( !Empty($aTotHorasEsforcoResp) ) {
						$aTempo 		= explode('.', $aTotHorasEsforcoResp['TOT_ESFORCO']);
						$nHora			= $aTempo[0];
						$nMinuto		= round(($aTempo[1]/100)*60, 0);
						$cTotalEsforco 	= str_pad($nHora, 2, '0', STR_PAD_LEFT).'.'.str_pad($nMinuto, 2, '0', STR_PAD_LEFT);
					} else {
						$cTotalEsforco 	= 0;
					}
					if ( !Empty($aTotHorasRealResp) ) {
						$aTempo 		= explode('.', $aTotHorasRealResp['TOT_HORA']);
						$nHora			= $aTempo[0];
						$nMinuto		= round(($aTempo[1]/100)*60, 0);
						$nTotalReal 	= str_pad($nHora, 2, '0', STR_PAD_LEFT).'.'.str_pad($nMinuto, 2, '0', STR_PAD_LEFT);
					} else {
						$nTotalReal 	= 0;
					}

					if ( ( !Empty($aTotHorasRealResp) ) AND ( !Empty($aTotHorasEsforcoResp) ) ) {
						$nTotalEXR 		= (!Empty($aTotHorasRealResp) ? (($aTotHorasRealResp['TOT_HORA']*100) / $aTotHorasEsforcoResp['TOT_ESFORCO']) : 0);
					} else {
						$nTotalEXR 		= 0;
					}

					array_push($aTotHorasEsfoXRealResp, Array(
							"resp_id"=>$aUsers[$r]['user_id'],
							"resp_nome"=>$aUsers[$r]['user_nome'],
							"TOT_ESFORCO"=>round($cTotalEsforco,2),
							"TOT_HORA"=>round($nTotalReal,2),
							"EXR"=>round($nTotalEXR, 2)
						)
					);
				}
				
				$aTotalHorasTickets["EXR"]	= $aTotHorasEsfoXRealResp;
									
				return $aTotalHorasTickets;

			}catch ( PDOException $ex ){ 
				return false;
			}
		}
		
	}
?>
