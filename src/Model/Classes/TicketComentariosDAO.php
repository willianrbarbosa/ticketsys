<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class TicketComentariosDAO extends Security {

		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oTicket_comentarios){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

			$asFields = $oTicket_comentarios->getArrayofFields();
			array_push($asFields, 'tkc_incdate', 'tkc_upddate','tkc_delete');
			$amValues = $oTicket_comentarios->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('ticket_comentarios',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Altera($oTicket_comentarios){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_comentarios->getArrayofFields();
			array_push($asFields, 'tkc_upddate');
			$amValues = $oTicket_comentarios->getArrayofValues();
			array_push($amValues, Date('Y-m-d H:i:s'));
			$aUpdKeys = array('tkc_id = ');
			$aUpdValues = array($oTicket_comentarios->gettkc_id());

			if ( $this->update_data('ticket_comentarios',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Deleta($oTicket_comentarios){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_comentarios->getArrayofFields();
			array_push($asFields, 'tkc_delete','tkc_deldate', 'tkc_deluser');
			$amValues = $oTicket_comentarios->getArrayofValues();
			array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tkc_id = ');
			$aUpdValues = array($oTicket_comentarios->gettkc_id());

			if ( $this->update_data('ticket_comentarios',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function Restaura($oTicket_comentarios){
			$this->cReturnMsg 	= '';

			$asFields = $oTicket_comentarios->getArrayofFields();
			array_push($asFields, 'tkc_delete','tkc_deldate', 'tkc_deluser');
			$amValues = $oTicket_comentarios->getArrayofValues();
			array_push($amValues, '', Date('Y-m-d H:i:s'), $this->getUser_nome());
			$aUpdKeys = array('tkc_id = ');
			$aUpdValues = array($oTicket_comentarios->gettkc_id());

			if ( $this->update_data('ticket_comentarios',$asFields,$amValues,$aUpdKeys,$aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {
				return false;
			}
		}

		public function buscaAll($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkc_id,tkc_tkt_id,tkc_user_id,tkc_data_hora,REPLACE(tkc_data_hora, ' ', 'T') AS tkc_data_hora_comp,tkc_descricao,tkc_tipo,tkc_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkc_incdate, ' ', 'T') as 'tkc_incdate',
								REPLACE(tkc_upddate, ' ', 'T') as 'tkc_upddate',
								REPLACE(tkc_deldate, ' ', 'T') as 'tkc_deldate',tkc_deluser
								FROM ticket_comentarios
								LEFT JOIN usuario ON
									user_id = tkc_user_id
									AND user_delete = ''
								WHERE tkc_delete = ''
									".$cWhere." 
								ORDER BY tkc_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_comentarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_comentarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaAllDeleted($cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkc_id,tkc_tkt_id,tkc_user_id,tkc_data_hora,REPLACE(tkc_data_hora, ' ', 'T') AS tkc_data_hora_comp,tkc_descricao,tkc_tipo,tkc_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkc_incdate, ' ', 'T') as 'tkc_incdate',
								REPLACE(tkc_upddate, ' ', 'T') as 'tkc_upddate',
								REPLACE(tkc_deldate, ' ', 'T') as 'tkc_deldate',tkc_deluser
								FROM ticket_comentarios
								LEFT JOIN usuario ON
									user_id = tkc_user_id
									AND user_delete = ''
								WHERE tkc_delete = '*'
									".$cWhere." 
								ORDER BY tkc_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);

				$stmt->execute();
				$aTicket_comentarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_comentarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByID($tkc_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkc_id,tkc_tkt_id,tkc_user_id,tkc_data_hora,REPLACE(tkc_data_hora, ' ', 'T') AS tkc_data_hora_comp,tkc_descricao,tkc_tipo,tkc_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkc_incdate, ' ', 'T') as 'tkc_incdate',
								REPLACE(tkc_upddate, ' ', 'T') as 'tkc_upddate',
								REPLACE(tkc_deldate, ' ', 'T') as 'tkc_deldate',tkc_deluser
								FROM ticket_comentarios
								LEFT JOIN usuario ON
									user_id = tkc_user_id
									AND user_delete = ''
								WHERE tkc_delete = ''
									AND tkc_id = ? 
									".$cWhere." 
								ORDER BY tkc_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkc_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_comentarios	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_comentarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByTicket($tkc_tkt_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkc_id,tkc_tkt_id,tkc_user_id,tkc_data_hora,REPLACE(tkc_data_hora, ' ', 'T') AS tkc_data_hora_comp,tkc_descricao,tkc_tipo,tkc_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkc_incdate, ' ', 'T') as 'tkc_incdate',
								REPLACE(tkc_upddate, ' ', 'T') as 'tkc_upddate',
								REPLACE(tkc_deldate, ' ', 'T') as 'tkc_deldate',tkc_deluser
								FROM ticket_comentarios
								LEFT JOIN usuario ON
									user_id = tkc_user_id
									AND user_delete = ''
								WHERE tkc_delete = ''
									AND tkc_tkt_id = ? 
									".$cWhere." 
								ORDER BY tkc_data_hora";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkc_tkt_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_comentarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_comentarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByUser_id($tkc_user_id, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkc_id,tkc_tkt_id,tkc_user_id,tkc_data_hora,REPLACE(tkc_data_hora, ' ', 'T') AS tkc_data_hora_comp,tkc_descricao,tkc_tipo,tkc_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkc_incdate, ' ', 'T') as 'tkc_incdate',
								REPLACE(tkc_upddate, ' ', 'T') as 'tkc_upddate',
								REPLACE(tkc_deldate, ' ', 'T') as 'tkc_deldate',tkc_deluser
								FROM ticket_comentarios
								LEFT JOIN usuario ON
									user_id = tkc_user_id
									AND user_delete = ''
								WHERE tkc_delete = ''
									AND tkc_user_id = ? 
									".$cWhere." 
								ORDER BY tkc_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkc_user_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aTicket_comentarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_comentarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

		public function buscaByData_hora($tkc_data_hora, $cWhere = ''){
			$cWhere = (!Empty($cWhere) ? 'AND' : '').$cWhere;

			try{
				$this->sql = "SELECT
								tkc_id,tkc_tkt_id,tkc_user_id,tkc_data_hora,REPLACE(tkc_data_hora, ' ', 'T') AS tkc_data_hora_comp,tkc_descricao,tkc_tipo,tkc_delete,
								user_id,user_nome,user_email,user_photo,user_tipo,
								REPLACE(tkc_incdate, ' ', 'T') as 'tkc_incdate',
								REPLACE(tkc_upddate, ' ', 'T') as 'tkc_upddate',
								REPLACE(tkc_deldate, ' ', 'T') as 'tkc_deldate',tkc_deluser
								FROM ticket_comentarios
								LEFT JOIN usuario ON
									user_id = tkc_user_id
									AND user_delete = ''
								WHERE tkc_delete = ''
									AND tkc_data_hora = ? 
									".$cWhere." 
								ORDER BY tkc_data_hora DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $tkc_data_hora	, PDO::PARAM_STR);

				$stmt->execute();
				$aTicket_comentarios	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aTicket_comentarios;
			}catch ( PDOException $ex ){ 
				$this->cReturnMsg  = "Erro: ".$ex->getMessage();
				return false;
			}
		}

	}
?>
