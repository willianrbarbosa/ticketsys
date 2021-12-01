<?php
	namespace TicketSys\Model\Classes;
	
	class OrigemTicket {
		private $ort_id;
		private $ort_descricao;

		public function __construct($ort_id,$ort_descricao){
			$this->ort_id  = $ort_id;
			$this->ort_descricao  = $ort_descricao;
		}

		public function setort_id($ort_id) { $this->ort_id = $ort_id; }
		public function getort_id() { return $this->ort_id; }

		public function setort_descricao($ort_descricao) { $this->ort_descricao = $ort_descricao; }
		public function getort_descricao() { return $this->ort_descricao; }


		public function getArrayofFields() {
			return array(
				'ort_id',
				'ort_descricao',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->getort_id(),
				$this->getort_descricao(),
			);
		}
	}
?>
