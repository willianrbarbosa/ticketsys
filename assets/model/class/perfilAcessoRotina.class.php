<?php
	class PerfilAcessoRotina {
		private $pta_rot_nome;
		private $pta_pfa_id;
		private $pta_nivel;
		private $pta_user_atrib;

		public function __construct($pta_rot_nome,$pta_pfa_id,$pta_nivel,$pta_user_atrib){
			$this->pta_rot_nome 	= $pta_rot_nome;
			$this->pta_pfa_id 		= $pta_pfa_id;
			$this->pta_nivel 		= $pta_nivel;
			$this->pta_user_atrib 	= $pta_user_atrib;
		}

		public function setpta_rot_nome($pta_rot_nome) { $this->pta_rot_nome = $pta_rot_nome; }
		public function getpta_rot_nome() { return $this->pta_rot_nome; }

		public function setpta_pfa_id($pta_pfa_id) { $this->pta_pfa_id = $pta_pfa_id; }
		public function getpta_pfa_id() { return $this->pta_pfa_id; }

		public function setpta_nivel($pta_nivel) { $this->pta_nivel = $pta_nivel; }
		public function getpta_nivel() { return $this->pta_nivel; }

		public function setpta_user_atrib($pta_user_atrib) { $this->pta_user_atrib = $pta_user_atrib; }
		public function getpta_user_atrib() { return $this->pta_user_atrib; }

		public function getArrayofFields() {
			return array(
				'pta_rot_nome',
				'pta_pfa_id',
				'pta_nivel',
				'pta_user_atrib'
			);
		}

		public function getArrayofValues() {
			return array(
				$this->getpta_rot_nome(),
				$this->getpta_pfa_id(),
				$this->getpta_nivel(),
				$this->getpta_user_atrib()
			);
		}
	}
?>