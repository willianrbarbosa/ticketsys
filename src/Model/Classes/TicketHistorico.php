<?php
	namespace TicketSys\Model\Classes;
	
	class TicketHistorico {
		private $tkh_id;
		private $tkh_tkt_id;
		private $tkh_user_id;
		private $tkh_data_hora;
		private $tkh_descricao;

		public function __construct($tkh_id,$tkh_tkt_id,$tkh_user_id,$tkh_data_hora,$tkh_descricao){
			$this->tkh_id  = $tkh_id;
			$this->tkh_tkt_id  = $tkh_tkt_id;
			$this->tkh_user_id  = $tkh_user_id;
			$this->tkh_data_hora  = $tkh_data_hora;
			$this->tkh_descricao  = $tkh_descricao;
		}

		public function settkh_id($tkh_id) { $this->tkh_id = $tkh_id; }
		public function gettkh_id() { return $this->tkh_id; }

		public function settkh_tkt_id($tkh_tkt_id) { $this->tkh_tkt_id = $tkh_tkt_id; }
		public function gettkh_tkt_id() { return $this->tkh_tkt_id; }

		public function settkh_user_id($tkh_user_id) { $this->tkh_user_id = $tkh_user_id; }
		public function gettkh_user_id() { return $this->tkh_user_id; }

		public function settkh_data_hora($tkh_data_hora) { $this->tkh_data_hora = $tkh_data_hora; }
		public function gettkh_data_hora() { return $this->tkh_data_hora; }

		public function settkh_descricao($tkh_descricao) { $this->tkh_descricao = $tkh_descricao; }
		public function gettkh_descricao() { return $this->tkh_descricao; }


		public function getArrayofFields() {
			return array(
				'tkh_id',
				'tkh_tkt_id',
				'tkh_user_id',
				'tkh_data_hora',
				'tkh_descricao',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->gettkh_id(),
				$this->gettkh_tkt_id(),
				$this->gettkh_user_id(),
				$this->gettkh_data_hora(),
				$this->gettkh_descricao(),
			);
		}
	}
?>
