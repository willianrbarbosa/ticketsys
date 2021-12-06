<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class ParametroDAO extends Security {
		
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function Insere($oParametro){
			$this->cReturnMsg 	= '';
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

		    $asFields = $oParametro->getArrayofFields();
		    $amValues = $oParametro->getArrayofValues();

			if ( $this->insert_data('parametro',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;	
			} else {
				echo $this->cReturnMsg;
				return false;	
			}		
		}

		public function Altera($oParametro){
			$this->cReturnMsg 	= '';

		    $asFields = $oParametro->getArrayofFields();
		    $amValues = $oParametro->getArrayofValues();
		    $aUpdKeys = array('par_key = ');
		    $aUpdValues = array($oParametro->getpar_key());

			if ( $this->update_data('parametro',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function AtualizaConteudo($par_key, $par_conteudo){
			$this->cReturnMsg 	= '';

		    $asFields = array('par_conteudo');
		    $amValues = array($par_conteudo);
		    $aUpdKeys = array('par_key = ');
		    $aUpdValues = array($par_key);

			if ( $this->update_data('parametro',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function buscaByKey($par_key){
			try{				
				$this->sql = "SELECT 
								par_key,par_conteudo,par_descricao
								FROM parametro
								WHERE par_key = ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $par_key	, PDO::PARAM_STR);
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
								par_key,par_conteudo,par_descricao
								FROM parametro";
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

		public function getParametro($par_key){
			try{				
				$this->sql = "SELECT 
								par_key,par_conteudo,par_descricao
								FROM parametro
								WHERE par_key = ?";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $par_key	, PDO::PARAM_STR);
				$stmt->execute();
				$aParametro	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aParametro['par_conteudo'];
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}
	}
?>