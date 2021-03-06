<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class IndicadorDAO extends Security {
		
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function Insere($oIndicador){
			$this->cReturnMsg 	= '';
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

		    $asFields = $oIndicador->getArrayofFields();
		    $amValues = $oIndicador->getArrayofValues();

			if ( $this->insert_data('indicadores',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;	
			} else {
				echo $this->cReturnMsg;
				return false;	
			}		
		}

		public function Altera($oIndicador){
			$this->cReturnMsg 	= '';

		    $asFields = $oIndicador->getArrayofFields();
		    $amValues = $oIndicador->getArrayofValues();
		    $aUpdKeys = array('ind_chave = ');
		    $aUpdValues = array($oIndicador->getind_chave());

			if ( $this->update_data('indicadores',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function buscaByChave($ind_chave){
			try{				
				$this->sql = "SELECT 
								ind_chave,ind_param,ind_valor,ind_descricao
								FROM indicadores
								WHERE ind_chave = ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ind_chave	, PDO::PARAM_STR);
				$stmt->execute();
				$aParametro	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aParametro;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByChaveParam($ind_chave, $ind_param){
			try{				
				$this->sql = "SELECT 
								ind_chave,ind_param,ind_valor,ind_descricao
								FROM indicadores
								WHERE ind_chave 	= ?
									AND ind_param	= ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ind_chave	, PDO::PARAM_STR);
				$stmt->bindValue(2,  $ind_param	, PDO::PARAM_STR);
				$stmt->execute();
				$aParametro	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aParametro;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaAll(){
			try{				
				$this->sql = "SELECT 
								ind_chave,ind_param,ind_valor,ind_descricao
								FROM indicadores";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aParametro	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aParametro;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}
		
		public function getIndicador($ind_chave, $ind_param){
			try{				
				$this->sql = "SELECT 
								ind_chave,ind_param,ind_valor,ind_descricao
								FROM indicadores
								WHERE ind_chave 	= ?
									AND ind_param	= ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ind_chave	, PDO::PARAM_STR);
				$stmt->bindValue(2,  $ind_param	, PDO::PARAM_STR);
				$stmt->execute();
				$aIndicador	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aIndicador;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}
	}
?>