<?php
	namespace TicketSys\Model\Classes;
	
	class PastaTrabalho {
		private $pst_id;
		private $pst_descricao;
		private $pst_grt_id;

		public function __construct($pst_id,$pst_descricao,$pst_grt_id){
			$this->pst_id  = $pst_id;
			$this->pst_descricao  = $pst_descricao;
			$this->pst_grt_id  = $pst_grt_id;
		}

		public function setpst_id($pst_id) { $this->pst_id = $pst_id; }
		public function getpst_id() { return $this->pst_id; }

		public function setpst_descricao($pst_descricao) { $this->pst_descricao = $pst_descricao; }
		public function getpst_descricao() { return $this->pst_descricao; }

		public function setpst_grt_id($pst_grt_id) { $this->pst_grt_id = $pst_grt_id; }
		public function getpst_grt_id() { return $this->pst_grt_id; }


		public function getArrayofFields() {
			return array(
				'pst_id',
				'pst_descricao',
				'pst_grt_id',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->getpst_id(),
				$this->getpst_descricao(),
				$this->getpst_grt_id(),
			);
		}
	}
?>
