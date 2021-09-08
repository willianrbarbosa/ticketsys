<?php
	class PerfilAcesso {
		private $pfa_id;
		private $pfa_descricao;

		public function __construct($pfa_id,$pfa_descricao){
			$this->pfa_id 			= $pfa_id;
			$this->pfa_descricao 	= $pfa_descricao;
		}	    

		public function setpfa_id($pfa_id) { $this->pfa_id = $pfa_id; }
		public function getpfa_id() { return $this->pfa_id; }

		public function setpfa_descricao($pfa_descricao) { $this->pfa_descricao = $pfa_descricao; }
		public function getpfa_descricao() { return $this->pfa_descricao; }

		public function getArrayofFields() {
			return array(
				'pfa_id',
				'pfa_descricao'
			);
		}

		public function getArrayofValues() {
			return array(
				$this->getpfa_id(),
				$this->getpfa_descricao()
			);
		}
	}
?>