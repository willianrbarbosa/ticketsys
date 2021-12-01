<?php
	namespace TicketSys\Model\Classes;
	
	class SituacaoTicket {
		private $stt_id;
		private $stt_ordem;
		private $stt_descricao;
		private $stt_aprova_ticket;
		private $stt_encerra_ticket;
		private $stt_kanban;

		public function __construct($stt_id,$stt_ordem,$stt_descricao,$stt_aprova_ticket,$stt_encerra_ticket,$stt_kanban){
			$this->stt_id  = $stt_id;
			$this->stt_ordem  = $stt_ordem;
			$this->stt_descricao  = $stt_descricao;
			$this->stt_aprova_ticket  = $stt_aprova_ticket;
			$this->stt_encerra_ticket  = $stt_encerra_ticket;
			$this->stt_kanban  = $stt_kanban;
		}

		public function setstt_id($stt_id) { $this->stt_id = $stt_id; }
		public function getstt_id() { return $this->stt_id; }

		public function setstt_ordem($stt_ordem) { $this->stt_ordem = $stt_ordem; }
		public function getstt_ordem() { return $this->stt_ordem; }

		public function setstt_descricao($stt_descricao) { $this->stt_descricao = $stt_descricao; }
		public function getstt_descricao() { return $this->stt_descricao; }

		public function setstt_aprova_ticket($stt_aprova_ticket) { $this->stt_aprova_ticket = $stt_aprova_ticket; }
		public function getstt_aprova_ticket() { return $this->stt_aprova_ticket; }

		public function setstt_encerra_ticket($stt_encerra_ticket) { $this->stt_encerra_ticket = $stt_encerra_ticket; }
		public function getstt_encerra_ticket() { return $this->stt_encerra_ticket; }

		public function setstt_kanban($stt_kanban) { $this->stt_kanban = $stt_kanban; }
		public function getstt_kanban() { return $this->stt_kanban; }


		public function getArrayofFields() {
			return array(
				'stt_id',
				'stt_ordem',
				'stt_descricao',
				'stt_aprova_ticket',
				'stt_encerra_ticket',
				'stt_kanban'
			);
		}


		public function getArrayofValues() {
			return array(
				$this->getstt_id(),
				$this->getstt_ordem(),
				$this->getstt_descricao(),
				$this->getstt_aprova_ticket(),
				$this->getstt_encerra_ticket(),
				$this->getstt_kanban()
			);
		}
	}
?>
