<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class PrioridadeTicketDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oPrioridade_ticket){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oPrioridade_ticket->getArrayofFields();
			array_push($asFields, 'prt_incdate', 'prt_upddate','prt_delete');
			$amValues = $oPrioridade_ticket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('prioridade_ticket',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oPrioridade_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oPrioridade_ticket->getArrayofFields();
			array_push($asFields, 'prt_upddate');
			$amValues = $oPrioridade_ticket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('prt_id = ');
			$aUpdValues = array($oPrioridade_ticket->getprt_id());

			if ( $this->update_data('prioridade_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oPrioridade_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oPrioridade_ticket->getArrayofFields();
			array_push($asFields, 'prt_delete','prt_deldate', 'prt_deluser');
			$amValues = $oPrioridade_ticket->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('prt_id = ');
			$aUpdValues = array($oPrioridade_ticket->getprt_id());

			if ( $this->update_data('prioridade_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oPrioridade_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oPrioridade_ticket->getArrayofFields();
			array_push($asFields, 'prt_delete','prt_deldate', 'prt_deluser');
			$amValues = $oPrioridade_ticket->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('prt_id = ');
			$aUpdValues = array($oPrioridade_ticket->getprt_id());

			if ( $this->update_data('prioridade_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								prt_id,prt_prioridade,prt_descricao,prt_cor,prt_delete,
								REPLACE(prt_incdate, ' ', 'T') as 'prt_incdate',
								REPLACE(prt_upddate, ' ', 'T') as 'prt_upddate',
								REPLACE(prt_deldate, ' ', 'T') as 'prt_deldate',prt_deluser
								FROM prioridade_ticket
								WHERE prt_delete = ''
									".$cWhere." 
								ORDER BY prt_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aPrioridade_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPrioridade_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								prt_id,prt_prioridade,prt_descricao,prt_cor,prt_delete,
								REPLACE(prt_incdate, ' ', 'T') as 'prt_incdate',
								REPLACE(prt_upddate, ' ', 'T') as 'prt_upddate',
								REPLACE(prt_deldate, ' ', 'T') as 'prt_deldate',prt_deluser
								FROM prioridade_ticket
								WHERE prt_delete = '*'
									".$cWhere." 
								ORDER BY prt_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aPrioridade_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPrioridade_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($prt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								prt_id,prt_prioridade,prt_descricao,prt_cor,prt_delete,
								REPLACE(prt_incdate, ' ', 'T') as 'prt_incdate',
								REPLACE(prt_upddate, ' ', 'T') as 'prt_upddate',
								REPLACE(prt_deldate, ' ', 'T') as 'prt_deldate',prt_deluser
								FROM prioridade_ticket
								WHERE prt_delete = ''
									AND prt_id = ? 
									".$cWhere." 
								ORDER BY prt_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $prt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aPrioridade_ticket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPrioridade_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
