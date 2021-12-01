<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class GrupoTrabalhoDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oGrupo_trabalho){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oGrupo_trabalho->getArrayofFields();
			array_push($asFields, 'grt_incdate', 'grt_upddate','grt_delete');
			$amValues = $oGrupo_trabalho->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('grupo_trabalho',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oGrupo_trabalho){
			$this->cReturnMsg 	= '';

			$asFields = $oGrupo_trabalho->getArrayofFields();
			array_push($asFields, 'grt_upddate');
			$amValues = $oGrupo_trabalho->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('grt_id = ');
			$aUpdValues = array($oGrupo_trabalho->getgrt_id());

			if ( $this->update_data('grupo_trabalho',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oGrupo_trabalho){
			$this->cReturnMsg 	= '';

			$asFields = $oGrupo_trabalho->getArrayofFields();
			array_push($asFields, 'grt_delete','grt_deldate', 'grt_deluser');
			$amValues = $oGrupo_trabalho->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('grt_id = ');
			$aUpdValues = array($oGrupo_trabalho->getgrt_id());

			if ( $this->update_data('grupo_trabalho',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oGrupo_trabalho){
			$this->cReturnMsg 	= '';

			$asFields = $oGrupo_trabalho->getArrayofFields();
			array_push($asFields, 'grt_delete','grt_deldate', 'grt_deluser');
			$amValues = $oGrupo_trabalho->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('grt_id = ');
			$aUpdValues = array($oGrupo_trabalho->getgrt_id());

			if ( $this->update_data('grupo_trabalho',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								grt_id,grt_descricao,grt_delete,
								REPLACE(grt_incdate, ' ', 'T') as 'grt_incdate',
								REPLACE(grt_upddate, ' ', 'T') as 'grt_upddate',
								REPLACE(grt_deldate, ' ', 'T') as 'grt_deldate',grt_deluser
								FROM grupo_trabalho
								WHERE grt_delete = ''
									".$cWhere." 
								ORDER BY grt_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aGrupo_trabalho	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aGrupo_trabalho;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								grt_id,grt_descricao,grt_delete,
								REPLACE(grt_incdate, ' ', 'T') as 'grt_incdate',
								REPLACE(grt_upddate, ' ', 'T') as 'grt_upddate',
								REPLACE(grt_deldate, ' ', 'T') as 'grt_deldate',grt_deluser
								FROM grupo_trabalho
								WHERE grt_delete = '*'
									".$cWhere." 
								ORDER BY grt_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aGrupo_trabalho	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aGrupo_trabalho;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($grt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								grt_id,grt_descricao,grt_delete,
								REPLACE(grt_incdate, ' ', 'T') as 'grt_incdate',
								REPLACE(grt_upddate, ' ', 'T') as 'grt_upddate',
								REPLACE(grt_deldate, ' ', 'T') as 'grt_deldate',grt_deluser
								FROM grupo_trabalho
								WHERE grt_delete = ''
									AND grt_id = ? 
									".$cWhere." 
								ORDER BY grt_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $grt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aGrupo_trabalho	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aGrupo_trabalho;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
