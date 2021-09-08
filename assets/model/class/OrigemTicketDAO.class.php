<?php
	class OrigemTicketDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oOrigem_ticket){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oOrigem_ticket->getArrayofFields();
			array_push($asFields, 'ort_incdate', 'ort_upddate','ort_delete');
			$amValues = $oOrigem_ticket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('origem_ticket',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oOrigem_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oOrigem_ticket->getArrayofFields();
			array_push($asFields, 'ort_upddate');
			$amValues = $oOrigem_ticket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('ort_id = ');
			$aUpdValues = array($oOrigem_ticket->getort_id());

			if ( $this->update_data('origem_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oOrigem_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oOrigem_ticket->getArrayofFields();
			array_push($asFields, 'ort_delete','ort_deldate', 'ort_deluser');
			$amValues = $oOrigem_ticket->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('ort_id = ');
			$aUpdValues = array($oOrigem_ticket->getort_id());

			if ( $this->update_data('origem_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oOrigem_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oOrigem_ticket->getArrayofFields();
			array_push($asFields, 'ort_delete','ort_deldate', 'ort_deluser');
			$amValues = $oOrigem_ticket->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('ort_id = ');
			$aUpdValues = array($oOrigem_ticket->getort_id());

			if ( $this->update_data('origem_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								ort_id,ort_descricao,ort_delete,
								REPLACE(ort_incdate, ' ', 'T') as 'ort_incdate',
								REPLACE(ort_upddate, ' ', 'T') as 'ort_upddate',
								REPLACE(ort_deldate, ' ', 'T') as 'ort_deldate',ort_deluser
								FROM origem_ticket
								WHERE ort_delete = ''
									".$cWhere." 
								ORDER BY ort_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aOrigem_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aOrigem_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								ort_id,ort_descricao,ort_delete,
								REPLACE(ort_incdate, ' ', 'T') as 'ort_incdate',
								REPLACE(ort_upddate, ' ', 'T') as 'ort_upddate',
								REPLACE(ort_deldate, ' ', 'T') as 'ort_deldate',ort_deluser
								FROM origem_ticket
								WHERE ort_delete = '*'
									".$cWhere." 
								ORDER BY ort_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aOrigem_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aOrigem_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($ort_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								ort_id,ort_descricao,ort_delete,
								REPLACE(ort_incdate, ' ', 'T') as 'ort_incdate',
								REPLACE(ort_upddate, ' ', 'T') as 'ort_upddate',
								REPLACE(ort_deldate, ' ', 'T') as 'ort_deldate',ort_deluser
								FROM origem_ticket
								WHERE ort_delete = ''
									AND ort_id = ? 
									".$cWhere." 
								ORDER BY ort_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ort_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aOrigem_ticket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aOrigem_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
