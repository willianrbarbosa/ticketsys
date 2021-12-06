<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class TipoAtividadeDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function Insere($oTipo_atividade){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oTipo_atividade->getArrayofFields();
			array_push($asFields, 'tav_incdate', 'tav_upddate','tav_delete');
			$amValues = $oTipo_atividade->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('tipo_atividade',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oTipo_atividade){
			$this->cReturnMsg 	= '';

			$asFields = $oTipo_atividade->getArrayofFields();
			array_push($asFields, 'tav_upddate');
			$amValues = $oTipo_atividade->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('tav_id = ');
			$aUpdValues = array($oTipo_atividade->gettav_id());

			if ( $this->update_data('tipo_atividade',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oTipo_atividade){
			$this->cReturnMsg 	= '';

			$asFields = $oTipo_atividade->getArrayofFields();
			array_push($asFields, 'tav_delete','tav_deldate', 'tav_deluser');
			$amValues = $oTipo_atividade->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tav_id = ');
			$aUpdValues = array($oTipo_atividade->gettav_id());

			if ( $this->update_data('tipo_atividade',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oTipo_atividade){
			$this->cReturnMsg 	= '';

			$asFields = $oTipo_atividade->getArrayofFields();
			array_push($asFields, 'tav_delete','tav_deldate', 'tav_deluser');
			$amValues = $oTipo_atividade->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tav_id = ');
			$aUpdValues = array($oTipo_atividade->gettav_id());

			if ( $this->update_data('tipo_atividade',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tav_id,tav_descricao,tav_delete,
								REPLACE(tav_incdate, ' ', 'T') as 'tav_incdate',
								REPLACE(tav_upddate, ' ', 'T') as 'tav_upddate',
								REPLACE(tav_deldate, ' ', 'T') as 'tav_deldate',tav_deluser
								FROM tipo_atividade
								WHERE tav_delete = ''
									".$cWhere." 
								ORDER BY tav_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTipo_atividade	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTipo_atividade;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tav_id,tav_descricao,tav_delete,
								REPLACE(tav_incdate, ' ', 'T') as 'tav_incdate',
								REPLACE(tav_upddate, ' ', 'T') as 'tav_upddate',
								REPLACE(tav_deldate, ' ', 'T') as 'tav_deldate',tav_deluser
								FROM tipo_atividade
								WHERE tav_delete = '*'
									".$cWhere." 
								ORDER BY tav_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTipo_atividade	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTipo_atividade;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($tav_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tav_id,tav_descricao,tav_delete,
								REPLACE(tav_incdate, ' ', 'T') as 'tav_incdate',
								REPLACE(tav_upddate, ' ', 'T') as 'tav_upddate',
								REPLACE(tav_deldate, ' ', 'T') as 'tav_deldate',tav_deluser
								FROM tipo_atividade
								WHERE tav_delete = ''
									AND tav_id = ? 
									".$cWhere." 
								ORDER BY tav_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tav_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTipo_atividade	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTipo_atividade;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
