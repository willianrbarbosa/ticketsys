<?php
	namespace TicketSys\Model\Classes;
	
	class Indicador {
		private $ind_chave;
		private $ind_param;
		private $ind_valor;
		private $ind_descricao;

		public function __construct($ind_chave,$ind_param,$ind_valor,$ind_descricao){
			$this->ind_chave 		= $ind_chave;
			$this->ind_param 		= $ind_param;
			$this->ind_valor 		= $ind_valor;
			$this->ind_descricao 	= $ind_descricao;
		}	    

		public function setind_chave($ind_chave) { $this->ind_chave = $ind_chave; }
		public function getind_chave() { return $this->ind_chave; }

		public function setind_param($ind_param) { $this->ind_param = $ind_param; }
		public function getind_param() { return $this->ind_param; }

		public function setind_valor($ind_valor) { $this->ind_valor = $ind_valor; }
		public function getind_valor() { return $this->ind_valor; }

		public function setind_descricao($ind_descricao) { $this->ind_descricao = $ind_descricao; }
		public function getind_descricao() { return $this->ind_descricao; }

		public function getArrayofFields() {
			return array(
				'ind_chave',
				'ind_param',
				'ind_valor',
				'ind_descricao'
			);
		}

		public function getArrayofValues() {
			return array(
				$this->getind_chave(),
				$this->getind_param(),
				$this->getind_valor(),
				$this->getind_descricao()
			);
		}
	}
?>