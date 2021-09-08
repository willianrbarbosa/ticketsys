<?php
	class PastaTrabalhoDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oPasta_trabalho){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oPasta_trabalho->getArrayofFields();
			array_push($asFields, 'pst_incdate', 'pst_upddate','pst_delete');
			$amValues = $oPasta_trabalho->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('pasta_trabalho',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oPasta_trabalho){
			$this->cReturnMsg 	= '';

			$asFields = $oPasta_trabalho->getArrayofFields();
			array_push($asFields, 'pst_upddate');
			$amValues = $oPasta_trabalho->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('pst_id = ');
			$aUpdValues = array($oPasta_trabalho->getpst_id());

			if ( $this->update_data('pasta_trabalho',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oPasta_trabalho){
			$this->cReturnMsg 	= '';

			$asFields = $oPasta_trabalho->getArrayofFields();
			array_push($asFields, 'pst_delete','pst_deldate', 'pst_deluser');
			$amValues = $oPasta_trabalho->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('pst_id = ');
			$aUpdValues = array($oPasta_trabalho->getpst_id());

			if ( $this->update_data('pasta_trabalho',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oPasta_trabalho){
			$this->cReturnMsg 	= '';

			$asFields = $oPasta_trabalho->getArrayofFields();
			array_push($asFields, 'pst_delete','pst_deldate', 'pst_deluser');
			$amValues = $oPasta_trabalho->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('pst_id = ');
			$aUpdValues = array($oPasta_trabalho->getpst_id());

			if ( $this->update_data('pasta_trabalho',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								pst_id,pst_descricao,pst_grt_id,pst_delete,
								grt_id,grt_descricao,
								REPLACE(pst_incdate, ' ', 'T') as 'pst_incdate',
								REPLACE(pst_upddate, ' ', 'T') as 'pst_upddate',
								REPLACE(pst_deldate, ' ', 'T') as 'pst_deldate',pst_deluser
								FROM pasta_trabalho
								INNER JOIN grupo_trabalho ON 
									grt_id = pst_grt_id
									ANd grt_delete = ''
								WHERE pst_delete = ''
									".$cWhere." 
								ORDER BY pst_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aPasta_trabalho	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPasta_trabalho;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								pst_id,pst_descricao,pst_grt_id,pst_delete,
								grt_id,grt_descricao,
								REPLACE(pst_incdate, ' ', 'T') as 'pst_incdate',
								REPLACE(pst_upddate, ' ', 'T') as 'pst_upddate',
								REPLACE(pst_deldate, ' ', 'T') as 'pst_deldate',pst_deluser
								FROM pasta_trabalho
								INNER JOIN grupo_trabalho ON 
									grt_id = pst_grt_id
									ANd grt_delete = ''
								WHERE pst_delete = '*'
									".$cWhere." 
								ORDER BY pst_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aPasta_trabalho	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPasta_trabalho;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($pst_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								pst_id,pst_descricao,pst_grt_id,pst_delete,
								grt_id,grt_descricao,
								REPLACE(pst_incdate, ' ', 'T') as 'pst_incdate',
								REPLACE(pst_upddate, ' ', 'T') as 'pst_upddate',
								REPLACE(pst_deldate, ' ', 'T') as 'pst_deldate',pst_deluser
								FROM pasta_trabalho
								INNER JOIN grupo_trabalho ON 
									grt_id = pst_grt_id
									ANd grt_delete = ''
								WHERE pst_delete = ''
									AND pst_id = ? 
									".$cWhere." 
								ORDER BY pst_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $pst_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aPasta_trabalho	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPasta_trabalho;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByGrt_id($pst_grt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								pst_id,pst_descricao,pst_grt_id,pst_delete,
								grt_id,grt_descricao,
								REPLACE(pst_incdate, ' ', 'T') as 'pst_incdate',
								REPLACE(pst_upddate, ' ', 'T') as 'pst_upddate',
								REPLACE(pst_deldate, ' ', 'T') as 'pst_deldate',pst_deluser
								FROM pasta_trabalho
								INNER JOIN grupo_trabalho ON 
									grt_id = pst_grt_id
									ANd grt_delete = ''
								WHERE pst_delete = ''
									AND pst_grt_id = ? 
									".$cWhere." 
								ORDER BY pst_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $pst_grt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aPasta_trabalho	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPasta_trabalho;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
