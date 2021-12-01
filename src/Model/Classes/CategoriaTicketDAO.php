<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class CategoriaTicketDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oCategoria_ticket){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oCategoria_ticket->getArrayofFields();
			array_push($asFields, 'cgt_incdate', 'cgt_upddate','cgt_delete');
			$amValues = $oCategoria_ticket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('categoria_ticket',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oCategoria_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oCategoria_ticket->getArrayofFields();
			array_push($asFields, 'cgt_upddate');
			$amValues = $oCategoria_ticket->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('cgt_id = ');
			$aUpdValues = array($oCategoria_ticket->getcgt_id());

			if ( $this->update_data('categoria_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oCategoria_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oCategoria_ticket->getArrayofFields();
			array_push($asFields, 'cgt_delete','cgt_deldate', 'cgt_deluser');
			$amValues = $oCategoria_ticket->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('cgt_id = ');
			$aUpdValues = array($oCategoria_ticket->getcgt_id());

			if ( $this->update_data('categoria_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oCategoria_ticket){
			$this->cReturnMsg 	= '';

			$asFields = $oCategoria_ticket->getArrayofFields();
			array_push($asFields, 'cgt_delete','cgt_deldate', 'cgt_deluser');
			$amValues = $oCategoria_ticket->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('cgt_id = ');
			$aUpdValues = array($oCategoria_ticket->getcgt_id());

			if ( $this->update_data('categoria_ticket',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								cgt_id,cgt_descricao,cgt_delete,
								REPLACE(cgt_incdate, ' ', 'T') as 'cgt_incdate',
								REPLACE(cgt_upddate, ' ', 'T') as 'cgt_upddate',
								REPLACE(cgt_deldate, ' ', 'T') as 'cgt_deldate',cgt_deluser
								FROM categoria_ticket
								WHERE cgt_delete = ''
									".$cWhere." 
								ORDER BY cgt_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aCategoria_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aCategoria_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								cgt_id,cgt_descricao,cgt_delete,
								REPLACE(cgt_incdate, ' ', 'T') as 'cgt_incdate',
								REPLACE(cgt_upddate, ' ', 'T') as 'cgt_upddate',
								REPLACE(cgt_deldate, ' ', 'T') as 'cgt_deldate',cgt_deluser
								FROM categoria_ticket
								WHERE cgt_delete = '*'
									".$cWhere." 
								ORDER BY cgt_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aCategoria_ticket	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aCategoria_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($cgt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								cgt_id,cgt_descricao,cgt_delete,
								REPLACE(cgt_incdate, ' ', 'T') as 'cgt_incdate',
								REPLACE(cgt_upddate, ' ', 'T') as 'cgt_upddate',
								REPLACE(cgt_deldate, ' ', 'T') as 'cgt_deldate',cgt_deluser
								FROM categoria_ticket
								WHERE cgt_delete = ''
									AND cgt_id = ? 
									".$cWhere." 
								ORDER BY cgt_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $cgt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aCategoria_ticket	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aCategoria_ticket;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
