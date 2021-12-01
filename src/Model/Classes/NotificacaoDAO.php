<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class NotificacaoDAO extends Security {
		
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oNotificacao){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

		    $asFields = $oNotificacao->getArrayofFields();
		    array_push($asFields, 'ntf_incdate', 'ntf_upddate','ntf_delete');
		    $amValues = $oNotificacao->getArrayofValues();
		    array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('notificacao',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;	
			} else {
				echo $this->cReturnMsg;
				return false;	
			}
		}

		public function Altera($oNotificacao){
			$this->cReturnMsg 	= '';

		    $asFields = $oNotificacao->getArrayofFields();
		    array_push($asFields, 'ntf_upddate');
		    $amValues = $oNotificacao->getArrayofValues();
		    array_push($amValues,Date('Y-m-d H:i:s'));
		    $aUpdKeys = array('ntf_id = ');
		    $aUpdValues = array($oNotificacao->getntf_id());

			if ( $this->update_data('notificacao',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
			
		}

		public function Apaga($oNotificacao){
			$this->cReturnMsg 	= '';

		    $asFields = array();
		    $aUpdKeys = array('ntf_id = ');
		    $aUpdValues = array($oNotificacao->getntf_id());

			if ( $this->erase_data('notificacao',$asFields, $aUpdKeys, $aUpdValues, $this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function buscaAll(){
			try{				
				$this->sql = "SELECT 
								ntf_id,ntf_dest_user_id,ntf_data_hora,ntf_tipo_alerta,ntf_notificacao,ntf_url,ntf_lida,
								REPLACE(ntf_data_hora, ' ', 'T') as 'ntf_data_hora',
								REPLACE(ntf_incdate, ' ', 'T') as 'ntf_incdate',REPLACE(ntf_upddate, ' ', 'T') as 'ntf_upddate'
								FROM notificacao
								ORDER BY ntf_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$ddaNotificacao	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $ddaNotificacao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaById($ntf_id){
			try{				
				$this->sql = "SELECT 
								ntf_id,ntf_dest_user_id,ntf_data_hora,ntf_tipo_alerta,ntf_notificacao,ntf_url,ntf_lida,
								REPLACE(ntf_data_hora, ' ', 'T') as 'ntf_data_hora',
								REPLACE(ntf_incdate, ' ', 'T') as 'ntf_incdate',REPLACE(ntf_upddate, ' ', 'T') as 'ntf_upddate'
								FROM notificacao
								WHERE ntf_id = ?
								ORDER BY ntf_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ntf_id	, PDO::PARAM_INT);
				$stmt->execute();
				$ddaNotificacao	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $ddaNotificacao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByUser($ntf_dest_user_id, $nLimit){
			try{				
				$cLimit = ($nLimit > 0 ? ' LIMIT '.$nLimit : '');
				$this->sql = "SELECT 
								ntf_id,ntf_dest_user_id,ntf_data_hora,ntf_tipo_alerta,ntf_notificacao,ntf_url,ntf_lida,
								REPLACE(ntf_data_hora, ' ', 'T') as 'ntf_data_hora',
								REPLACE(ntf_incdate, ' ', 'T') as 'ntf_incdate',REPLACE(ntf_upddate, ' ', 'T') as 'ntf_upddate'
								FROM notificacao
								WHERE ntf_dest_user_id = ?
								ORDER BY ntf_data_hora DESC".$cLimit;
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ntf_dest_user_id	, PDO::PARAM_INT);
				$stmt->execute();
				$ddaNotificacao	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $ddaNotificacao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaUnreadByUser($ntf_dest_user_id){
			try{				
				$this->sql = "SELECT 
								ntf_id,ntf_dest_user_id,ntf_data_hora,ntf_tipo_alerta,ntf_notificacao,ntf_url,ntf_lida,
								REPLACE(ntf_data_hora, ' ', 'T') as 'ntf_data_hora',
								REPLACE(ntf_incdate, ' ', 'T') as 'ntf_incdate',REPLACE(ntf_upddate, ' ', 'T') as 'ntf_upddate'
								FROM notificacao
								WHERE ntf_dest_user_id = ?
									AND ntf_lida = 'N'
								ORDER BY ntf_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ntf_dest_user_id	, PDO::PARAM_INT);
				$stmt->execute();
				$ddaNotificacao	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $ddaNotificacao;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}
		
	}
?>