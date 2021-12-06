<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class TicketApontamentosDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function Insere($oTicket_apontamentos){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oTicket_apontamentos->getArrayofFields();
			array_push($asFields, 'tkp_incdate', 'tkp_upddate','tkp_delete');
			$amValues = $oTicket_apontamentos->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('ticket_apontamentos',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oTicket_apontamentos){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_apontamentos->getArrayofFields();
			array_push($asFields, 'tkp_upddate');
			$amValues = $oTicket_apontamentos->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('tkp_id = ');
			$aUpdValues = array($oTicket_apontamentos->gettkp_id());

			if ( $this->update_data('ticket_apontamentos',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oTicket_apontamentos){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_apontamentos->getArrayofFields();
			array_push($asFields, 'tkp_delete','tkp_deldate', 'tkp_deluser');
			$amValues = $oTicket_apontamentos->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tkp_id = ');
			$aUpdValues = array($oTicket_apontamentos->gettkp_id());

			if ( $this->update_data('ticket_apontamentos',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oTicket_apontamentos){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_apontamentos->getArrayofFields();
			array_push($asFields, 'tkp_delete','tkp_deldate', 'tkp_deluser');
			$amValues = $oTicket_apontamentos->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tkp_id = ');
			$aUpdValues = array($oTicket_apontamentos->gettkp_id());

			if ( $this->update_data('ticket_apontamentos',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete = ''
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete = '*'
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($tkp_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete = ''
									AND tkp_id = ? 
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkp_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTicket($tkp_tkt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete = ''
									AND tkp_tkt_id = ? 
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkp_tkt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByUser_id($tkp_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete = ''
									AND tkp_user_id = ? 
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkp_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByData($tkp_data, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete = ''
									AND tkp_data = ? 
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkp_data	, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaEmExecucaoTicket($tkp_tkt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								TIMEDIFF(Current_Time(), tkp_hora_exec_ini) AS tempo_execucao,
								tkt_id,tkt_titulo,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',
								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN ticket ON
									tkt_id = tkp_tkt_id
									AND tkt_delete = ''
								LEFT JOIN pasta_trabalho ON
									pst_id = tkt_pst_id
									AND pst_delete = ''
								LEFT JOIN grupo_trabalho ON 
									grt_id = pst_grt_id
									ANd grt_delete = ''
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete 	= ''
									AND tkp_hora_exec_fim IS NULL
									AND tkp_tkt_id 	= ? 
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkp_tkt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaEmExecucaoTicketUsuario($tkp_tkt_id, $tkp_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								TIMEDIFF(Current_Time(), tkp_hora_exec_ini) AS tempo_execucao,
								tkt_id,tkt_titulo,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',
								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN ticket ON
									tkt_id = tkp_tkt_id
									AND tkt_delete = ''
								LEFT JOIN pasta_trabalho ON
									pst_id = tkt_pst_id
									AND pst_delete = ''
								LEFT JOIN grupo_trabalho ON 
									grt_id = pst_grt_id
									ANd grt_delete = ''
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete 	= ''
									AND tkp_hora_exec_fim IS NULL
									AND tkp_tkt_id 	= ? 
									AND tkp_user_id = ? 
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkp_tkt_id	, PDO::PARAM_INT);
				$stmt->bindValue(2,  $tkp_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaEmExecucaoUsuario($tkp_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								TIMEDIFF(Current_Time(), tkp_hora_exec_ini) AS tempo_execucao,
								tkt_id,tkt_titulo,tkt_abertura_data,REPLACE(tkt_abertura_data,' ', 'T') AS 'tkt_abertura_data_comp',
								pst_id,pst_descricao,pst_grt_id,
								grt_id,grt_descricao,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN ticket ON
									tkt_id = tkp_tkt_id
									AND tkt_delete = ''
								LEFT JOIN pasta_trabalho ON
									pst_id = tkt_pst_id
									AND pst_delete = ''
								LEFT JOIN grupo_trabalho ON 
									grt_id = pst_grt_id
									ANd grt_delete = ''
								INNER JOIN usuario ON
									user_id = tkp_user_id
									AND user_resp_ticket = 'S'
									AND user_delete = ''
								WHERE tkp_delete 	= ''
									AND tkp_hora_exec_fim IS NULL
									AND tkp_user_id = ? 
									".$cWhere." 
								ORDER BY tkp_data DESC,tkp_hora_exec_ini DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkp_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_apontamentos	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByCondicao($cOrderby, $cWhere, $cFetch = 'all'){
			try{
				$this->sql = "SELECT
								tkp_id,tkp_tkt_id,tkp_user_id,tkp_data,tkp_hora_exec_ini,tkp_hora_exec_fim,
								tkp_horas_total,SEC_TO_TIME(tkp_horas_total*60*60) AS 'tkp_horas_total_comp',
								tkp_delete,
								TIMEDIFF(Current_Time(), tkp_hora_exec_ini) AS tempo_execucao,
								usr_apt.user_id AS 'apont_user_id',usr_apt.user_nome AS 'apont_user_nome',usr_apt.user_email AS 'apont_user_email',
								
								tkt_id,tkt_titulo,tkt_abertura_data,
								tkt_data_ini_estim,tkt_hora_ini_estim,tkt_data_ini_real,tkt_hora_ini_real,tkt_data_fim_estim,tkt_hora_fim_estim,tkt_data_fim_real,tkt_hora_fim_real,tkt_total_hora_estim,
								tkt_total_hora_real,tkt_per_concluido,
								tkt_aprovado,tkt_aprovado_data,REPLACE(tkt_aprovado_data,' ', 'T') AS 'tkt_aprovado_data_comp',tkt_aprovado_user_id,
								tkt_encerrado,tkt_encerrado_data,REPLACE(tkt_encerrado_data,' ', 'T') AS 'tkt_encerrado_data_comp',tkt_encerrado_user_id,
								tkt_ticket_pai,
								tkt_arquivado,tkt_arquivado_data,REPLACE(tkt_arquivado_data,' ', 'T') AS 'tkt_arquivado_data_comp',tkt_arquivado_user_id,tkt_delete,

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
								usr_aprov.user_id AS 'aprov_user_id',usr_aprov.user_nome AS 'aprov_user_nome',usr_aprov.user_email AS 'aprov_user_email',
								usr_enc.user_id AS 'enc_user_id',usr_enc.user_nome AS 'enc_user_nome',usr_enc.user_email AS 'enc_user_email',
								usr_arq.user_id AS 'arq_user_id',usr_arq.user_nome AS 'arq_user_nome',usr_arq.user_email AS 'arq_user_email',
								usr_solic.user_id AS 'solic_user_id',usr_solic.user_nome AS 'solic_user_nome',usr_solic.user_email AS 'solic_user_email',
								usr_resp.user_id AS 'resp_user_id',usr_resp.user_nome AS 'resp_user_nome',usr_resp.user_email AS 'resp_user_email',

								REPLACE(tkp_incdate, ' ', 'T') as 'tkp_incdate',
								REPLACE(tkp_upddate, ' ', 'T') as 'tkp_upddate',
								REPLACE(tkp_deldate, ' ', 'T') as 'tkp_deldate',tkp_deluser
								FROM ticket_apontamentos
								INNER JOIN usuario AS usr_apt ON
									usr_apt.user_id = tkp_user_id
									AND usr_apt.user_delete = ''
								INNER JOIN ticket ON
									tkt_id = tkp_tkt_id
									AND tkt_delete = ''
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
								WHERE tkp_delete = '' 
									".$cWhere."
								ORDER BY ".$cOrderby;
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				if ( $cFetch == 'all' ) { 
					$aTicket_apontamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} else {
					$aTicket_apontamentos = $stmt->fetch(PDO::FETCH_ASSOC);
				}
				$stmt->closeCursor();

				return $aTicket_apontamentos;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

	}
?>
