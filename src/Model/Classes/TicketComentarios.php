<?php
	namespace TicketSys\Model\Classes;
	
	class TicketComentarios {
		private $tkc_id;
		private $tkc_tkt_id;
		private $tkc_user_id;
		private $tkc_data_hora;
		private $tkc_descricao;
		private $tkc_tipo;

		public function __construct($tkc_id,$tkc_tkt_id,$tkc_user_id,$tkc_data_hora,$tkc_descricao,$tkc_tipo){
			$this->tkc_id  = $tkc_id;
			$this->tkc_tkt_id  = $tkc_tkt_id;
			$this->tkc_user_id  = $tkc_user_id;
			$this->tkc_data_hora  = $tkc_data_hora;
			$this->tkc_descricao  = $tkc_descricao;
			$this->tkc_tipo  = $tkc_tipo;
		}

		public function settkc_id($tkc_id) { $this->tkc_id = $tkc_id; }
		public function gettkc_id() { return $this->tkc_id; }

		public function settkc_tkt_id($tkc_tkt_id) { $this->tkc_tkt_id = $tkc_tkt_id; }
		public function gettkc_tkt_id() { return $this->tkc_tkt_id; }

		public function settkc_user_id($tkc_user_id) { $this->tkc_user_id = $tkc_user_id; }
		public function gettkc_user_id() { return $this->tkc_user_id; }

		public function settkc_data_hora($tkc_data_hora) { $this->tkc_data_hora = $tkc_data_hora; }
		public function gettkc_data_hora() { return $this->tkc_data_hora; }

		public function settkc_descricao($tkc_descricao) { $this->tkc_descricao = $tkc_descricao; }
		public function gettkc_descricao() { return $this->tkc_descricao; }

		public function settkc_tipo($tkc_tipo) { $this->tkc_tipo = $tkc_tipo; }
		public function gettkc_tipo() { return $this->tkc_tipo; }


		public function getArrayofFields() {
			return array(
				'tkc_id',
				'tkc_tkt_id',
				'tkc_user_id',
				'tkc_data_hora',
				'tkc_descricao',
				'tkc_tipo',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->gettkc_id(),
				$this->gettkc_tkt_id(),
				$this->gettkc_user_id(),
				$this->gettkc_data_hora(),
				$this->gettkc_descricao(),
				$this->gettkc_tipo(),
			);
		}
	}
?>
