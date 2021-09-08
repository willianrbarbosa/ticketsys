<?php
	class TicketApontamentos {
		private $tkp_id;
		private $tkp_tkt_id;
		private $tkp_user_id;
		private $tkp_data;
		private $tkp_hora_exec_ini;
		private $tkp_hora_exec_fim;
		private $tkp_horas_total;

		public function __construct($tkp_id,$tkp_tkt_id,$tkp_user_id,$tkp_data,$tkp_hora_exec_ini,$tkp_hora_exec_fim,$tkp_horas_total){
			$this->tkp_id  				= $tkp_id;
			$this->tkp_tkt_id  			= $tkp_tkt_id;
			$this->tkp_user_id  		= $tkp_user_id;
			$this->tkp_data  			= $tkp_data;
			$this->tkp_hora_exec_ini  	= $tkp_hora_exec_ini;
			$this->tkp_hora_exec_fim  	= $tkp_hora_exec_fim;
			$this->tkp_horas_total  	= $tkp_horas_total;
		}

		public function settkp_id($tkp_id) { $this->tkp_id = $tkp_id; }
		public function gettkp_id() { return $this->tkp_id; }

		public function settkp_tkt_id($tkp_tkt_id) { $this->tkp_tkt_id = $tkp_tkt_id; }
		public function gettkp_tkt_id() { return $this->tkp_tkt_id; }

		public function settkp_user_id($tkp_user_id) { $this->tkp_user_id = $tkp_user_id; }
		public function gettkp_user_id() { return $this->tkp_user_id; }

		public function settkp_data($tkp_data) { $this->tkp_data = $tkp_data; }
		public function gettkp_data() { return $this->tkp_data; }

		public function settkp_hora_exec_ini($tkp_hora_exec_ini) { $this->tkp_hora_exec_ini = $tkp_hora_exec_ini; }
		public function gettkp_hora_exec_ini() { return $this->tkp_hora_exec_ini; }

		public function settkp_hora_exec_fim($tkp_hora_exec_fim) { $this->tkp_hora_exec_fim = $tkp_hora_exec_fim; }
		public function gettkp_hora_exec_fim() { return $this->tkp_hora_exec_fim; }

		public function settkp_horas_total($tkp_horas_total) { $this->tkp_horas_total = $tkp_horas_total; }
		public function gettkp_horas_total() { return $this->tkp_horas_total; }


		public function getArrayofFields() {
			return array(
				'tkp_id',
				'tkp_tkt_id',
				'tkp_user_id',
				'tkp_data',
				'tkp_hora_exec_ini',
				'tkp_hora_exec_fim',
				'tkp_horas_total',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->gettkp_id(),
				$this->gettkp_tkt_id(),
				$this->gettkp_user_id(),
				$this->gettkp_data(),
				$this->gettkp_hora_exec_ini(),
				$this->gettkp_hora_exec_fim(),
				$this->gettkp_horas_total(),
			);
		}
	}
?>
