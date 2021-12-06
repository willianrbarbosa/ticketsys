<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class TicketHistoricoDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function Insere($oTicket_historico){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oTicket_historico->getArrayofFields();
			array_push($asFields, 'tkh_incdate', 'tkh_upddate','tkh_delete');
			$amValues = $oTicket_historico->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('ticket_historico',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oTicket_historico){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_historico->getArrayofFields();
			array_push($asFields, 'tkh_upddate');
			$amValues = $oTicket_historico->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('tkh_id = ');
			$aUpdValues = array($oTicket_historico->gettkh_id());

			if ( $this->update_data('ticket_historico',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oTicket_historico){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_historico->getArrayofFields();
			array_push($asFields, 'tkh_delete','tkh_deldate', 'tkh_deluser');
			$amValues = $oTicket_historico->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tkh_id = ');
			$aUpdValues = array($oTicket_historico->gettkh_id());

			if ( $this->update_data('ticket_historico',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oTicket_historico){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_historico->getArrayofFields();
			array_push($asFields, 'tkh_delete','tkh_deldate', 'tkh_deluser');
			$amValues = $oTicket_historico->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tkh_id = ');
			$aUpdValues = array($oTicket_historico->gettkh_id());

			if ( $this->update_data('ticket_historico',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkh_id,tkh_tkt_id,tkh_user_id,tkh_data_hora,REPLACE(tkh_data_hora, ' ', 'T') as 'tkh_data_hora_comp',tkh_descricao,tkh_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkh_incdate, ' ', 'T') as 'tkh_incdate',
								REPLACE(tkh_upddate, ' ', 'T') as 'tkh_upddate',
								REPLACE(tkh_deldate, ' ', 'T') as 'tkh_deldate',tkh_deluser
								FROM ticket_historico
								LEFT JOIN usuario ON
									user_id = tkh_user_id
									AND user_delete = ''
								WHERE tkh_delete = ''
									".$cWhere." 
								ORDER BY tkh_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_historico	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_historico;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkh_id,tkh_tkt_id,tkh_user_id,tkh_data_hora,REPLACE(tkh_data_hora, ' ', 'T') as 'tkh_data_hora_comp',tkh_descricao,tkh_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkh_incdate, ' ', 'T') as 'tkh_incdate',
								REPLACE(tkh_upddate, ' ', 'T') as 'tkh_upddate',
								REPLACE(tkh_deldate, ' ', 'T') as 'tkh_deldate',tkh_deluser
								FROM ticket_historico
								LEFT JOIN usuario ON
									user_id = tkh_user_id
									AND user_delete = ''
								WHERE tkh_delete = '*'
									".$cWhere." 
								ORDER BY tkh_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_historico	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_historico;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($tkh_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkh_id,tkh_tkt_id,tkh_user_id,tkh_data_hora,REPLACE(tkh_data_hora, ' ', 'T') as 'tkh_data_hora_comp',tkh_descricao,tkh_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkh_incdate, ' ', 'T') as 'tkh_incdate',
								REPLACE(tkh_upddate, ' ', 'T') as 'tkh_upddate',
								REPLACE(tkh_deldate, ' ', 'T') as 'tkh_deldate',tkh_deluser
								FROM ticket_historico
								LEFT JOIN usuario ON
									user_id = tkh_user_id
									AND user_delete = ''
								WHERE tkh_delete = ''
									AND tkh_id = ? 
									".$cWhere." 
								ORDER BY tkh_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkh_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_historico	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_historico;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTicket($tkh_tkt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkh_id,tkh_tkt_id,tkh_user_id,tkh_data_hora,REPLACE(tkh_data_hora, ' ', 'T') as 'tkh_data_hora_comp',tkh_descricao,tkh_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkh_incdate, ' ', 'T') as 'tkh_incdate',
								REPLACE(tkh_upddate, ' ', 'T') as 'tkh_upddate',
								REPLACE(tkh_deldate, ' ', 'T') as 'tkh_deldate',tkh_deluser
								FROM ticket_historico
								LEFT JOIN usuario ON
									user_id = tkh_user_id
									AND user_delete = ''
								WHERE tkh_delete = ''
									AND tkh_tkt_id = ? 
									".$cWhere." 
								ORDER BY tkh_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkh_tkt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_historico	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_historico;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByUser_id($tkh_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkh_id,tkh_tkt_id,tkh_user_id,tkh_data_hora,REPLACE(tkh_data_hora, ' ', 'T') as 'tkh_data_hora_comp',tkh_descricao,tkh_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkh_incdate, ' ', 'T') as 'tkh_incdate',
								REPLACE(tkh_upddate, ' ', 'T') as 'tkh_upddate',
								REPLACE(tkh_deldate, ' ', 'T') as 'tkh_deldate',tkh_deluser
								FROM ticket_historico
								LEFT JOIN usuario ON
									user_id = tkh_user_id
									AND user_delete = ''
								WHERE tkh_delete = ''
									AND tkh_user_id = ? 
									".$cWhere." 
								ORDER BY tkh_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkh_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_historico	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_historico;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByData_hora($tkh_data_hora, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkh_id,tkh_tkt_id,tkh_user_id,tkh_data_hora,REPLACE(tkh_data_hora, ' ', 'T') as 'tkh_data_hora_comp',tkh_descricao,tkh_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkh_incdate, ' ', 'T') as 'tkh_incdate',
								REPLACE(tkh_upddate, ' ', 'T') as 'tkh_upddate',
								REPLACE(tkh_deldate, ' ', 'T') as 'tkh_deldate',tkh_deluser
								FROM ticket_historico
								LEFT JOIN usuario ON
									user_id = tkh_user_id
									AND user_delete = ''
								WHERE tkh_delete = ''
									AND tkh_data_hora = ? 
									".$cWhere." 
								ORDER BY tkh_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkh_data_hora	, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket_historico	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_historico;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>