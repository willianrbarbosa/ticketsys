<?php
	class TicketArquivos {
		private $tka_id;
		private $tka_tkt_id;
		private $tka_user_id;
		private $tka_data_hora;
		private $tka_arquivo_nome;
		private $tka_arquivo_local;
		private $tka_arquivo_tipo;

		public function __construct($tka_id,$tka_tkt_id,$tka_user_id,$tka_data_hora,$tka_arquivo_nome,$tka_arquivo_local,$tka_arquivo_tipo){
			$this->tka_id  = $tka_id;
			$this->tka_tkt_id  = $tka_tkt_id;
			$this->tka_user_id  = $tka_user_id;
			$this->tka_data_hora  = $tka_data_hora;
			$this->tka_arquivo_nome  = $tka_arquivo_nome;
			$this->tka_arquivo_local  = $tka_arquivo_local;
			$this->tka_arquivo_tipo  = $tka_arquivo_tipo;
		}

		public function settka_id($tka_id) { $this->tka_id = $tka_id; }
		public function gettka_id() { return $this->tka_id; }

		public function settka_tkt_id($tka_tkt_id) { $this->tka_tkt_id = $tka_tkt_id; }
		public function gettka_tkt_id() { return $this->tka_tkt_id; }

		public function settka_user_id($tka_user_id) { $this->tka_user_id = $tka_user_id; }
		public function gettka_user_id() { return $this->tka_user_id; }

		public function settka_data_hora($tka_data_hora) { $this->tka_data_hora = $tka_data_hora; }
		public function gettka_data_hora() { return $this->tka_data_hora; }

		public function settka_arquivo_nome($tka_arquivo_nome) { $this->tka_arquivo_nome = $tka_arquivo_nome; }
		public function gettka_arquivo_nome() { return $this->tka_arquivo_nome; }

		public function settka_arquivo_local($tka_arquivo_local) { $this->tka_arquivo_local = $tka_arquivo_local; }
		public function gettka_arquivo_local() { return $this->tka_arquivo_local; }

		public function settka_arquivo_tipo($tka_arquivo_tipo) { $this->tka_arquivo_tipo = $tka_arquivo_tipo; }
		public function gettka_arquivo_tipo() { return $this->tka_arquivo_tipo; }


		public function getArrayofFields() {
			return array(
				'tka_id',
				'tka_tkt_id',
				'tka_user_id',
				'tka_data_hora',
				'tka_arquivo_nome',
				'tka_arquivo_local',
				'tka_arquivo_tipo'
			);
		}


		public function getArrayofValues() {
			return array(
				$this->gettka_id(),
				$this->gettka_tkt_id(),
				$this->gettka_user_id(),
				$this->gettka_data_hora(),
				$this->gettka_arquivo_nome(),
				$this->gettka_arquivo_local(),
				$this->gettka_arquivo_tipo()
			);
		}
	}
?>
