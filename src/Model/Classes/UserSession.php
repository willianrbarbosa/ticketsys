<?php
	namespace TicketSys\Model\Classes;

	class UserSession {
		public function Destroy(){
			// Remove as variáveis da sessão (caso elas existam)
			unset(
				$_SESSION['user_id'], 
				$_SESSION['user_nome'], 
				$_SESSION['user_email'], 
				$_SESSION['user_tipo'], 
				$_SESSION['user_pfa_id'], 
				$_SESSION['user_token']
			);
			//Finaliza a sessão
			session_destroy();
		}

		public function Exist(){
			return ISSET($_SESSION['user_id']);
		}	

	    public function setUser_id($user_id) {	$_SESSION['user_id'] = $user_id;	}
	    public function getUser_id() {	return $_SESSION['user_id'];	}

	    public function setUser_nome($user_nome) {	$_SESSION['user_nome'] = $user_nome;	}
	    public function getUser_nome() {	return $_SESSION['user_nome'];	}

	    public function setUser_email($user_email) {	$_SESSION['user_email']  = $user_email;	}
	    public function getUser_email() {	return $_SESSION['user_email'] ;	}

	    public function setUser_tipo($user_tipo) {	$_SESSION['user_tipo']  = $user_tipo;	}
	    public function getUser_tipo() {	return $_SESSION['user_tipo'] ;	}

	    public function setUser_pfa_id($user_pfa_id) {	$_SESSION['user_pfa_id'] = $user_pfa_id;	}
	    public function getUser_pfa_id() {	return $_SESSION['user_pfa_id'];	}

	    public function setUser_token($user_token) {	$_SESSION['user_token'] = $user_token;	}
	    public function getUser_token() {	return $_SESSION['user_token'];	}
	}	
?>