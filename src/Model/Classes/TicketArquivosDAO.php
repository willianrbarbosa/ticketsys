<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class TicketArquivosDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oTicket_arquivos){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oTicket_arquivos->getArrayofFields();
			array_push($asFields, 'tka_incdate', 'tka_upddate','tka_delete');
			$amValues = $oTicket_arquivos->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('ticket_arquivos',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oTicket_arquivos){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_arquivos->getArrayofFields();
			array_push($asFields, 'tka_upddate');
			$amValues = $oTicket_arquivos->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('tka_id = ');
			$aUpdValues = array($oTicket_arquivos->gettka_id());

			if ( $this->update_data('ticket_arquivos',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oTicket_arquivos){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_arquivos->getArrayofFields();
			array_push($asFields, 'tka_delete','tka_deldate', 'tka_deluser');
			$amValues = $oTicket_arquivos->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tka_id = ');
			$aUpdValues = array($oTicket_arquivos->gettka_id());

			if ( $this->update_data('ticket_arquivos',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oTicket_arquivos){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_arquivos->getArrayofFields();
			array_push($asFields, 'tka_delete','tka_deldate', 'tka_deluser');
			$amValues = $oTicket_arquivos->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tka_id = ');
			$aUpdValues = array($oTicket_arquivos->gettka_id());

			if ( $this->update_data('ticket_arquivos',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tka_id,tka_tkt_id,tka_user_id,tka_data_hora,REPLACE(tka_data_hora, ' ', 'T') as 'tka_data_hora_comp',tka_arquivo_nome,tka_arquivo_local,tka_arquivo_tipo,tka_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,

								REPLACE(tka_incdate, ' ', 'T') as 'tka_incdate',
								REPLACE(tka_upddate, ' ', 'T') as 'tka_upddate',
								REPLACE(tka_deldate, ' ', 'T') as 'tka_deldate',tka_deluser
								FROM ticket_arquivos
								LEFT JOIN usuario ON
									user_id = tka_user_id
									AND user_delete = ''
								WHERE tka_delete = ''
									".$cWhere." 
								ORDER BY tka_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_arquivos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_arquivos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tka_id,tka_tkt_id,tka_user_id,tka_data_hora,REPLACE(tka_data_hora, ' ', 'T') as 'tka_data_hora_comp',tka_arquivo_nome,tka_arquivo_local,tka_arquivo_tipo,tka_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tka_incdate, ' ', 'T') as 'tka_incdate',
								REPLACE(tka_upddate, ' ', 'T') as 'tka_upddate',
								REPLACE(tka_deldate, ' ', 'T') as 'tka_deldate',tka_deluser
								FROM ticket_arquivos
								LEFT JOIN usuario ON
									user_id = tka_user_id
									AND user_delete = ''
								WHERE tka_delete = '*'
									".$cWhere." 
								ORDER BY tka_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_arquivos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_arquivos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($tka_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tka_id,tka_tkt_id,tka_user_id,tka_data_hora,REPLACE(tka_data_hora, ' ', 'T') as 'tka_data_hora_comp',tka_arquivo_nome,tka_arquivo_local,tka_arquivo_tipo,tka_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tka_incdate, ' ', 'T') as 'tka_incdate',
								REPLACE(tka_upddate, ' ', 'T') as 'tka_upddate',
								REPLACE(tka_deldate, ' ', 'T') as 'tka_deldate',tka_deluser
								FROM ticket_arquivos
								LEFT JOIN usuario ON
									user_id = tka_user_id
									AND user_delete = ''
								WHERE tka_delete = ''
									AND tka_id = ? 
									".$cWhere." 
								ORDER BY tka_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tka_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_arquivos	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_arquivos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTkt_id($tka_tkt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tka_id,tka_tkt_id,tka_user_id,tka_data_hora,REPLACE(tka_data_hora, ' ', 'T') as 'tka_data_hora_comp',tka_arquivo_nome,tka_arquivo_local,tka_arquivo_tipo,tka_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tka_incdate, ' ', 'T') as 'tka_incdate',
								REPLACE(tka_upddate, ' ', 'T') as 'tka_upddate',
								REPLACE(tka_deldate, ' ', 'T') as 'tka_deldate',tka_deluser
								FROM ticket_arquivos
								LEFT JOIN usuario ON
									user_id = tka_user_id
									AND user_delete = ''
								WHERE tka_delete = ''
									AND tka_tkt_id = ? 
									".$cWhere." 
								ORDER BY tka_data_hora";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tka_tkt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_arquivos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_arquivos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByUser_id($tka_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tka_id,tka_tkt_id,tka_user_id,tka_data_hora,REPLACE(tka_data_hora, ' ', 'T') as 'tka_data_hora_comp',tka_arquivo_nome,tka_arquivo_local,tka_arquivo_tipo,tka_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tka_incdate, ' ', 'T') as 'tka_incdate',
								REPLACE(tka_upddate, ' ', 'T') as 'tka_upddate',
								REPLACE(tka_deldate, ' ', 'T') as 'tka_deldate',tka_deluser
								FROM ticket_arquivos
								LEFT JOIN usuario ON
									user_id = tka_user_id
									AND user_delete = ''
								WHERE tka_delete = ''
									AND tka_user_id = ? 
									".$cWhere." 
								ORDER BY tka_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tka_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_arquivos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_arquivos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByData_hora($tka_data_hora, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tka_id,tka_tkt_id,tka_user_id,tka_data_hora,REPLACE(tka_data_hora, ' ', 'T') as 'tka_data_hora_comp',tka_arquivo_nome,tka_arquivo_local,tka_arquivo_tipo,tka_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tka_incdate, ' ', 'T') as 'tka_incdate',
								REPLACE(tka_upddate, ' ', 'T') as 'tka_upddate',
								REPLACE(tka_deldate, ' ', 'T') as 'tka_deldate',tka_deluser
								FROM ticket_arquivos
								LEFT JOIN usuario ON
									user_id = tka_user_id
									AND user_delete = ''
								WHERE tka_delete = ''
									AND tka_data_hora = ? 
									".$cWhere." 
								ORDER BY tka_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tka_data_hora	, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket_arquivos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_arquivos;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
