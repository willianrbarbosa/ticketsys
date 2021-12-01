<?php	
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class Security extends UserSession {
		/**
		* Sistema de segurança com acesso restrito
		* @author Willian Barbosa | © Copyright 2016, Developed by Codeware SI | All rights reserved.
		* @version 1.0
		* @package SecuritySystem
		*/
		//  Configurações do Script
		// ==============================
		public $base_patch 		= '/ticketsys';
		public $caseSensitive 	= false;
														
		public $dbType 			= "mysql";
		public $servidor 		= 'localhost';
		public $bduser 			= 'root';
		public $bdpasswd		= '';
		public $database		= 'tickets';
		public $persistent 		= false;

		//Variáveis de parâmetros de e-mail		
		public $ServerName	= 'Ticket SYS';
		public $emailenviar	= 'sistema@email.com.br';
		public $cSenhaEmail	= '';
		public $cHost		= 'mail.email.com.br';
		public $cPort		= '587';

		public $conn = null;

		public function __construct(){
			$this->getConnection();
		}

		public function getConnection(){		
			try{
				$this->conn = new PDO($this->dbType.":host=".$this->servidor.";dbname=".$this->database, $this->bduser, $this->bdpasswd,
				array( PDO::ATTR_PERSISTENT => $this->persistent, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'') );
		        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
				return $this->conn;

			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
			}
		}

		public function Close(){
			if( $this->conn != null )
				$this->conn = null;
		}

		/**
		* Função que valida um usuário e senha
		* @param string $user 	- O usuário a ser validado
		* @param string $passwd 		- A senha a ser validada
		* @param String $cMsg 		- Retorna a mensagem de erro de acordo com a validação
		* @return bool 				- Se o usuário foi validado ou não (true/false)
		*/
		public function userValid($user, $passwd, $lCript, &$cMsg) {
			$cS = ($this->caseSensitive) ? 'BINARY' : '';
			$nUser = addslashes($user);
			$nPasswd = ($lCript ? MD5(addslashes($passwd)) : addslashes($passwd));
			$sql = "SELECT 
					user_id,user_nome,user_email,user_passwd,user_pfa_id,user_tipo,user_token,user_delete
					FROM usuario WHERE ".$cS." user_email = '".$nUser."' AND user_delete = '' AND user_ativo = 'S'";
			
			$stmt = $this->conn->prepare($sql);
		    $stmt->execute();
			if ($stmt->rowCount() <= 0) {
				// Nenhum registro foi encontrado => o usuário é inválido
				$cMsg = 'Usuário não cadastrado ou inativo na base de dados. Verifique!';
				return false;
			} else {
				$aUser = $stmt->fetch();
				//Valida a senha criptografada
				if ( $nPasswd == $aUser['user_passwd']){
					$this->setUser_id($aUser['user_id']); 
					$this->setUser_nome($aUser['user_nome']);
					$this->setUser_email($aUser['user_email']);
					$this->setUser_tipo($aUser['user_tipo']);
					$this->setUser_pfa_id($aUser['user_tipo'] != 3 ? $aUser['user_pfa_id'] : null);
					$this->setUser_token($aUser['user_token']);	
						
					$cMsg = 'Logado com sucesso!';
					return true;
				} else { 
					//Senha incorreta;
					$cMsg = 'A senha digitada está incorreta. Verifique!';
					return false;	
				}
			}
		}

		/**
		* Função que verifica se e-mail já está cadastrado
		* @param string $Email 	- E-mail a ser validado\
		* @return bool 			- Se o e-mail foi validade ou não (true/false)
		*/
		public function validUserEmail($UserEmail) {
			$cS = ($this->caseSensitive) ? 'BINARY' : '';
			// Usa a função addslashes para escapar as aspas
			$cUserEmail = addslashes($UserEmail);
			// Monta uma consulta SQL (query) para procurar um usuário para o e-mail informado.
			$sql = "SELECT user_id,user_nome,user_email,user_passwd,user_tipo,user_token,user_delete FROM usuario WHERE ".$cS." user_email = '".$cUserEmail."' AND user_delete = '' ";
			
			$stmt = $this->conn->prepare($sql);
		    $stmt->execute();
		    $cont = $stmt->rowCount();
			// Verifica se encontrou algum registro
			if ($cont <= 0) {
				// Nenhum registro foi encontrado => e-mail não cadastrado
				return true;
			} else { 
				return false;	
			}
		}

		/**
		* Função que gera o token de login do usuário
		* @param string $userEmail 	- E-mail a do usuário
		* @return bool
		*/
		public function newUserToken($userEmail) {
			try{			    
				$this->conn->beginTransaction();

			    $tokenGeneric = MD5(Date('yyyymmdd').$userEmail.time());
			    $cToken = substr(preg_replace("/[^0-9]/", "", hash('sha256', $tokenGeneric)),1,20);
		    
				$sql = "UPDATE usuario SET user_token = ? WHERE user_email = ? ";

				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue(1,  $cToken				, PDO::PARAM_STR);
				$stmt->bindValue(2,  addslashes($userEmail)	, PDO::PARAM_STR);

			    $stmt->execute();
				$this->conn->commit();
				$this->setUser_token($cToken);

			    return true;
			}catch ( PDOException $ex ){
				$this->conn->rollback();
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function getDWConnection(){		
			try{
				$this->DW_conn = new PDO($this->DW_dbType.":host=".$this->DW_servidor.";dbname=".$this->DW_database, $this->DW_bduser, $this->DW_bdpasswd,
				array( PDO::ATTR_PERSISTENT => $this->persistent, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'') );
		        $this->DW_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
				return $this->DW_conn;

			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
			}
		}

		public function CloseDWConn(){
			if( $this->DW_conn != null )
				$this->DW_conn = null;
		}

		/**
		* Função que insere os dados na base
		* @param string $ssTableName 	- Nome da tabela
		* @param string $asFields 		- Campos de inclusão
		* @param string $amValues 		- Valores para inclusão
		* @param string $nInsertID 		- Retorna o ID inserido
		* @param string $cReturnMsg 	- Retorna uma mensagem de erro, caso ocorra
		* @return bool
		*/
	    public function insert_data($ssTableName,$asFields = array(),$amValues = array(),&$nInsertID,&$cReturnMsg) {	
			if( (Empty($ssTableName)) OR (Empty($asFields)) OR (Empty($amValues)) ) {
				return false;
			} elseif ( count($asFields) <> count($amValues) ) {
				$cReturnMsg = 'Qtde. de campos da tabela \'asFields\' difere dos valores para inserção \'amValues\'.';
				return false;
			} else {
				try{
					$cFields = '';
					for ($i=0; $i < count($asFields); $i++) { 
						if ( !Empty($cFields) ) {	$cFields .= ',';	}
						$cFields .= '?';
					}			
					$this->conn->beginTransaction();

					$sql = "INSERT INTO ".$ssTableName." (".implode(', ', $asFields).") VALUES (".$cFields.")";
					$stmt = $this->conn->prepare($sql);
					for ($i=0; $i < count($amValues); $i++) { 						
						$stmt->bindValue(($i+1),  $amValues[$i], (gettype($amValues[$i]) == 'integer' ? PDO::PARAM_INT : PDO::PARAM_STR));
					}
					$stmt->execute();
				
					$nInsertID = $this->conn->lastInsertId(); 	
					$this->conn->commit();
					return true;
				}catch ( PDOException $ex ){ 	
					$cReturnMsg = "Query Error: ".$ex->getMessage();
					$this->conn->rollback();
					return false;
				}
			}
		}

		/**
		* Função que atualiza os dados na base
		* @param string $ssTableName 	- Nome da tabela
		* @param string $asFields 		- Campos de atualização
		* @param string $amValues 		- Valores para atualização
		* @param string $aUpdKeys 		- Chaves para atualização (WHERE)
		* @param string $aUpdValues		- Valores para Chaves para atualização (WHERE)
		* @param string $cReturnMsg 	- Retorna uma mensagem de erro, caso ocorra
		* @return bool
		*/
	    public function update_data($ssTableName,$asFields = array(),$amValues = array(),$aUpdKeys = array(), $aUpdValues = array(),&$cReturnMsg) {	
			if( (Empty($ssTableName)) OR (Empty($asFields)) OR (Empty($amValues)) ) {
				return false;
			} elseif ( count($asFields) <> count($amValues) ) {
				$cReturnMsg = 'Qtde. de campos da tabela \'asFields\' difere dos valores para inserção \'amValues\'.';
				return false;
			} elseif ( count($aUpdKeys) <> count($aUpdValues) ) {
				$cReturnMsg = 'Qtde. de campos das Keys \'aUpdKeys\' difere dos valores para inserção \'aUpdValues\'.';
				return false;
			} else {
				try{				
					$this->conn->beginTransaction();

					$sql = "UPDATE ".$ssTableName." SET ".implode(' = ?, ', $asFields)." = ? WHERE ".implode(' ? ', $aUpdKeys)." ?";					
					$stmt = $this->conn->prepare($sql);
					for ($i=0; $i < count($amValues); $i++) { 						
						$stmt->bindValue(($i+1),  $amValues[$i], (gettype($amValues[$i]) == 'integer' ? PDO::PARAM_INT : PDO::PARAM_STR));
					}
					for ($k=0; $k < count($aUpdValues); $k++) { 						
						$stmt->bindValue(($k+$i+1),  $aUpdValues[$k], (gettype($aUpdValues[$k]) == 'integer' ? PDO::PARAM_INT : PDO::PARAM_STR));
					}
					$stmt->execute();
					
					$this->conn->commit();
					$cReturnMsg = 'alterado';

					return true;
				}catch ( PDOException $ex ){ 	
					$cReturnMsg = "Query Error: ".$ex->getMessage();
					$this->conn->rollback();
					return false;
				}
			}
		}

		/**
		* Função que APAGA os dados na base
		* @param string $ssTableName 	- Nome da tabela
		* @param string $aUpdKeys 		- Chaves para DELETE (WHERE)
		* @param string $aUpdValues		- Valores para Chaves para DELETE (WHERE)
		* @param string $cReturnMsg 	- Retorna uma mensagem de erro, caso ocorra
		* @return bool
		*/
	    public function erase_data($ssTableName,$aUpdKeys = array(),$aUpdValues = array(),&$cReturnMsg) {	
			if( (Empty($ssTableName)) OR (Empty($aUpdKeys)) OR (Empty($aUpdValues)) ) {
				return false;
			} else {
				try{			
					$this->conn->beginTransaction();

					$sql = "DELETE FROM ".$ssTableName." WHERE ".implode(' ? ', $aUpdKeys)." ?";					
					$stmt = $this->conn->prepare($sql);
					for ($k=0; $k < count($aUpdValues); $k++) { 						
						$stmt->bindValue(($k+1),  $aUpdValues[$k], (gettype($aUpdValues[$k]) == 'integer' ? PDO::PARAM_INT : PDO::PARAM_STR));
					}
					$stmt->execute();
					
					$this->conn->commit();
					$cReturnMsg = 'apagado';

					return true;
				}catch ( PDOException $ex ){ 	
					$cReturnMsg = "Query Error: ".$ex->getMessage();
					$this->conn->rollback();
					return false;
				}
			}
		}

		public function sanitizeString($str) {
			$str = preg_replace('/[áàãâä]/ui', 'a', $str);
			$str = preg_replace('/[éèêë]/ui', 'e', $str);
			$str = preg_replace('/[íìîï]/ui', 'i', $str);
			$str = preg_replace('/[óòõôö]/ui', 'o', $str);
			$str = preg_replace('/[úùûü]/ui', 'u', $str);
			$str = preg_replace('/[ç]/ui', 'c', $str);
			// $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
			// $str = preg_replace('/[^a-z0-9]/i', '_', $str);
			// $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
			return $str;
		}

		public function OnlyNumbers($str) {
			$str = preg_replace("/[^0-9]/", "", $str);
			return $str;
		}

		/**
		* Função que protege uma página
		*/
		public function PageSafe() {
			if ($this->Exist()){
				if($this->getUser_id() <> NULL OR $this->getUser_nome() <> NULL){
					return true;
				}
			} else {
				return false;
			}
		}
		/**
		* Função para expulsar um visitante
		*/
		public function goIndex() {
			$this->Destroy();
			// Manda pra tela de login
			header("Location: ../../#/login");
		}
		
	}
?>