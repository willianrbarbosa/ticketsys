<?php
	class TipoAtividade {
		private $tav_id;
		private $tav_descricao;

		public function __construct($tav_id,$tav_descricao){
			$this->tav_id  = $tav_id;
			$this->tav_descricao  = $tav_descricao;
		}

		public function settav_id($tav_id) { $this->tav_id = $tav_id; }
		public function gettav_id() { return $this->tav_id; }

		public function settav_descricao($tav_descricao) { $this->tav_descricao = $tav_descricao; }
		public function gettav_descricao() { return $this->tav_descricao; }


		public function getArrayofFields() {
			return array(
				'tav_id',
				'tav_descricao',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->gettav_id(),
				$this->gettav_descricao(),
			);
		}
	}
?>
