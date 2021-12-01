<?php
	namespace TicketSys\Model\Classes;
	
	class Notificacao {
		private $ntf_id;
		private $ntf_dest_user_id;
		private $ntf_data_hora;
		private $ntf_tipo_alerta;
		private $ntf_notificacao;
		private $ntf_url;
		private $ntf_lida;

		public function __construct($ntf_id,$ntf_dest_user_id,$ntf_data_hora,$ntf_tipo_alerta,$ntf_notificacao,$ntf_url,$ntf_lida){
			$this->ntf_id 				= $ntf_id;
			$this->ntf_dest_user_id 	= $ntf_dest_user_id;
			$this->ntf_data_hora		= $ntf_data_hora;
			$this->ntf_tipo_alerta		= $ntf_tipo_alerta;
			$this->ntf_notificacao		= $ntf_notificacao;
			$this->ntf_url 				= $ntf_url;
			$this->ntf_lida				= $ntf_lida;
		}	    

		public function setntf_id($ntf_id) { $this->ntf_id = $ntf_id; }
		public function getntf_id() { return $this->ntf_id; }

		public function setntf_dest_user_id($ntf_dest_user_id) { $this->ntf_dest_user_id = $ntf_dest_user_id; }
		public function getntf_dest_user_id() { return $this->ntf_dest_user_id; }

		public function setntf_data_hora($ntf_data_hora) { $this->ntf_data_hora = $ntf_data_hora; }
		public function getntf_data_hora() { return $this->ntf_data_hora; }

		public function setntf_tipo_alerta($ntf_tipo_alerta) { $this->ntf_tipo_alerta = $ntf_tipo_alerta; }
		public function getntf_tipo_alerta() { return $this->ntf_tipo_alerta; }

		public function setntf_notificacao($ntf_notificacao) { $this->ntf_notificacao = $ntf_notificacao; }
		public function getntf_notificacao() { return $this->ntf_notificacao; }

		public function setntf_url($ntf_url) { $this->ntf_url = $ntf_url; }
		public function getntf_url() { return $this->ntf_url; }

		public function setntf_lida($ntf_lida) { $this->ntf_lida = $ntf_lida; }
		public function getntf_lida() { return $this->ntf_lida; }

		public function getArrayofFields() {
			return array(
				'ntf_id',
				'ntf_dest_user_id',
				'ntf_data_hora',
				'ntf_tipo_alerta',
				'ntf_notificacao',
				'ntf_url',
				'ntf_lida'
			);
		}

		public function getArrayofValues() {
			return array(
				$this->getntf_id(),
				$this->getntf_dest_user_id(),
				$this->getntf_data_hora(),
				$this->getntf_tipo_alerta(),
				$this->getntf_notificacao(),
				$this->getntf_url(),
				$this->getntf_lida()
			);
		}
	}
?>