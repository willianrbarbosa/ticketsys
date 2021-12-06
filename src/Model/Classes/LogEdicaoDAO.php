<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class LogEdicaoDAO extends Security {
		
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function Insere($oLogEdicao){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

		    $asFields = $oLogEdicao->getArrayofFields();
		    $amValues = $oLogEdicao->getArrayofValues();

			if ( $this->insert_data('log_edicao',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;	
			} else {
				echo $this->cReturnMsg;
				return false;	
			}
		}

		public function Altera($oLogEdicao){
			$this->cReturnMsg 	= '';

		    $asFields = $oLogEdicao->getArrayofFields();
		    $amValues = $oLogEdicao->getArrayofValues();
		    $aUpdKeys = array('led_id = ');
		    $aUpdValues = array($oLogEdicao->getled_id());

			if ( $this->update_data('log_edicao',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
			
		}

		public function Apaga($oLogEdicao){
			$this->cReturnMsg 	= '';

		    $aUpdKeys = array('led_id = ');
		    $aUpdValues = array($oLogEdicao->getled_id());

			if ( $this->erase_data('log_edicao',$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function buscaAll(){
			try{	
				$this->sql = "SELECT 
								led_id,led_user_id,led_rot_nome,led_key,led_action,led_table,led_date,
								REPLACE(led_date, ' ', 'T') as 'led_date_comp',
								user.user_nome,user.user_email,user.user_tipo,
								rot_nome,rot_descricao
								FROM log_edicao AS lcc
								INNER JOIN usuario AS user ON 
									user.user_id = lcc.led_user_id
									AND user.user_delete = ''
								LEFT JOIN rotina AS rot ON 
									rot.rot_nome = lcc.led_rot_nome
								ORDER BY lcc.led_date DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aLOGEdicao	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aLOGEdicao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaById($lcc_id){
			try{				
				$this->sql = "SELECT 
								led_id,led_user_id,led_rot_nome,led_key,led_action,led_table,led_date,
								REPLACE(led_date, ' ', 'T') as 'led_date_comp',
								user.user_nome,user.user_email,user.user_tipo,
								rot_nome,rot_descricao
								FROM log_edicao AS lcc
								INNER JOIN usuario AS user ON 
									user.user_id = lcc.led_user_id
									AND user.user_delete = ''
								LEFT JOIN rotina AS rot ON 
									rot.rot_nome = lcc.led_rot_nome 
								WHERE led_id = ?
								ORDER BY lcc.led_date DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $lcc_id	, PDO::PARAM_INT);
				$stmt->execute();
				$aLOGEdicao	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aLOGEdicao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByRotina($led_rot_nome){
			try{				
				$this->sql = "SELECT 
								led_id,led_user_id,led_rot_nome,led_key,led_action,led_table,led_date,
								REPLACE(led_date, ' ', 'T') as 'led_date_comp',
								user.user_nome,user.user_email,user.user_tipo,
								rot_nome,rot_descricao
								FROM log_edicao AS lcc
								INNER JOIN usuario AS user ON 
									user.user_id = lcc.led_user_id
									AND user.user_delete = ''
								LEFT JOIN rotina AS rot ON 
									rot.rot_nome = lcc.led_rot_nome
								WHERE led_rot_nome = ?
								ORDER BY lcc.led_date DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $led_rot_nome	, PDO::PARAM_INT);
				$stmt->execute();
				$aLOGEdicao	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aLOGEdicao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByUser($led_user_id){
			try{				
				$this->sql = "SELECT 
								led_id,led_user_id,led_rot_nome,led_key,led_action,led_table,led_date,
								REPLACE(led_date, ' ', 'T') as 'led_date_comp',
								user.user_nome,user.user_email,user.user_tipo,
								rot_nome,rot_descricao
								FROM log_edicao AS lcc
								INNER JOIN usuario AS user ON 
									user.user_id = lcc.led_user_id
									AND user.user_delete = ''
								LEFT JOIN rotina AS rot ON 
									rot.rot_nome = lcc.led_rot_nome
								WHERE led_user_id = ?
								ORDER BY lcc.led_date DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $led_user_id	, PDO::PARAM_INT);
				$stmt->execute();
				$aLOGEdicao	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aLOGEdicao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaFilters($led_user_id, $led_Action){
			try{			
				$cWhere = '';
				if ( $led_user_id <> null ) {	$cWhere .= " AND led_user_id = ".$led_user_id;	}
				if ( $led_Action <> null ) {	$cWhere .= " AND led_action = '".$led_Action."' ";	}
	
				$this->sql = "SELECT 
								led_id,led_user_id,led_rot_nome,led_key,led_action,led_table,led_date,
								REPLACE(led_date, ' ', 'T') as 'led_date_comp',
								user.user_nome,user.user_email,user.user_tipo,
								rot_nome,rot_descricao
								FROM log_edicao AS lcc
								INNER JOIN usuario AS user ON 
									user.user_id = lcc.led_user_id
									AND user.user_delete = ''
								LEFT JOIN rotina AS rot ON 
									rot.rot_nome = lcc.led_rot_nome
								WHERE led_id > 0
									".$cWhere."
								ORDER BY lcc.led_date DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aLOGEdicao	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aLOGEdicao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}
		
	}
?>