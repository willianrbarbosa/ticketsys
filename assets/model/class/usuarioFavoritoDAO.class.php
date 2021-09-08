<?php
	class usuarioFavoritoDAO extends Security {
		
		public $conex = null;
		public $sql = '';
		public $id_inserido;
		public $cReturnMsg;

		public function __construct(){
			date_default_timezone_set('America/Sao_Paulo');
			$this->conex = Security::getConnection();
		}

		public function Insere($oUserFavorite){
			$this->cReturnMsg 	= '';
			$this->id_inserido 	= 0;

		    $asFields = $oUserFavorite->getArrayofFields();
		    array_push($asFields, 'ufv_incdate', 'ufv_upddate','ufv_delete');
		    $amValues = $oUserFavorite->getArrayofValues();
		    array_push($amValues, Date('Y-m-d H:i:s'), Date('Y-m-d H:i:s'), '');

			if ( $this->insert_data('user_favoritos',$asFields,$amValues,$this->id_inserido,$this->cReturnMsg) ) {
				return true;
			} else {
				echo $this->cReturnMsg;
				return false;	
			}
		}

		public function Altera($oUserFavorite){
			$this->cReturnMsg 	= '';

		    $asFields = $oUserFavorite->getArrayofFields();
		    array_push($asFields, 'ufv_upddate');
		    $amValues = $oUserFavorite->getArrayofValues();
		    array_push($amValues,Date('Y-m-d H:i:s'));
		    $aUpdKeys = array('ufv_id = ');
		    $aUpdValues = array($oUserFavorite->getufv_id());

			if ( $this->update_data('user_favoritos',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
			
		}

		public function Deleta($oUserFavorite){
			$this->cReturnMsg 	= '';

		    $asFields = array();
		    array_push($asFields, 'ufv_delete','ufv_deldate', 'ufv_deluser');
		    $amValues = array();
		    array_push($amValues, '*', Date('Y-m-d H:i:s'), $this->getuser_nome());
		    $aUpdKeys = array('ufv_id = ');
		    $aUpdValues = array($oUserFavorite->getufv_id());

			if ( $this->update_data('user_favoritos',$asFields,$amValues,$aUpdKeys, $aUpdValues,$this->cReturnMsg) ) {
				return true;
			} else {		
				echo $this->cReturnMsg;
				return false;		
			}
		}

		public function buscaAll(){
			try{				
				$this->sql = "SELECT 
								ufv_id,ufv_user_id,ufv_descricao,ufv_categoria,ufv_url,
								REPLACE(ufv_incdate, ' ', 'T') as 'ufv_incdate',REPLACE(ufv_upddate, ' ', 'T') as 'ufv_upddate'
								FROM user_favoritos
								WHERE ufv_delete = ''
								ORDER BY ufv_categoria, ufv_descricao";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->execute();
				$auserFavoritos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $auserFavoritos;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaById($ufv_id){
			try{				
				$this->sql = "SELECT 
								ufv_id,ufv_user_id,ufv_descricao,ufv_categoria,ufv_url,
								REPLACE(ufv_incdate, ' ', 'T') as 'ufv_incdate',REPLACE(ufv_upddate, ' ', 'T') as 'ufv_upddate'
								FROM user_favoritos
								WHERE ufv_delete = ''
									AND ufv_id = ?
								ORDER BY ufv_categoria, ufv_descricao";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ufv_id	, PDO::PARAM_INT);
				$stmt->execute();
				$auserFavoritos	= $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $auserFavoritos;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}

		public function buscaByUserId($ufv_user_id){
			try{				
				$this->sql = "SELECT 
								ufv_id,ufv_user_id,ufv_descricao,ufv_categoria,ufv_url,
								REPLACE(ufv_incdate, ' ', 'T') as 'ufv_incdate',REPLACE(ufv_upddate, ' ', 'T') as 'ufv_upddate'
								FROM user_favoritos
								WHERE ufv_delete = ''
									AND ufv_user_id = ?
								ORDER BY ufv_categoria, ufv_descricao";
				$stmt = $this->conex->prepare($this->sql);
				$stmt->bindValue(1,  $ufv_user_id	, PDO::PARAM_INT);
				$stmt->execute();
				$auserFavoritos	= $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				return $auserFavoritos;
			}catch ( PDOException $ex ){ 
				echo "Erro: ".$ex->getMessage(); 
				return false;
			}
		}
		
	}
?>