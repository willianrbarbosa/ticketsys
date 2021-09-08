<?php
	class usuarioFavorito {
		private $ufv_id;
		private $ufv_user_id;
		private $ufv_descricao;	
		private $ufv_categoria;	
		private $ufv_url;

		public function __construct($ufv_id,$ufv_user_id,$ufv_descricao,$ufv_categoria,$ufv_url){
			$this->ufv_id 			= $ufv_id;
			$this->ufv_user_id 		= $ufv_user_id;
			$this->ufv_descricao 	= $ufv_descricao;
			$this->ufv_categoria 	= $ufv_categoria;
			$this->ufv_url 			= $ufv_url;
		}	    

		public function setufv_id($ufv_id) { $this->ufv_id = $ufv_id; }
		public function getufv_id() { return $this->ufv_id; }

		public function setufv_user_id($ufv_user_id) { $this->ufv_user_id = $ufv_user_id; }
		public function getufv_user_id() { return $this->ufv_user_id; }

		public function setufv_descricao($ufv_descricao) { $this->ufv_descricao = $ufv_descricao; }
		public function getufv_descricao() { return $this->ufv_descricao; }

		public function setufv_categoria($ufv_categoria) { $this->ufv_categoria = $ufv_categoria; }
		public function getufv_categoria() { return $this->ufv_categoria; }

		public function setufv_url($ufv_url) { $this->ufv_url = $ufv_url; }
		public function getufv_url() { return $this->ufv_url; }

		public function getArrayofFields() {
			return array(
				'ufv_id',
				'ufv_user_id',
				'ufv_descricao',
				'ufv_categoria',
				'ufv_url'
			);
		}

		public function getArrayofValues() {
			return array(
				$this->getufv_id(),
				$this->getufv_user_id(),
				$this->getufv_descricao(),
				$this->getufv_categoria(),
				$this->getufv_url()				
			);
		}
	}
?>