<?php   
	require_once '../lib/snappy/vendor/autoload.php';

	class snappyReportPDF extends Knp\Snappy\Pdf{  
		//options do snappy
		//https://wkhtmltopdf.org/usage/wkhtmltopdf.txt

		// Atributos da classe  
		public $pdo  		= null;  
		public $snappyPDF  	= null;
		public $css  		= null;  
		public $titulo 		= null; 

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
		protected function getHeader($cTitulo, $lImpLogo = true){ 
			$cLogoPDF	= '
				<span style="color: #372684 !important; font-weight: bold; font-family: Arial Black; font-size: 22px; line-height: 10px;">CADASTRA</span><br/>
				<span style="color: #13713b !important; font-weight: bold; font-family: Arial Black; font-size: 22px; line-height: 10px;">CERTO</span>';
			if ( $lImpLogo ) {
				$cLogoPDF	= '<img src="https://app.cadastracerto.com.br/assets/img/ticket_logo.jpg" style="height: 50px !important; width: auto !important;">';
			}

			$retorno 	= '
			<!DOCTYPE html>
			<html>
				<head>
			        <title>CADASTRA CERTO</title>
			        <meta charset="UTF-8">
				</head>
				<body>
					<table class="tbl_header" width="100%"> 
						<tr>  
							<td width="10%" align="left" class="tdHeader">'.$cLogoPDF.'</td> 
							<td width="90%" align="center" class="tdHeader" style="font-size: 11px; font-weigth: bold;">'.$cTitulo.'</td>
						</tr>
					</table>
				</body>
			</html>';  
			return $retorno;  
		} 

		/*  
		* Método para montar o Rodapé do relatório em PDF  
		*/  
		protected function getFooter($texto){ 
			if ( !Empty($texto) ) {
				$retorno = '
				<!DOCTYPE html>
				<html>
					<head>
				        <title>CADASTRA CERTO</title>
				        <meta charset="UTF-8">
					</head>
					<body>
						<table class="tbl_footer" width="100%">  
							<tr>  
								<td align="left" class="tdFooter" style="font-size: 10px; ">'.$texto.'</td>   
							</tr>  
						</table>
					</body>
				</html>';  
			} else {
				$retorno = "";
			}
			return $retorno;  
		}  

		/*   
		* Método para construir o arquivo PDF  
		*/  
		public function BuildPDF($dirBIN, $cHeader, $cFooter, $cPaper, $cOrientation, $nML, $nMR, $nMT, $nMB, $lImpLogoHeader = true){
			$this->snappyPDF = new Knp\Snappy\Pdf($dirBIN);
			$this->snappyPDF->setoptions([
				'title'				=> $this->titulo,
				'username'			=> SEC_USER_NOME,
				'page-size'			=> $cPaper,
				'orientation'		=> $cOrientation,
				'margin-left'		=> $nML,
				'margin-right'		=> $nMR,
				'margin-top'		=> $nMT,
				'margin-bottom'		=> $nMB,
				'header-html'		=> $this->getHeader($cHeader, $lImpLogoHeader),
				'footer-html'		=> $this->getFooter($cFooter),
				'footer-right'		=> '[page]/[toPage]',
				'header-font-size'	=> '6',
				'footer-font-size'	=> '6',
			]);
			if ( !Empty($this->css) ) {
				$this->snappyPDF->getOutputFromHtml($this->css);  
			}
		}

		public function ExibirPDF($cPrintPDF) {  
			// Cabeçalho para o navegador entender que o conteúdo é um PDF
			header('Content-Type: application/pdf');
			header('Content-disposition: inline; filename="'.$this->titulo.'"');
			header('Cache-Control: no-cache, must-revalidate, max-age=0');
			header("Pragma: no-cache");
			header('X-Generator: CadastraCerto - Snappy PDF');
			header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

			echo $this->snappyPDF->getOutputFromHtml($cPrintPDF, ['encoding' => 'UTF8']);  
		}

		public function BaixaPDF($cPrintPDF) {  
			header('Content-Disposition: attachment; filename="'.$this->titulo.'"');
			header('Cache-Control: no-cache, must-revalidate, max-age=0');
			header("Pragma: no-cache");
			header('X-Generator: CadastraCerto - Snappy PDF');
			header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			
			echo $this->snappyPDF->getOutputFromHtml($cPrintPDF, ['encoding' => 'UTF8']); 
		}

		public function SalvaPDF($cPrintPDF, $cNomePDF) {  
			echo $this->snappyPDF->generateFromHtml($cPrintPDF, $cNomePDF, ['encoding' => 'UTF8']);  
		}
	}
?>