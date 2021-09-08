<?php
	class SituacaoTicketDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oSituacao_ticket){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oSituacao_ticket->getArrayofFields();
			array_push($asFields, 'stt_incdate', 'stt_upddate','stt_delete');
			$amValues = $oSituacao_ticket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('situacao_ticket',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oSituacao_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oSituacao_ticket->getArrayofFields();
			array_push($asFields, 'stt_upddate');
			$amValues = $oSituacao_ticket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('stt_id = ');
			$aUpdValues = array($oSituacao_ticket->getstt_id());

			if ( $this->update_data('situacao_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oSituacao_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oSituacao_ticket->getArrayofFields();
			array_push($asFields, 'stt_delete','stt_deldate', 'stt_deluser');
			$amValues = $oSituacao_ticket->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('stt_id = ');
			$aUpdValues = array($oSituacao_ticket->getstt_id());

			if ( $this->update_data('situacao_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oSituacao_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oSituacao_ticket->getArrayofFields();
			array_push($asFields, 'stt_delete','stt_deldate', 'stt_deluser');
			$amValues = $oSituacao_ticket->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('stt_id = ');
			$aUpdValues = array($oSituacao_ticket->getstt_id());

			if ( $this->update_data('situacao_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								stt_id,stt_ordem,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,stt_kanban,stt_delete,
								REPLACE(stt_incdate, ' ', 'T') as 'stt_incdate',
								REPLACE(stt_upddate, ' ', 'T') as 'stt_upddate',
								REPLACE(stt_deldate, ' ', 'T') as 'stt_deldate',stt_deluser
								FROM situacao_ticket
								WHERE stt_delete = ''
									".$cWhere." 
								ORDER BY stt_ordem";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aSituacao_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aSituacao_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								stt_id,stt_ordem,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,stt_kanban,stt_delete,
								REPLACE(stt_incdate, ' ', 'T') as 'stt_incdate',
								REPLACE(stt_upddate, ' ', 'T') as 'stt_upddate',
								REPLACE(stt_deldate, ' ', 'T') as 'stt_deldate',stt_deluser
								FROM situacao_ticket
								WHERE stt_delete = '*'
									".$cWhere." 
								ORDER BY stt_ordem";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aSituacao_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aSituacao_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($stt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								stt_id,stt_ordem,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,stt_kanban,stt_delete,
								REPLACE(stt_incdate, ' ', 'T') as 'stt_incdate',
								REPLACE(stt_upddate, ' ', 'T') as 'stt_upddate',
								REPLACE(stt_deldate, ' ', 'T') as 'stt_deldate',stt_deluser
								FROM situacao_ticket
								WHERE stt_delete = ''
									AND stt_id = ? 
									".$cWhere." 
								ORDER BY stt_ordem";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $stt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aSituacao_ticket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aSituacao_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByKanban($stt_kanban, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								stt_id,stt_ordem,stt_descricao,stt_aprova_ticket,stt_encerra_ticket,stt_kanban,stt_delete,
								REPLACE(stt_incdate, ' ', 'T') as 'stt_incdate',
								REPLACE(stt_upddate, ' ', 'T') as 'stt_upddate',
								REPLACE(stt_deldate, ' ', 'T') as 'stt_deldate',stt_deluser
								FROM situacao_ticket
								WHERE stt_delete = ''
									AND stt_kanban = ? 
									".$cWhere." 
								ORDER BY stt_ordem";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $stt_kanban	, PDO::PARAM_STR);

				$stmt->execute();
				$aSituacao_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aSituacao_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
