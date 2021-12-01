<?php
	namespace TicketSys\Model\Classes;
	
	class Ticket {
		private $tkt_id;
		private $tkt_pst_id;
		private $tkt_titulo;
		private $tkt_tav_id;
		private $tkt_descricao;
		private $tkt_abertura_data;
		private $tkt_abertura_user_id;
		private $tkt_stt_id;
		private $tkt_cgt_id;
		private $tkt_prt_id;
		private $tkt_ort_id;

		private $tkt_data_ini_estim;
		private $tkt_hora_ini_estim;
		private $tkt_data_ini_real;
		private $tkt_hora_ini_real;
		private $tkt_data_fim_estim;
		private $tkt_hora_fim_estim;
		private $tkt_data_fim_real;
		private $tkt_hora_fim_real;
		private $tkt_total_hora_estim;
		private $tkt_total_hora_real;
		private $tkt_per_concluido;

		private $tkt_aprovado;
		private $tkt_aprovado_data;
		private $tkt_aprovado_user_id;

		private $tkt_encerrado;
		private $tkt_encerrado_data;
		private $tkt_encerrado_user_id;

		private $tkt_arquivado;
		private $tkt_arquivado_data;
		private $tkt_arquivado_user_id;

		private $tkt_ticket_pai;

		public function __construct($tkt_id,$tkt_pst_id,$tkt_titulo,$tkt_tav_id,$tkt_descricao,$tkt_abertura_data,$tkt_abertura_user_id,$tkt_stt_id,$tkt_cgt_id,$tkt_prt_id,
									$tkt_ort_id,$tkt_data_ini_estim,$tkt_hora_ini_estim,$tkt_data_ini_real,$tkt_hora_ini_real,$tkt_data_fim_estim,$tkt_hora_fim_estim,$tkt_data_fim_real,$tkt_hora_fim_real,$tkt_total_hora_estim,
									$tkt_total_hora_real,$tkt_per_concluido,
									$tkt_aprovado,$tkt_aprovado_data,$tkt_aprovado_user_id,
									$tkt_encerrado,$tkt_encerrado_data,$tkt_encerrado_user_id,
									$tkt_arquivado,$tkt_arquivado_data,$tkt_arquivado_user_id,
									$tkt_ticket_pai){
			$this->tkt_id  = $tkt_id;
			$this->tkt_pst_id  = $tkt_pst_id;
			$this->tkt_titulo  = $tkt_titulo;
			$this->tkt_tav_id  = $tkt_tav_id;
			$this->tkt_descricao  = $tkt_descricao;
			$this->tkt_abertura_data  = $tkt_abertura_data;
			$this->tkt_abertura_user_id  = $tkt_abertura_user_id;
			$this->tkt_stt_id  = $tkt_stt_id;
			$this->tkt_cgt_id  = $tkt_cgt_id;
			$this->tkt_prt_id  = $tkt_prt_id;
			$this->tkt_ort_id  = $tkt_ort_id;

			$this->tkt_data_ini_estim  = $tkt_data_ini_estim;
			$this->tkt_hora_ini_estim  = $tkt_hora_ini_estim;
			$this->tkt_data_ini_real  = $tkt_data_ini_real;
			$this->tkt_hora_ini_real  = $tkt_hora_ini_real;
			$this->tkt_data_fim_estim  = $tkt_data_fim_estim;
			$this->tkt_hora_fim_estim  = $tkt_hora_fim_estim;
			$this->tkt_data_fim_real  = $tkt_data_fim_real;
			$this->tkt_hora_fim_real  = $tkt_hora_fim_real;
			$this->tkt_total_hora_estim  = $tkt_total_hora_estim;
			$this->tkt_total_hora_real  = $tkt_total_hora_real;
			$this->tkt_per_concluido  = $tkt_per_concluido;

			$this->tkt_aprovado  = $tkt_aprovado;
			$this->tkt_aprovado_data  = $tkt_aprovado_data;
			$this->tkt_aprovado_user_id  = $tkt_aprovado_user_id;

			$this->tkt_encerrado  = $tkt_encerrado;
			$this->tkt_encerrado_data  = $tkt_encerrado_data;
			$this->tkt_encerrado_user_id  = $tkt_encerrado_user_id;

			$this->tkt_arquivado  = $tkt_arquivado;
			$this->tkt_arquivado_data  = $tkt_arquivado_data;
			$this->tkt_arquivado_user_id  = $tkt_arquivado_user_id;

			$this->tkt_ticket_pai  = $tkt_ticket_pai;
		}

		public function settkt_id($tkt_id) { $this->tkt_id = $tkt_id; }
		public function gettkt_id() { return $this->tkt_id; }

		public function settkt_pst_id($tkt_pst_id) { $this->tkt_pst_id = $tkt_pst_id; }
		public function gettkt_pst_id() { return $this->tkt_pst_id; }

		public function settkt_titulo($tkt_titulo) { $this->tkt_titulo = $tkt_titulo; }
		public function gettkt_titulo() { return $this->tkt_titulo; }

		public function settkt_tav_id($tkt_tav_id) { $this->tkt_tav_id = $tkt_tav_id; }
		public function gettkt_tav_id() { return $this->tkt_tav_id; }

		public function settkt_descricao($tkt_descricao) { $this->tkt_descricao = $tkt_descricao; }
		public function gettkt_descricao() { return $this->tkt_descricao; }

		public function settkt_abertura_data($tkt_abertura_data) { $this->tkt_abertura_data = $tkt_abertura_data; }
		public function gettkt_abertura_data() { return $this->tkt_abertura_data; }

		public function settkt_abertura_user_id($tkt_abertura_user_id) { $this->tkt_abertura_user_id = $tkt_abertura_user_id; }
		public function gettkt_abertura_user_id() { return $this->tkt_abertura_user_id; }

		public function settkt_stt_id($tkt_stt_id) { $this->tkt_stt_id = $tkt_stt_id; }
		public function gettkt_stt_id() { return $this->tkt_stt_id; }

		public function settkt_cgt_id($tkt_cgt_id) { $this->tkt_cgt_id = $tkt_cgt_id; }
		public function gettkt_cgt_id() { return $this->tkt_cgt_id; }

		public function settkt_prt_id($tkt_prt_id) { $this->tkt_prt_id = $tkt_prt_id; }
		public function gettkt_prt_id() { return $this->tkt_prt_id; }

		public function settkt_ort_id($tkt_ort_id) { $this->tkt_ort_id = $tkt_ort_id; }
		public function gettkt_ort_id() { return $this->tkt_ort_id; }


		public function settkt_data_ini_estim($tkt_data_ini_estim) { $this->tkt_data_ini_estim = $tkt_data_ini_estim; }
		public function gettkt_data_ini_estim() { return $this->tkt_data_ini_estim; }

		public function settkt_hora_ini_estim($tkt_hora_ini_estim) { $this->tkt_hora_ini_estim = $tkt_hora_ini_estim; }
		public function gettkt_hora_ini_estim() { return $this->tkt_hora_ini_estim; }

		public function settkt_data_ini_real($tkt_data_ini_real) { $this->tkt_data_ini_real = $tkt_data_ini_real; }
		public function gettkt_data_ini_real() { return $this->tkt_data_ini_real; }

		public function settkt_hora_ini_real($tkt_hora_ini_real) { $this->tkt_hora_ini_real = $tkt_hora_ini_real; }
		public function gettkt_hora_ini_real() { return $this->tkt_hora_ini_real; }

		public function settkt_data_fim_estim($tkt_data_fim_estim) { $this->tkt_data_fim_estim = $tkt_data_fim_estim; }
		public function gettkt_data_fim_estim() { return $this->tkt_data_fim_estim; }

		public function settkt_hora_fim_estim($tkt_hora_fim_estim) { $this->tkt_hora_fim_estim = $tkt_hora_fim_estim; }
		public function gettkt_hora_fim_estim() { return $this->tkt_hora_fim_estim; }

		public function settkt_data_fim_real($tkt_data_fim_real) { $this->tkt_data_fim_real = $tkt_data_fim_real; }
		public function gettkt_data_fim_real() { return $this->tkt_data_fim_real; }

		public function settkt_hora_fim_real($tkt_hora_fim_real) { $this->tkt_hora_fim_real = $tkt_hora_fim_real; }
		public function gettkt_hora_fim_real() { return $this->tkt_hora_fim_real; }

		public function settkt_total_hora_estim($tkt_total_hora_estim) { $this->tkt_total_hora_estim = $tkt_total_hora_estim; }
		public function gettkt_total_hora_estim() { return $this->tkt_total_hora_estim; }

		public function settkt_total_hora_real($tkt_total_hora_real) { $this->tkt_total_hora_real = $tkt_total_hora_real; }
		public function gettkt_total_hora_real() { return $this->tkt_total_hora_real; }

		public function settkt_per_concluido($tkt_per_concluido) { $this->tkt_per_concluido = $tkt_per_concluido; }
		public function gettkt_per_concluido() { return $this->tkt_per_concluido; }

		
		public function settkt_aprovado($tkt_aprovado) { $this->tkt_aprovado = $tkt_aprovado; }
		public function gettkt_aprovado() { return $this->tkt_aprovado; }

		public function settkt_aprovado_data($tkt_aprovado_data) { $this->tkt_aprovado_data = $tkt_aprovado_data; }
		public function gettkt_aprovado_data() { return $this->tkt_aprovado_data; }

		public function settkt_aprovado_user_id($tkt_aprovado_user_id) { $this->tkt_aprovado_user_id = $tkt_aprovado_user_id; }
		public function gettkt_aprovado_user_id() { return $this->tkt_aprovado_user_id; }


		public function settkt_encerrado($tkt_encerrado) { $this->tkt_encerrado = $tkt_encerrado; }
		public function gettkt_encerrado() { return $this->tkt_encerrado; }

		public function settkt_encerrado_data($tkt_encerrado_data) { $this->tkt_encerrado_data = $tkt_encerrado_data; }
		public function gettkt_encerrado_data() { return $this->tkt_encerrado_data; }

		public function settkt_encerrado_user_id($tkt_encerrado_user_id) { $this->tkt_encerrado_user_id = $tkt_encerrado_user_id; }
		public function gettkt_encerrado_user_id() { return $this->tkt_encerrado_user_id; }


		public function settkt_arquivado($tkt_arquivado) { $this->tkt_arquivado = $tkt_arquivado; }
		public function gettkt_arquivado() { return $this->tkt_arquivado; }

		public function settkt_arquivado_data($tkt_arquivado_data) { $this->tkt_arquivado_data = $tkt_arquivado_data; }
		public function gettkt_arquivado_data() { return $this->tkt_arquivado_data; }

		public function settkt_arquivado_user_id($tkt_arquivado_user_id) { $this->tkt_arquivado_user_id = $tkt_arquivado_user_id; }
		public function gettkt_arquivado_user_id() { return $this->tkt_arquivado_user_id; }


		public function settkt_ticket_pai($tkt_ticket_pai) { $this->tkt_ticket_pai = $tkt_ticket_pai; }
		public function gettkt_ticket_pai() { return $this->tkt_ticket_pai; }


		public function getArrayofFields() {
			return array(
				'tkt_id',
				'tkt_pst_id',
				'tkt_titulo',
				'tkt_tav_id',
				'tkt_descricao',
				'tkt_abertura_data',
				'tkt_abertura_user_id',
				'tkt_stt_id',
				'tkt_cgt_id',
				'tkt_prt_id',
				'tkt_ort_id',

				'tkt_data_ini_estim',
				'tkt_hora_ini_estim',
				'tkt_data_ini_real',
				'tkt_hora_ini_real',
				'tkt_data_fim_estim',
				'tkt_hora_fim_estim',
				'tkt_data_fim_real',
				'tkt_hora_fim_real',
				'tkt_total_hora_estim',
				'tkt_total_hora_real',
				'tkt_per_concluido',
				
				'tkt_aprovado',
				'tkt_aprovado_data',
				'tkt_aprovado_user_id',
				
				'tkt_encerrado',
				'tkt_encerrado_data',
				'tkt_encerrado_user_id',

				'tkt_arquivado',
				'tkt_arquivado_data',
				'tkt_arquivado_user_id',

				'tkt_ticket_pai',
			);
		}


		public function getArrayofValues() {
			return array(
				$this->gettkt_id(),
				$this->gettkt_pst_id(),
				$this->gettkt_titulo(),
				$this->gettkt_tav_id(),
				$this->gettkt_descricao(),
				$this->gettkt_abertura_data(),
				$this->gettkt_abertura_user_id(),
				$this->gettkt_stt_id(),
				$this->gettkt_cgt_id(),
				$this->gettkt_prt_id(),
				$this->gettkt_ort_id(),

				$this->gettkt_data_ini_estim(),
				$this->gettkt_hora_ini_estim(),
				$this->gettkt_data_ini_real(),
				$this->gettkt_hora_ini_real(),
				$this->gettkt_data_fim_estim(),
				$this->gettkt_hora_fim_estim(),
				$this->gettkt_data_fim_real(),
				$this->gettkt_hora_fim_real(),
				$this->gettkt_total_hora_estim(),
				$this->gettkt_total_hora_real(),
				$this->gettkt_per_concluido(),

				$this->gettkt_aprovado(),
				$this->gettkt_aprovado_data(),
				$this->gettkt_aprovado_user_id(),

				$this->gettkt_encerrado(),
				$this->gettkt_encerrado_data(),
				$this->gettkt_encerrado_user_id(),

				$this->gettkt_arquivado(),
				$this->gettkt_arquivado_data(),
				$this->gettkt_arquivado_user_id(),

				$this->gettkt_ticket_pai(),
			);
		}
	}
?>
