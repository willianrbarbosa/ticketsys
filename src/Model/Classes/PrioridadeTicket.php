<?php
	namespace TicketSys\Model\Classes;
	
	class PrioridadeTicket {
		private $prt_id;
		private $prt_prioridade;
		private $prt_descricao;
		private $prt_cor;

		public function __construct($prt_id,$prt_prioridade,$prt_descricao,$prt_cor){
			$this->prt_id  = $prt_id;
			$this->prt_prioridade  = $prt_prioridade;
			$this->prt_descricao  = $prt_descricao;
			$this->prt_cor  = $prt_cor;
		}

		public function setprt_id($prt_id) { $this->prt_id = $prt_id; }
		public function getprt_id() { return $this->prt_id; }

		public function setprt_prioridade($prt_prioridade) { $this->prt_prioridade = $prt_prioridade; }
		public function getprt_prioridade() { return $this->prt_prioridade; }

		public function setprt_descricao($prt_descricao) { $this->prt_descricao = $prt_descricao; }
		public function getprt_descricao() { return $this->prt_descricao; }

		public function setprt_cor($prt_cor) { $this->prt_cor = $prt_cor; }
		public function getprt_cor() { return $this->prt_cor; }


		public function getArrayofFields() {
			return array(
				'prt_id',
				'prt_prioridade',
				'prt_descricao',
				'prt_cor',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->getprt_id(),
				$this->getprt_prioridade(),
				$this->getprt_descricao(),
				$this->getprt_cor(),
			);
		}
	}
?>
