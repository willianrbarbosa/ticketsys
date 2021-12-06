<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class PerfilAcessoDAO extends Security {
		
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function Insere($oPerfilAcesso){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

		    $asFields = $oPerfilAcesso->getArrayofFields();
		    array_push($asFields, 'pfa_incdate', 'pfa_upddate','pfa_delete');
		    $amValues = $oPerfilAcesso->getArrayofValues();
		    array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('perfil_acesso',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;	
			} else {
				echo $this->cReturnMsg;
				return false;	
			}
		}

		public function Altera($oPerfilAcesso){
			$this->cReturnMsg 	= '';

		    $asFields = $oPerfilAcesso->getArrayofFields();
		    array_push($asFields, 'pfa_upddate');
		    $amValues = $oPerfilAcesso->getArrayofValues();
		    array_push($amValues,Date('Y-m-d H:i:s'));
		    $aUpdKeys = array('pfa_id = ');
		    $aUpdValues = array($oPerfilAcesso->getpfa_id());

			if ( $this->update_data('perfil_acesso',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
			
		}

		public function Deleta($oPerfilAcesso){
			$this->cReturnMsg 	= '';

		    $asFields = array();
		    array_push($asFields, 'pfa_delete','pfa_deldate', 'pfa_deluser');
		    $amValues = array();
		    array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getuser_nome());
		    $aUpdKeys = array('pfa_id = ');
		    $aUpdValues = array($oPerfilAcesso->getpfa_id());

			if ( $this->update_data('perfil_acesso',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function Restaura($oPerfilAcesso){
			$this->cReturnMsg 	= '';

		    $asFields = array();
		    array_push($asFields, 'pfa_delete','pfa_deldate', 'pfa_deluser');
		    $amValues = array();
		    array_push($amValues, '',null, '');
		    $aUpdKeys = array('pfa_id = ');
		    $aUpdValues = array($oPerfilAcesso->getpfa_id());

			if ( $this->update_data('perfil_acesso',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function buscaAll(){
			try{				
				$this->sql = "SELECT 
								pfa_id, pfa_descricao,
								REPLACE(pfa_incdate, ' ', 'T') as 'pfa_incdate',REPLACE(pfa_upddate, ' ', 'T') as 'pfa_upddate'
								FROM perfil_acesso
								WHERE pfa_delete = ''
								ORDER BY pfa_descricao";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aPerfisAcessos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfisAcessos;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaById($pfa_id){
			try{				
				$this->sql = "SELECT 
								pfa_id, pfa_descricao,
								REPLACE(pfa_incdate, ' ', 'T') as 'pfa_incdate',REPLACE(pfa_upddate, ' ', 'T') as 'pfa_upddate'
								FROM perfil_acesso
								WHERE pfa_delete = ''
									AND pfa_id = ?
								ORDER BY pfa_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $pfa_id	, PDO::PARAM_INT);
				$stmt->execute();
				$aPerfilAcesso	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfilAcesso;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByCondicao($cWhere){
			$cWhere = (!empty($cWhere) ? 'AND ' : '').$cWhere;
			try{				
				$this->sql = "SELECT 
								pfa_id, pfa_descricao,
								REPLACE(pfa_incdate, ' ', 'T') as 'pfa_incdate',REPLACE(pfa_upddate, ' ', 'T') as 'pfa_upddate'
								FROM perfil_acesso
								WHERE pfa_delete = ''
								".$cWhere."
								ORDER BY pfa_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aPerfisAcessos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfisAcessos;

			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaDeletedByCondicao($cWhere){
			$cWhere = (!empty($cWhere) ? 'AND ' : '').$cWhere;
			try{				
				$this->sql = "SELECT 
								pfa_id, pfa_descricao,
								REPLACE(pfa_incdate, ' ', 'T') as 'pfa_incdate',REPLACE(pfa_upddate, ' ', 'T') as 'pfa_upddate',
								REPLACE(pfa_deldate, ' ', 'T') as 'pfa_deldate', pfa_deluser
								FROM perfil_acesso
								WHERE pfa_delete = '*'
								".$cWhere."
								ORDER BY pfa_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aPerfisAcessos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfisAcessos;

			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaPerfilByCliPlano($cli_id){
			try{
				$this->sql = "SELECT pfa_id, pfa_descricao,
								REPLACE(pfa_incdate, ' ', 'T') as 'pfa_incdate',REPLACE(pfa_upddate, ' ', 'T') as 'pfa_upddate',
								REPLACE(pfa_deldate, ' ', 'T') as 'pfa_deldate', pfa_deluser
								FROM cliente
								LEFT JOIN plano ON
								pla_id = cli_pla_id
								AND pla_delete = ''
								LEFT JOIN perfil_acesso ON
								pfa_id = pla_pfa_id
								AND pfa_delete = ''
								WHERE cli_id = ?
								AND cli_delete = ''";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $cli_id	, PDO::PARAM_INT);
				$stmt->execute();
				$aPerfisAcessos = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfisAcessos;
			}catch (PDOException $ex){
				echo "Erro: ".$ex->getMessage();
				return false;
			}
		}
	}
?>