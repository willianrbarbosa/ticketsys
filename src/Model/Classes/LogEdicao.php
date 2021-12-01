<?php
	namespace TicketSys\Model\Classes;
	
	class LogEdicao {
		private $led_id;
		private $led_user_id;
		private $led_rot_nome;
		private $led_key;
		private $led_action;
		private $led_table;
		private $led_date;

		public function __construct($led_id,$led_user_id,$led_rot_nome,$led_key,$led_action,$led_table,$led_date){
			$this->led_id 			= $led_id;
			$this->led_user_id 		= $led_user_id;
			$this->led_rot_nome 	= $led_rot_nome;
			$this->led_key 			= $led_key;
			$this->led_action 		= $led_action;
			$this->led_table 		= $led_table;
			$this->led_date 		= $led_date;
		}

		public function setled_id($led_id) { $this->led_id = $led_id; }
		public function getled_id() { return $this->led_id; }

		public function setled_user_id($led_user_id) { $this->led_user_id = $led_user_id; }
		public function getled_user_id() { return $this->led_user_id; }

		public function setled_rot_nome($led_rot_nome) { $this->led_rot_nome = $led_rot_nome; }
		public function getled_rot_nome() { return $this->led_rot_nome; }

		public function setled_key($led_key) { $this->led_key = $led_key; }
		public function getled_key() { return $this->led_key; }

		public function setled_action($led_action) { $this->led_action = $led_action; }
		public function getled_action() { return $this->led_action; }

		public function setled_table($led_table) { $this->led_table = $led_table; }
		public function getled_table() { return $this->led_table; }

		public function setled_date($led_date) { $this->led_date = $led_date; }
		public function getled_date() { return $this->led_date; }

		public function getArrayofFields() {
			return array(
				'led_id',
				'led_user_id',
				'led_rot_nome',
				'led_key',
				'led_action',
				'led_table',
				'led_date'
			);
		}

		public function getArrayofValues() {
			return array(
				$this->getled_id(),
				$this->getled_user_id(),
				$this->getled_rot_nome(),
				$this->getled_key(),
				$this->getled_action(),
				$this->getled_table(),
				$this->getled_date()
			);
		}
	}
?>