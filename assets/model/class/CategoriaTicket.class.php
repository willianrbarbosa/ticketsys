<?php
	class CategoriaTicket {
		private $cgt_id;
		private $cgt_descricao;

		public function __construct($cgt_id,$cgt_descricao){
			$this->cgt_id  = $cgt_id;
			$this->cgt_descricao  = $cgt_descricao;
		}

		public function setcgt_id($cgt_id) { $this->cgt_id = $cgt_id; }
		public function getcgt_id() { return $this->cgt_id; }

		public function setcgt_descricao($cgt_descricao) { $this->cgt_descricao = $cgt_descricao; }
		public function getcgt_descricao() { return $this->cgt_descricao; }


		public function getArrayofFields() {
			return array(
				'cgt_id',
				'cgt_descricao',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->getcgt_id(),
				$this->getcgt_descricao(),
			);
		}
	}
?>
