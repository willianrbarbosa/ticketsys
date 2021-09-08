<?php
	class PerfilAcessoRotinaDAO extends Security {
		
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oPerfilAcessoRotina){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

		    $asFields = $oPerfilAcessoRotina->getArrayofFields();
		    array_push($asFields, 'pta_incdate', 'pta_upddate','pta_delete');
		    $amValues = $oPerfilAcessoRotina->getArrayofValues();
		    array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('perfil_rotina',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;	
			} else {
				echo $this->cReturnMsg;
				return false;	
			}
		}

		public function Deleta($oPerfilAcessoRotina){
			$this->cReturnMsg 	= '';

		    $asFields = array();
		    array_push($asFields, 'pta_delete','pta_deldate', 'pta_deluser');
		    $amValues = array();
		    array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getuser_nome());
		    $aUpdKeys = array('pta_rot_nome = ', ' AND pta_pfa_id = ');
		    $aUpdValues = array($oPerfilAcessoRotina->getpta_rot_nome(), $oPerfilAcessoRotina->getpta_pfa_id());

			if ( $this->update_data('perfil_rotina',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function ApagaByPerfil($pta_pfa_id){
			$this->cReturnMsg 	= '';

		    $aUpdKeys = array('pta_pfa_id = ');
		    $aUpdValues = array($pta_pfa_id);

			if ( $this->erase_data('perfil_rotina',$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function buscaRotinas(){
			try{				
				$this->sql = "SELECT 
								rot_nome,rot_descricao
								FROM rotina
								ORDER BY rot_descricao";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aPerfilRotinas	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfilRotinas;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function loadRotinaSelecionadasPerfil($pta_pfa_id){
			try{				
				$this->sql = "SELECT 
								rot_nome,rot_descricao,
								CASE WHEN pta_nivel IS NULL THEN 0 ELSE pta_nivel END AS 'nivel',
								CASE WHEN pta_pfa_id IS NOT NULL THEN 'true' END AS selecionado
								FROM rotina
								LEFT JOIN perfil_rotina ON
									pta_rot_nome = rot_nome
									AND pta_pfa_id = ?
								ORDER BY rot_descricao";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $pta_pfa_id	, PDO::PARAM_INT);

				$stmt->execute();
				$aPerfilRotinas	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfilRotinas;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaAll(){
			try{				
				$this->sql = "SELECT 
								pta_rot_nome,pta_pfa_id,pta_nivel,pta_user_atrib,
								REPLACE(pta_incdate, ' ', 'T') as 'pta_incdate',REPLACE(pta_upddate, ' ', 'T') as 'pta_upddate'
								FROM perfil_rotina
								WHERE pta_delete = ''
								ORDER BY pta_pfa_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$aPerfilRotinas	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfilRotinas;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByRotina($pta_rot_nome){
			try{				
				$this->sql = "SELECT 
								pta_rot_nome,pta_pfa_id,pta_nivel,pta_user_atrib,
								REPLACE(pta_incdate, ' ', 'T') as 'pta_incdate',REPLACE(pta_upddate, ' ', 'T') as 'pta_upddate'
								FROM perfil_rotina
								WHERE pta_delete = '' 
									AND pta_rot_nome = ?
								ORDER BY pta_pfa_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $pta_rot_nome	, PDO::PARAM_INT);
				$stmt->execute();
				$aPerfilRotinas	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfilRotinas;
			}catch ( PDOException $ex ){ 
				$this->conex = null;
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByPerfil($pta_pfa_id){
			try{				
				$this->sql = "SELECT 
								pta_rot_nome,pta_pfa_id,pta_nivel,pta_user_atrib,
								REPLACE(pta_incdate, ' ', 'T') as 'pta_incdate',REPLACE(pta_upddate, ' ', 'T') as 'pta_upddate'
								FROM perfil_rotina
								WHERE pta_delete = '' 
									AND pta_pfa_id = ?
								ORDER BY pta_pfa_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $pta_pfa_id	, PDO::PARAM_STR);
				$stmt->execute();
				$aPerfilRotinas	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfilRotinas;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByPerfilRotina($pta_rot_nome, $pta_pfa_id){
			try{				
				$this->sql = "SELECT 
								pta_rot_nome,pta_pfa_id,pta_nivel,pta_user_atrib,
								REPLACE(pta_incdate, ' ', 'T') as 'pta_incdate',REPLACE(pta_upddate, ' ', 'T') as 'pta_upddate'
								FROM perfil_rotina
								WHERE pta_delete = '' 
									AND pta_rot_nome = ?
									AND pta_pfa_id = ?
								ORDER BY pta_pfa_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $pta_rot_nome	, PDO::PARAM_STR);
				$stmt->bindValue(2,  $pta_pfa_id	, PDO::PARAM_INT);
				$stmt->execute();
				$aPerfilRotinas	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aPerfilRotinas;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function CheckUserLoggedRotina($pta_rot_nome,$pta_pfa_id){
			try{				
				$this->sql = "SELECT 
								pta_rot_nome,pta_pfa_id,pta_nivel,pta_user_atrib,
								REPLACE(pta_incdate, ' ', 'T') as 'pta_incdate',REPLACE(pta_upddate, ' ', 'T') as 'pta_upddate'
								FROM perfil_rotina
								WHERE pta_delete = '' 
									AND pta_rot_nome = ?
									AND pta_pfa_id = ?
								ORDER BY pta_pfa_id";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $pta_rot_nome				, PDO::PARAM_STR);
				$stmt->bindValue(2,  $pta_pfa_id				, PDO::PARAM_INT);
				$stmt->execute();
				$aUserRotina	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $aUserRotina;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}
		
	}
?>