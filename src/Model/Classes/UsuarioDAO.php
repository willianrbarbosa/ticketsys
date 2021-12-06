<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;

	class UsuarioDAO extends Security {
		
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = $this->getConnection();
		}

		public function Insere($oUser){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

		    $asFields = $oUser->getArrayofFields();
		    array_push($asFields, 'user_incdate', 'user_upddate','user_delete');
		    $amValues = $oUser->getArrayofValues();
		    array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('usuario',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;	
			} else {
				echo $this->cReturnMsg;
				return false;	
			}
		}

		public function Altera($oUser){
			$this->cReturnMsg 	= '';

		    $asFields = $oUser->getArrayofFields();
		    array_push($asFields, 'user_upddate');
		    $amValues = $oUser->getArrayofValues();
		    array_push($amValues,Date('Y-m-d H:i:s'));
		    $aUpdKeys = array('user_id = ');
		    $aUpdValues = array($oUser->getuser_id());

			if ( $this->update_data('usuario',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
			
		}

		public function Deleta($oUser){
			$this->cReturnMsg 	= '';

		    $asFields = array();
		    array_push($asFields, 'user_delete','user_deldate', 'user_deluser');
		    $amValues = array();
		    array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getuser_nome());
		    $aUpdKeys = array('user_id = ');
		    $aUpdValues = array($oUser->getuser_id());

			if ( $this->update_data('usuario',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function Inativa($oUser){
			$this->cReturnMsg 	= '';

		    $asFields = array();
		    array_push($asFields,'user_ativo','user_token','user_upddate');
		    $amValues = array();
		    array_push($amValues,'N',null, Date('Y-m-d H:i:s'));
		    $aUpdKeys = array('user_id = ');
		    $aUpdValues = array($oUser->getuser_id());

			if ( $this->update_data('usuario',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function Ativa($oUser){
			$this->cReturnMsg 	= '';

		    $asFields = array();
		    array_push($asFields,'user_token','user_ativo','user_upddate');
		    $amValues = array();
		    array_push($amValues,$oUser->getuser_token(),'S', Date('Y-m-d H:i:s'));
		    $aUpdKeys = array('user_id = ');
		    $aUpdValues = array($oUser->getuser_id());

			if ( $this->update_data('usuario',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function AlteraSenha($usrID, $newPass){
			$this->cReturnMsg 	= '';

		    $asFields = array('user_passwd');
		    $amValues = array($newPass);
		    $aUpdKeys = array('user_id = ');
		    $aUpdValues = array($usrID);

			if ( $this->update_data('usuario',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function setUserTheme($user_id, $user_tema){
			$this->cReturnMsg 	= '';

		    $asFields = array('user_tema');
		    $amValues = array($user_tema);
		    $aUpdKeys = array('user_id = ');
		    $aUpdValues = array($user_id);

			if ( $this->update_data('usuario',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}			
		}

		public function buscaAll(){
			try{				
				$this->sql = "SELECT 
								user_id,user_nome,user_email,user_passwd,user_pfa_id,user_photo,user_tipo,user_token,user_ativo,user_tema,user_pst_id,user_resp_ticket,
								user_email_confirm,
								REPLACE(user_incdate, ' ', 'T') as 'user_incdate',REPLACE(user_upddate, ' ', 'T') as 'user_upddate'
								FROM usuario
								WHERE user_delete = ''
								AND user_ativo = 'S'	
								ORDER BY user_nome";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aUsers	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaInativos(){
			try{				
				$this->sql = "SELECT 
								user_id,user_nome,user_email,user_passwd,user_pfa_id,user_photo,user_tipo,user_token,user_ativo,user_tema,user_pst_id,user_resp_ticket,
								user_email_confirm,
								REPLACE(user_incdate, ' ', 'T') as 'user_incdate',REPLACE(user_upddate, ' ', 'T') as 'user_upddate'
								FROM usuario
								WHERE user_delete = ''
								AND user_ativo = 'N'	
								ORDER BY user_nome";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aUsers	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaById($user_id){
			try{				
				$this->sql = "SELECT 
								user_id,user_nome,user_email,user_passwd,user_pfa_id,user_photo,user_tipo,user_token,user_ativo,user_tema,user_pst_id,user_resp_ticket,
								user_email_confirm,
								REPLACE(user_incdate, ' ', 'T') as 'user_incdate',REPLACE(user_upddate, ' ', 'T') as 'user_upddate'
								FROM usuario
								WHERE user_delete = '' 
									AND user_id = ?
								ORDER BY user_nome";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $user_id	, PDO::PARAM_INT);
				$stmt->execute();
				$aUsers	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaUserAcesso($usa_user_id){
			try{				
				$this->sql = "SELECT 
								usa_id,usa_user_id,usa_user_email,usa_ip,
								REPLACE(usa_data, ' ', 'T') as 'usa_data'
								FROM usuario_acesso
								WHERE usa_user_id = ?
								ORDER BY usa_data DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $usa_user_id	, PDO::PARAM_INT);
				$stmt->execute();
				$aUsers	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaUsersAcessos(){
			try{				
				$this->sql = "SELECT 
								usa_id,usa_user_id,usa_user_email,usa_ip,
								REPLACE(usa_data, ' ', 'T') as 'usa_data'
								FROM usuario_acesso
								ORDER BY usa_data DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aUsers	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaUsersAcessosByFilter($usa_user_id,$data_de,$data_ate){
			try{				
				if(Empty($data_ate)){
					$data_ate = Date('Y-m-d');
				}

				if(($usa_user_id > 0) && (!Empty($data_de))){
					$cWhere = "WHERE usa_user_id = ".$usa_user_id." and usa_data between '".$data_de."' AND '".$data_ate."'";
				}elseif(($usa_user_id > 0) && (Empty($data_de)) AND ($data_ate == Date('Y-m-d'))){
					$cWhere = "WHERE usa_user_id = ".$usa_user_id;
				}elseif(($usa_user_id <= 0) && (!Empty($data_de))){
					$cWhere = "WHERE usa_data between '".$data_de."' AND '".$data_ate."'";
				}elseif(($usa_user_id > 0) AND (Empty($data_de)) AND ($data_de != Date('Y-m-d'))){
					$cWhere = "WHERE usa_user_id = ".$usa_user_id." AND usa_data <= '".$data_ate."'";
				}elseif((Empty($data_de)) AND ($data_de != Date('Y-m-d'))){
					$cWhere = "WHERE Date(usa_data) <= '".$data_ate."'";
				}elseif(($usa_user_id <= 0) AND (Empty($data_de)) AND ($data_ate == Date('Y-m-d'))){
					$cWhere = '';
				}
				$this->sql = "SELECT 
								usa_id,usa_user_id,usa_user_email,usa_ip,
								REPLACE(usa_data, ' ', 'T') as 'usa_data'
								FROM usuario_acesso
								".$cWhere."
								ORDER BY usa_data DESC";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aUsers	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByToken($user_token){
			try{				
				$this->sql = "SELECT 
								user_id,user_nome,user_email,user_passwd,user_pfa_id,user_photo,user_tipo,user_token,user_ativo,user_tema,user_pst_id,user_resp_ticket,
								user_email_confirm,
								REPLACE(user_incdate, ' ', 'T') as 'user_incdate',REPLACE(user_upddate, ' ', 'T') as 'user_upddate'
								FROM usuario
								WHERE user_delete = '' 
									AND user_token = ?
								ORDER BY user_nome";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $user_token	, PDO::PARAM_STR);
				$stmt->execute();
				$aUsers	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByEmail($user_email){
			try{				
				$this->sql = "SELECT 
								user_id,user_nome,user_email,user_passwd,user_pfa_id,user_photo,user_tipo,user_token,user_ativo,user_tema,user_pst_id,user_resp_ticket,
								user_email_confirm,
								REPLACE(user_incdate, ' ', 'T') as 'user_incdate',REPLACE(user_upddate, ' ', 'T') as 'user_upddate'
								FROM usuario
								WHERE user_delete = '' 
									AND user_email = ?
								ORDER BY user_nome";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $user_email	, PDO::PARAM_STR);
				$stmt->execute();
				$aUsers	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByTipo($user_tipo){
			try{				
				$this->sql = "SELECT 
								user_id,user_nome,user_email,user_passwd,user_pfa_id,user_photo,user_tipo,user_token,user_ativo,user_tema,user_pst_id,user_resp_ticket,
								user_email_confirm,
								REPLACE(user_incdate, ' ', 'T') as 'user_incdate',REPLACE(user_upddate, ' ', 'T') as 'user_upddate'
								FROM usuario
								WHERE user_delete 	= '' 
									AND user_ativo 	= 'S'
									AND user_tipo 	= ?
								ORDER BY user_nome";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $user_tipo	, PDO::PARAM_INT);
				$stmt->execute();
				$aUsers	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaRespTicket($user_resp_ticket){
			try{				
				$this->sql = "SELECT 
								user_id,user_nome,user_email,user_passwd,user_pfa_id,user_photo,user_tipo,user_token,user_ativo,user_tema,user_pst_id,user_resp_ticket,
								user_email_confirm,
								REPLACE(user_incdate, ' ', 'T') as 'user_incdate',REPLACE(user_upddate, ' ', 'T') as 'user_upddate'
								FROM usuario
								WHERE user_delete 	= '' 
									AND user_ativo 	= 'S'
									AND user_resp_ticket 	= ?
								ORDER BY user_nome";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $user_resp_ticket	, PDO::PARAM_STR);
				$stmt->execute();
				$aUsers	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUsers;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}
	}
?>