<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class TicketUsuariosDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oTicket_usuarios){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oTicket_usuarios->getArrayofFields();
			array_push($asFields, 'tku_incdate', 'tku_upddate','tku_delete');
			$amValues = $oTicket_usuarios->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('ticket_usuarios',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oTicket_usuarios){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_usuarios->getArrayofFields();
			array_push($asFields, 'tku_upddate');
			$amValues = $oTicket_usuarios->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('tku_id = ');
			$aUpdValues = array($oTicket_usuarios->gettku_id());

			if ( $this->update_data('ticket_usuarios',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oTicket_usuarios){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_usuarios->getArrayofFields();
			array_push($asFields, 'tku_delete','tku_deldate', 'tku_deluser');
			$amValues = $oTicket_usuarios->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tku_id = ');
			$aUpdValues = array($oTicket_usuarios->gettku_id());

			if ( $this->update_data('ticket_usuarios',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function ApagaPorTicketTipo($tku_tkt_id, $tku_tipo){
			$this->cReturnMsg 	= '';

			$aUpdKeys = array('tku_tkt_id = ', ' AND tku_tipo = ');
			$aUpdValues = array($tku_tkt_id, $tku_tipo);

			if ( $this->erase_data('ticket_usuarios',$aUpdKeys, $aUpdValues, $this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oTicket_usuarios){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_usuarios->getArrayofFields();
			array_push($asFields, 'tku_delete','tku_deldate', 'tku_deluser');
			$amValues = $oTicket_usuarios->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tku_id = ');
			$aUpdValues = array($oTicket_usuarios->gettku_id());

			if ( $this->update_data('ticket_usuarios',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tku_id,tku_tkt_id,tku_user_id,tku_tipo,tku_notif_email,tku_notif_sistema,tku_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tku_incdate, ' ', 'T') as 'tku_incdate',
								REPLACE(tku_upddate, ' ', 'T') as 'tku_upddate',
								REPLACE(tku_deldate, ' ', 'T') as 'tku_deldate',tku_deluser
								FROM ticket_usuarios
								LEFT JOIN usuario ON
									user_id = tku_user_id
									AND user_delete = ''
								WHERE tku_delete = ''
									".$cWhere." 
								ORDER BY tku_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_usuarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_usuarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tku_id,tku_tkt_id,tku_user_id,tku_tipo,tku_notif_email,tku_notif_sistema,tku_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tku_incdate, ' ', 'T') as 'tku_incdate',
								REPLACE(tku_upddate, ' ', 'T') as 'tku_upddate',
								REPLACE(tku_deldate, ' ', 'T') as 'tku_deldate',tku_deluser
								FROM ticket_usuarios
								LEFT JOIN usuario ON
									user_id = tku_user_id
									AND user_delete = ''
								WHERE tku_delete = '*'
									".$cWhere." 
								ORDER BY tku_id";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_usuarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_usuarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($tku_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tku_id,tku_tkt_id,tku_user_id,tku_tipo,tku_notif_email,tku_notif_sistema,tku_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tku_incdate, ' ', 'T') as 'tku_incdate',
								REPLACE(tku_upddate, ' ', 'T') as 'tku_upddate',
								REPLACE(tku_deldate, ' ', 'T') as 'tku_deldate',tku_deluser
								FROM ticket_usuarios
								LEFT JOIN usuario ON
									user_id = tku_user_id
									AND user_delete = ''
								WHERE tku_delete = ''
									AND tku_id = ? 
									".$cWhere." 
								ORDER BY tku_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tku_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_usuarios	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_usuarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTicket($tku_tkt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tku_id,tku_tkt_id,tku_user_id,tku_tipo,tku_notif_email,tku_notif_sistema,tku_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tku_incdate, ' ', 'T') as 'tku_incdate',
								REPLACE(tku_upddate, ' ', 'T') as 'tku_upddate',
								REPLACE(tku_deldate, ' ', 'T') as 'tku_deldate',tku_deluser
								FROM ticket_usuarios
								LEFT JOIN usuario ON
									user_id = tku_user_id
									AND user_delete = ''
								WHERE tku_delete = ''
									AND tku_tkt_id = ? 
									".$cWhere." 
								ORDER BY tku_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tku_tkt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_usuarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_usuarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByUser_id($tku_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tku_id,tku_tkt_id,tku_user_id,tku_tipo,tku_notif_email,tku_notif_sistema,tku_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tku_incdate, ' ', 'T') as 'tku_incdate',
								REPLACE(tku_upddate, ' ', 'T') as 'tku_upddate',
								REPLACE(tku_deldate, ' ', 'T') as 'tku_deldate',tku_deluser
								FROM ticket_usuarios
								LEFT JOIN usuario ON
									user_id = tku_user_id
									AND user_delete = ''
								WHERE tku_delete = ''
									AND tku_user_id = ? 
									".$cWhere." 
								ORDER BY tku_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tku_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_usuarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_usuarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTicketUserID($tku_tkt_id, $tku_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tku_id,tku_tkt_id,tku_user_id,tku_tipo,tku_notif_email,tku_notif_sistema,tku_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tku_incdate, ' ', 'T') as 'tku_incdate',
								REPLACE(tku_upddate, ' ', 'T') as 'tku_upddate',
								REPLACE(tku_deldate, ' ', 'T') as 'tku_deldate',tku_deluser
								FROM ticket_usuarios
								LEFT JOIN usuario ON
									user_id = tku_user_id
									AND user_delete = ''
								WHERE tku_delete = ''
									AND tku_tkt_id = ? 
									AND tku_user_id = ? 
									".$cWhere." 
								ORDER BY tku_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tku_tkt_id	, PDO::PARAM_INT);
				$stmt->bindValue(2,  $tku_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_usuarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_usuarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTipo($tku_tkt_id, $tku_tipo, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tku_id,tku_tkt_id,tku_user_id,tku_tipo,tku_notif_email,tku_notif_sistema,tku_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tku_incdate, ' ', 'T') as 'tku_incdate',
								REPLACE(tku_upddate, ' ', 'T') as 'tku_upddate',
								REPLACE(tku_deldate, ' ', 'T') as 'tku_deldate',tku_deluser
								FROM ticket_usuarios
								LEFT JOIN usuario ON
									user_id = tku_user_id
									AND user_delete = ''
								WHERE tku_delete = ''
									AND tku_tkt_id = ? 
									AND tku_tipo = ? 
									".$cWhere." 
								ORDER BY tku_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tku_tkt_id	, PDO::PARAM_INT);
				$stmt->bindValue(2,  $tku_tipo		, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket_usuarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_usuarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTicketUserIDTipo($tku_tkt_id, $tku_user_id, $tku_tipo, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tku_id,tku_tkt_id,tku_user_id,tku_tipo,tku_notif_email,tku_notif_sistema,tku_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tku_incdate, ' ', 'T') as 'tku_incdate',
								REPLACE(tku_upddate, ' ', 'T') as 'tku_upddate',
								REPLACE(tku_deldate, ' ', 'T') as 'tku_deldate',tku_deluser
								FROM ticket_usuarios
								LEFT JOIN usuario ON
									user_id = tku_user_id
									AND user_delete = ''
								WHERE tku_delete = ''
									AND tku_tkt_id = ? 
									AND tku_user_id = ? 
									AND tku_tipo = ? 
									".$cWhere." 
								ORDER BY tku_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tku_tkt_id	, PDO::PARAM_INT);
				$stmt->bindValue(2,  $tku_user_id	, PDO::PARAM_INT);
				$stmt->bindValue(3,  $tku_tipo		, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket_usuarios	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_usuarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
