<?php
	namespace TicketSys\Model\Classes;
	
	class GrupoTrabalho {
		private $grt_id;
		private $grt_descricao;

		public function __construct($grt_id,$grt_descricao){
			$this->grt_id  = $grt_id;
			$this->grt_descricao  = $grt_descricao;
		}

		public function setgrt_id($grt_id) { $this->grt_id = $grt_id; }
		public function getgrt_id() { return $this->grt_id; }

		public function setgrt_descricao($grt_descricao) { $this->grt_descricao = $grt_descricao; }
		public function getgrt_descricao() { return $this->grt_descricao; }


		public function getArrayofFields() {
			return array(
				'grt_id',
				'grt_descricao',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->getgrt_id(),
				$this->getgrt_descricao(),
			);
		}
	}
?>
