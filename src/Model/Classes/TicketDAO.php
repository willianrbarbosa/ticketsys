<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class TicketDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oTicket){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oTicket->getArrayofFields();
			array_push($asFields, 'tkt_incdate', 'tkt_upddate','tkt_delete');
			$amValues = $oTicket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('ticket',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oTicket){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket->getArrayofFields();
			array_push($asFields, 'tkt_upddate');
			$amValues = $oTicket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('tkt_id = ');
			$aUpdValues = array($oTicket->gettkt_id());

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function AlteraDataEstimada($tktID, $tktDays, $tktTime, $tktAltIni){
			try{
				$this->conex->beginTransaction();

				$this->sql = "UPDATE ticket set 
								".($tktAltIni == 'S' ? "tkt_data_ini_estim = ADDDATE( tkt_data_ini_estim, INTERVAL ".$tktDays." DAY)," : "") ."	
								tkt_data_fim_estim = ADDDATE( tkt_data_fim_estim, INTERVAL ".$tktDays." DAY)
							WHERE tkt_id 	= ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1, $tktID		, PDO::PARAM_INT);

				$stmt->execute();
			
				$this->conex->commit();	

				$aTimes = explode(':', $tktTime);
				
				$this->conex->beginTransaction();
				$this->sql = "UPDATE ticket set 
								".($tktAltIni == 'S' ? "tkt_hora_ini_estim =  TIME((ADDTIME(tkt_hora_ini_estim, ".($tktDays < 0 ? ($aTimes[0] < 0 ? (24 + $aTimes[0]) : $aTimes[0]) : $aTimes[0])."0000))%(TIME('24:00:00')))," : "") ."
								tkt_hora_fim_estim =  TIME((ADDTIME(tkt_hora_fim_estim, ".($tktDays < 0 ? ($aTimes[0] < 0 ? (24 + $aTimes[0]) : $aTimes[0]) : $aTimes[0])."0000))%(TIME('24:00:00')))
							WHERE tkt_id 	= ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1, $tktID		, PDO::PARAM_INT);
				$stmt->execute();			
				$this->conex->commit();	
				$this->conex->beginTransaction();
				$this->sql = "UPDATE ticket set 
								".($tktAltIni == 'S' ? "tkt_hora_ini_estim =  TIME((ADDTIME(tkt_hora_ini_estim, 00".$aTimes[1]."00))%(TIME('24:00:00')))," : "") ."
								tkt_hora_fim_estim =  TIME((ADDTIME(tkt_hora_fim_estim, 00".$aTimes[1]."00))%(TIME('24:00:00')))
							WHERE tkt_id 	= ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1, $tktID		, PDO::PARAM_INT);
				$stmt->execute();			
				$this->conex->commit();	
				$this->conex->beginTransaction();
				$this->sql = "UPDATE ticket set 
								".($tktAltIni == 'S' ? "tkt_hora_ini_estim =  TIME((ADDTIME(tkt_hora_ini_estim, 0000".$aTimes[2]."))%(TIME('24:00:00')))," : "") ."
								tkt_hora_fim_estim =  TIME((ADDTIME(tkt_hora_fim_estim, 0000".$aTimes[0]."))%(TIME('24:00:00')))
							WHERE tkt_id 	= ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1, $tktID		, PDO::PARAM_INT);
				$stmt->execute();			
				$this->conex->commit();	
				

				$this->conex = null;
				return true;
			}catch ( PDOException $ex ){
				$this->conex = null;
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function AtualizaSituacaoPadrao($tkt_id, $tkt_stt_id){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_stt_id');
		    $amValues = array($tkt_stt_id);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function AtualizaSituacaoAprovado($tkt_id, $tkt_stt_id, $tkt_data_fim_real, $tkt_hora_fim_real, $tkt_aprovado, $tkt_aprovado_data, $tkt_aprovado_user_id){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_stt_id','tkt_data_fim_real','tkt_hora_fim_real','tkt_aprovado','tkt_aprovado_data','tkt_aprovado_user_id');
		    $amValues = array($tkt_stt_id,$tkt_data_fim_real,$tkt_hora_fim_real,$tkt_aprovado,$tkt_aprovado_data,$tkt_aprovado_user_id);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function AtualizaSituacaoEncerrado($tkt_id, $tkt_stt_id, $tkt_encerrado, $tkt_encerrado_data, $tkt_encerrado_user_id){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_stt_id','tkt_encerrado','tkt_encerrado_data','tkt_encerrado_user_id');
		    $amValues = array($tkt_stt_id,$tkt_encerrado,$tkt_encerrado_data,$tkt_encerrado_user_id);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function reabreTicket($tkt_id, $tkt_stt_id){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_stt_id','tkt_data_fim_real','tkt_hora_fim_real','tkt_aprovado','tkt_aprovado_data','tkt_aprovado_user_id','tkt_encerrado','tkt_encerrado_data','tkt_encerrado_user_id');
		    $amValues = array($tkt_stt_id,null,null,'N',null,null,'N',null,null);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function AtualizaDataInicioReal($tkt_id, $tkt_data_ini_real, $tkt_hora_ini_real){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_data_ini_real','tkt_hora_ini_real');
		    $amValues = array($tkt_data_ini_real, $tkt_hora_ini_real);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function AtualizaDataFimReal($tkt_id, $tkt_data_fim_real, $tkt_hora_fim_real){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_data_fim_real','tkt_hora_fim_real');
		    $amValues = array($tkt_data_fim_real, $tkt_hora_fim_real);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function AtualizaEsforcoReal($tkt_id, $tkt_total_hora_real){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_total_hora_real');
		    $amValues = array($tkt_total_hora_real);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function AtualizaPerConcluido($tkt_id, $tkt_per_concluido){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_per_concluido');
		    $amValues = array($tkt_per_concluido);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function AtualizaDataInicioEsforcoReaPerConcluidol($tkt_id, $tkt_data_ini_real, $tkt_hora_ini_real, $tkt_total_hora_real, $tkt_per_concluido){
			$this->cReturnMsg 	= '';

		    $asFields = array('tkt_data_ini_real','tkt_hora_ini_real','tkt_total_hora_real','tkt_per_concluido');
		    $amValues = array($tkt_data_ini_real, $tkt_hora_ini_real, $tkt_total_hora_real, $tkt_per_concluido);
		    $aUpdKeys = array('tkt_id = ');
		    $aUpdValues = array($tkt_id);

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function Deleta($oTicket){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket->getArrayofFields();
			array_push($asFields, 'tkt_delete','tkt_deldate', 'tkt_deluser');
			$amValues = $oTicket->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tkt_id = ');
			$aUpdValues = array($oTicket->gettkt_id());

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oTicket){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket->getArrayofFields();
			array_push($asFields, 'tkt_delete','tkt_deldate', 'tkt_deluser');
			$amValues = $oTicket->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tkt_id = ');
			$aUpdValues = array($oTicket->gettkt_id());

			if ( $this->update_data('ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
								WHERE tkt_delete = '*'
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($tkt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByPst_id($tkt_pst_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_pst_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_pst_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTav_id($tkt_tav_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_tav_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_tav_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByStt_id($tkt_stt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_stt_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_stt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByAbertura_data($tkt_abertura_data, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_abertura_data = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_abertura_data	, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByAbertura_user_id($tkt_abertura_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_abertura_user_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_abertura_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByAprovado($tkt_aprovado, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_aprovado = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_aprovado	, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByAprovado_data($tkt_aprovado_data, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_aprovado_data = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_aprovado_data	, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByAprovado_user_id($tkt_aprovado_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_aprovado_user_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_aprovado_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByCgt_id($tkt_cgt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_cgt_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_cgt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByPrt_id($tkt_prt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_prt_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_prt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByOrt_id($tkt_ort_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_ort_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_ort_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByArquivado_user_id($tkt_arquivado_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND tkt_arquivado_user_id = ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_arquivado_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaTodosByUsuario($user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete = ''
									AND (tkt_abertura_user_id = ? 
										OR tkt_id IN (SELECT tku_tkt_id FROM ticket_usuarios WHERE tku_tipo IN ('S','O') AND tku_user_id = ?)
                                    )
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $user_id	, PDO::PARAM_INT);
				$stmt->bindValue(2,  $user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByPendentesResponsavel($tkt_resp_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete 					= ''
									AND tkt_encerrado 				= 'N' 
									AND tkt_usr_resp.tku_user_id 	= ? 
									".$cWhere." 
								ORDER BY prt_prioridade ASC, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkt_resp_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaTicketsKanban($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema',

                                (SELECT COUNT(tka_id) FROM ticket_arquivos WHERE tka_delete = '' AND tka_tkt_id = tkt_id) AS qtde_arquivos,
                                (SELECT COUNT(tkc_id) FROM ticket_comentarios WHERE tkc_delete = '' AND tkc_tkt_id = tkt_id) AS qtde_comentarios
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete 					= ''
									AND tkt_arquivado 				= 'N'
									AND tkt_stt_id 					> 1
									".$cWhere." 
								ORDER BY prt_prioridade, tkt_data_fim_estim, tkt_hora_fim_estim, tkt_data_ini_estim, tkt_hora_ini_estim, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaTicketsAgenda($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema',

                                (SELECT COUNT(tka_id) FROM ticket_arquivos WHERE tka_delete = '' AND tka_tkt_id = tkt_id) AS qtde_arquivos,
                                (SELECT COUNT(tkc_id) FROM ticket_comentarios WHERE tkc_delete = '' AND tkc_tkt_id = tkt_id) AS qtde_comentarios
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete 			= ''
									AND tkt_arquivado 		= 'N'
									AND tkt_data_ini_estim 	IS NOT NULL
									AND tkt_stt_id 			NOT IN (1,6,7)
									".$cWhere." 
								ORDER BY tkt_data_ini_estim, tkt_hora_ini_estim, tkt_data_fim_estim, tkt_hora_fim_estim, tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaParaTriagem($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''
								WHERE tkt_delete 					= ''
									AND tkt_encerrado 				= 'N' 
									AND tkt_stt_id 					= 1
									".$cWhere." 
								ORDER BY tkt_abertura_data";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByCondicao($cOrderby, $cWhere, $cFetch = 'all'){
			try{
				$this->sql = "SELECT
								tkt_id,tkt_pst_id,tkt_titulo,tkt_tav_id,tkt_descricao,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',tkt_abertura_user_id,tkt_stt_id,tkt_cgt_id,tkt_prt_id,
								tkt_ort_id,tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,
								tkt_total_hora_estim,SEC_TO_TIME(tkt_total_hora_estim*60*60) AS 'tkt_total_hora_estim_comp',
								tkt_total_hora_real,SEC_TO_TIME(tkt_total_hora_real*60*60) AS 'tkt_total_hora_real_comp',
								tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,
								REPLACE(tkt_incdate, ' ', 'T') as 'tkt_incdate',
								REPLACE(tkt_upddate, ' ', 'T') as 'tkt_upddate',
								REPLACE(tkt_deldate, ' ', 'T') as 'tkt_deldate',tkt_deluser,

								CASE WHEN tkt_data_ini_estim IS NOT NULL THEN 
								     CASE WHEN tkt_data_ini_real IS NOT NULL THEN 
										CASE WHEN tkt_data_ini_estim = tkt_data_ini_real THEN 
											'S' 
										ELSE 
											CASE WHEN tkt_data_ini_estim > tkt_data_ini_real THEN 'P' ELSE 'N' END 
										END 
								     ELSE 
								          'N'
								     END
								ELSE
								    'B'
								END AS AGENDA,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN 								     
									CASE WHEN tkt_data_fim_estim = Current_Date() THEN 'D' ELSE
										CASE WHEN CONCAT(tkt_data_fim_estim,' ',tkt_hora_fim_estim) < Now() THEN 'N' ELSE 'S' END
									END
								ELSE
								    'B'
								END AS PRAZO,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(Current_Date(),tkt_data_fim_estim) ELSE 0 END AS dias_atraso,
								CASE WHEN tkt_data_fim_estim IS NOT NULL THEN DATEDIFF(tkt_data_fim_real,tkt_data_fim_estim) ELSE 0 END AS dias_desvio_prazo,

								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								stt_id,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,
								tav_id,tav_descricao,
								cgt_id,cgt_descricao,
								prt_id,prt_prioridade,prt_descricao,prt_cor,
								ort_id,ort_descricao,

								usr_abert.user_id AS 'abert_user_id',usr_abert.user_nome AS 'abert_user_nome',usr_abert.user_email AS 'abert_user_email',
								usr_abert.user_photo AS 'abert_user_photo',usr_abert.user_tipo AS 'abert_user_tipo',

								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_aprov.user_photo AS 'aprov_user_photo',usr_aprov.user_tipo AS 'aprov_user_tipo',

								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_enc.user_photo AS 'enc_user_photo',usr_enc.user_tipo AS 'enc_user_tipo',

								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_arq.user_photo AS 'arq_user_photo',usr_arq.user_tipo AS 'arq_user_tipo',

								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_solic.user_photo AS 'solic_user_photo',usr_solic.user_tipo AS 'solic_user_tipo',
								tkt_usr_solic.tku_notif_email AS 'solic_tku_notif_email',tkt_usr_solic.tku_notif_sistema AS 'solic_tku_notif_sistema',

								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',
								usr_resp.user_photo AS 'resp_user_photo',usr_resp.user_tipo AS 'resp_user_tipo',
								tkt_usr_resp.tku_notif_email AS 'resp_tku_notif_email',tkt_usr_resp.tku_notif_sistema AS 'resp_tku_notif_sistema'
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
								LEFT JOIN tipo_atividade ON
									tav_id = tkt_tav_id
									AND tav_delete = ''
								LEFT JOIN categoria_ticket ON
									cgt_id = tkt_cgt_id
									AND cgt_delete = ''
								LEFT JOIN prioridade_ticket ON
									prt_id = tkt_prt_id
									AND prt_delete = ''
								LEFT JOIN origem_ticket ON
									ort_id = tkt_ort_id
									AND ort_delete = ''
								LEFT JOIN usuario AS usr_abert ON
									usr_abert.user_id = tkt_abertura_user_id
									AND usr_abert.user_delete = ''
								LEFT JOIN usuario AS usr_aprov ON
									usr_aprov.user_id = tkt_aprovado_user_id
									AND usr_aprov.user_delete = ''
								LEFT JOIN usuario AS usr_enc ON
									usr_enc.user_id = tkt_encerrado_user_id
									AND usr_enc.user_delete = ''
								LEFT JOIN usuario AS usr_arq ON
									usr_arq.user_id = tkt_arquivado_user_id
									AND usr_arq.user_delete = ''
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
									AND usr_resp.user_resp_ticket = 'S'
									AND usr_resp.user_delete = ''								
								WHERE tkt_delete = '' 
									".$cWhere."
								ORDER BY ".$cOrderby;
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				if ( $cFetch == 'all' ) { 
					$aTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} else {
					$aTickets = $stmt->fetch(PDO::FETCH_ASSOC);
				}
				$stmt->closeCursor();

				return $aTickets;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

	}
?>