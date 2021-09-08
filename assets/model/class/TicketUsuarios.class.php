<?php
	class TicketUsuarios {
		private $tku_id;
		private $tku_tkt_id;
		private $tku_user_id;
		private $tku_tipo;
		private $tku_notif_email;
		private $tku_notif_sistema;

		public function __construct($tku_id,$tku_tkt_id,$tku_user_id,$tku_tipo,$tku_notif_email,$tku_notif_sistema){
			$this->tku_id  = $tku_id;
			$this->tku_tkt_id  = $tku_tkt_id;
			$this->tku_user_id  = $tku_user_id;
			$this->tku_tipo  = $tku_tipo;
			$this->tku_notif_email  = $tku_notif_email;
			$this->tku_notif_sistema  = $tku_notif_sistema;
		}

		public function settku_id($tku_id) { $this->tku_id = $tku_id; }
		public function gettku_id() { return $this->tku_id; }

		public function settku_tkt_id($tku_tkt_id) { $this->tku_tkt_id = $tku_tkt_id; }
		public function gettku_tkt_id() { return $this->tku_tkt_id; }

		public function settku_user_id($tku_user_id) { $this->tku_user_id = $tku_user_id; }
		public function gettku_user_id() { return $this->tku_user_id; }

		public function settku_tipo($tku_tipo) { $this->tku_tipo = $tku_tipo; }
		public function gettku_tipo() { return $this->tku_tipo; }

		public function settku_notif_email($tku_notif_email) { $this->tku_notif_email = $tku_notif_email; }
		public function gettku_notif_email() { return $this->tku_notif_email; }

		public function settku_notif_sistema($tku_notif_sistema) { $this->tku_notif_sistema = $tku_notif_sistema; }
		public function gettku_notif_sistema() { return $this->tku_notif_sistema; }


		public function getArrayofFields() {
			return array(
				'tku_id',
				'tku_tkt_id',
				'tku_user_id',
				'tku_tipo',
				'tku_notif_email',
				'tku_notif_sistema',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->gettku_id(),
				$this->gettku_tkt_id(),
				$this->gettku_user_id(),
				$this->gettku_tipo(),
				$this->gettku_notif_email(),
				$this->gettku_notif_sistema(),
			);
		}
	}
?>
