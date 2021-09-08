<?php   
	// require_once("../lib/mpdf/mpdf.php");
require_once '../lib/mpdf/vendor/autoload.php';

	class reportCliente extends \Mpdf\Mpdf{  

		// Atributos da classe  
		public $pdo  = null;  
		public $pdf  = null;
		public $css  = null;  
		public $titulo = null; 

		/*  
		* Construtor da classe  
		* @param $css  - Arquivo CSS  
		* @param $titulo - Título do relatório   
		*/  
		public function __construct($css, $titulo) {  
			$this->titulo = $titulo;  
			$this->setarCSS($css);  
		}

		/*  
		* Método para setar o conteúdo do arquivo CSS para o atributo css  
		* @param $file - Caminho para arquivo CSS  
		*/  
		public function setarCSS($file){  
			if (file_exists($file)):  
				$this->css = file_get_contents($file);   
			endif;  
		} 

		/*  
		* Método para montar o Cabeçalho do relatório em PDF  
		*/  
		protected function getHeader($texto){ 
			$retorno = "
					<table class=\"tbl_header\" width=\"1000\"> 
						<tr>  
							<td align=\"left\" class=\"tdHeader\"><img src=\"https://app.cadastracerto.com.br/assets/img/logo-cadastra-certo-rpt.jpg\" style=\"height: 50px !important; width: auto !important;\"></td> 
							<td align=\"center\" class=\"tdHeader\" style=\"font-size: 11px; font-weigth: bold;\">".$texto."</td>  
							<td align=\"right\" style=\"font-size: 10px; \">Página: {PAGENO}</td>  
						</tr>
					</table>";  
			return $retorno;  
		} 

		/*  
		* Método para montar o Rodapé do relatório em PDF  
		*/  
		protected function getFooter($texto){  
			$data = date('d/m/Y');  
			if ( !Empty($texto) ) {
				$retorno = "
						<table class=\"tbl_footer\" width=\"1000\">  
							<tr>  
								<td align=\"left\" class=\"tdFooter\" style=\"font-size: 10px; \">".$texto."</a></td>  
								<td align=\"right\" class=\"tdFooter\" style=\"font-size: 10px; \">Gerado em ".$data."</td>  
							</tr>  
						</table>";  
			} else {
				$retorno = "";
			}
			return $retorno;  
		}  

		/*   
		* Método para construir o arquivo PDF  
		*/  
		public function BuildPDF($cHeader, $cFooter, $cOrientation, $nML, $nMR, $nMT, $nMB){  
			// $this->pdf = new mPDF('utf-8', $cOrientation,'','',$nML,$nMR,$nMT,$nMB);  
			$mPDFCfg	= [
				'mode' 			=> 'utf-8',
				'format' 		=> $cOrientation,
				'margin_left' 	=> $nML,
				'margin_right' 	=> $nMR,
				'margin_top' 	=> $nMT,
				'margin_bottom' => $nMB
			];
			$this->pdf 	= new \Mpdf\Mpdf($mPDFCfg);  
			$this->pdf->showImageErrors = true;
			if ( !Empty($this->css) ) {
				$this->pdf->WriteHTML($this->css);  
			}
			$this->pdf->SetHTMLHeader($this->getHeader($cHeader));  
			$this->pdf->SetHTMLFooter($this->getFooter($cFooter)); 
		}   

		/*   
		* Método para exibir o arquivo PDF  
		* @param $name - Nome do arquivo se necessário grava-lo  
		*/  
		public function Exibir($name = null) {  
			$this->pdf->Output($name, 'I');  
		}  

		public function BaixaPDF($name = null) {  
			$this->pdf->Output($name, 'D');  
		}  

		public function SalvaPDF($name = null) {  
			$this->pdf->Output($name, 'F');  
		} 
	}   
?>