<?php
	namespace TicketSys\Model\Classes;
	
	class Usuario {
		private $user_id;
		private $user_nome;
		private $user_email;
		private $user_passwd;
		private $user_pfa_id;
		private $user_photo;
		private $user_tipo;
		private $user_token;
		private $user_ativo;
		private $user_pst_id;
		private $user_resp_ticket;
		private $user_email_confirm;

		public function __construct($user_id,$user_nome,$user_email,$user_passwd,$user_pfa_id,$user_photo,$user_tipo,$user_token,$user_ativo,$user_pst_id,$user_resp_ticket,$user_email_confirm){
			$this->user_id 				= $user_id;
			$this->user_nome 			= $user_nome;
			$this->user_email 			= $user_email;
			$this->user_passwd 			= $user_passwd;
			$this->user_pfa_id 			= $user_pfa_id;
			$this->user_photo 			= $user_photo;
			$this->user_tipo 			= $user_tipo;
			$this->user_token 			= $user_token;
			$this->user_ativo 			= $user_ativo;
			$this->user_pst_id 			= $user_pst_id;
			$this->user_resp_ticket 	= $user_resp_ticket;
			$this->user_email_confirm 	= $user_email_confirm;
		}	    

		public function setuser_id($user_id) { $this->user_id = $user_id; }
		public function getuser_id() { return $this->user_id; }

		public function setuser_nome($user_nome) { $this->user_nome = $user_nome; }
		public function getuser_nome() { return $this->user_nome; }

		public function setuser_email($user_email) { $this->user_email = $user_email; }
		public function getuser_email() { return $this->user_email; }

		public function setuser_passwd($user_passwd) { $this->user_passwd = $user_passwd; }
		public function getuser_passwd() { return $this->user_passwd; }
		
		public function setuser_pfa_id($user_pfa_id) { $this->user_pfa_id = $user_pfa_id; }
		public function getuser_pfa_id() { return $this->user_pfa_id; }

		public function setuser_photo($user_photo) { $this->user_photo = $user_photo; }
		public function getuser_photo() { return $this->user_photo; }

		public function setuser_tipo($user_tipo) { $this->user_tipo = $user_tipo; }
		public function getuser_tipo() { return $this->user_tipo; }

		public function setuser_token($user_token) { $this->user_token = $user_token; }
		public function getuser_token() { return $this->user_token; }

		public function setuser_ativo($user_ativo) { $this->user_ativo = $user_ativo; }
		public function getuser_ativo() { return $this->user_ativo; }

		public function setuser_pst_id($user_pst_id) { $this->user_pst_id = $user_pst_id; }
		public function getuser_pst_id() { return $this->user_pst_id; }

		public function setuser_resp_ticket($user_resp_ticket) { $this->user_resp_ticket = $user_resp_ticket; }
		public function getuser_resp_ticket() { return $this->user_resp_ticket; }

		public function setuser_email_confirm($user_email_confirm) { $this->user_email_confirm = $user_email_confirm; }
		public function getuser_email_confirm() { return $this->user_email_confirm; }

		public function getArrayofFields() {
			return array(
				'user_id',
				'user_nome',
				'user_email',
				'user_passwd',
				'user_pfa_id',
				'user_photo',
				'user_tipo',
				'user_token',
				'user_ativo',
				'user_pst_id',
				'user_resp_ticket',
				'user_email_confirm'
			);
		}

		public function getArrayofValues() {
			return array(
				$this->getuser_id(),
				$this->getuser_nome(),
				$this->getuser_email(),
				$this->getuser_passwd(),
				$this->getuser_pfa_id(),
				$this->getuser_photo(),
				$this->getuser_tipo(),
				$this->getuser_token(),
				$this->getuser_ativo(),
				$this->getuser_pst_id(),
				$this->getuser_resp_ticket(),
				$this->getuser_email_confirm()
			);
		}
	}
?>