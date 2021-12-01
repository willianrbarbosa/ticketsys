<?php
	namespace TicketSys\Model\Classes;
	
	class Parametro {
		private $par_key;
		private $par_conteudo;
		private $par_descricao;

		public function __construct($par_key,$par_conteudo,$par_descricao){
			$this->par_key 			= $par_key;
			$this->par_conteudo 	= $par_conteudo;
			$this->par_descricao 	= $par_descricao;
		}	    

		public function setpar_key($par_key) { $this->par_key = $par_key; }
		public function getpar_key() { return $this->par_key; }

		public function setpar_conteudo($par_conteudo) { $this->par_conteudo = $par_conteudo; }
		public function getpar_conteudo() { return $this->par_conteudo; }

		public function setpar_descricao($par_descricao) { $this->par_descricao = $par_descricao; }
		public function getpar_descricao() { return $this->par_descricao; }

		public function getArrayofFields() {
			return array(
				'par_key',
				'par_conteudo',
				'par_descricao'
			);
		}

		public function getArrayofValues() {
			return array(
				$this->getpar_key(),
				$this->getpar_conteudo(),
				$this->getpar_descricao()
			);
		}
	}
?>