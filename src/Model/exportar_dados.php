<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
    ob_start();
	session_start();
	
	include('session_vars.php');

	if ( SESSION_EXISTS ) {	
		if ( !Empty($_POST) ) {
			$cNomeTabela 	= $_POST['nome_tabela'];
			$cTabelaHTML 	= $_POST['tabela_html'];
			$cTipoExporta 	= $_POST['exporta_tipo'];

			$cHTMLFILE = '
			<html>
			<head>
				<meta charset="UTF-8"/>
	    		<meta name="viewport" content="width=device-width, initial-scale=1">
				<style>
					.table-striped > tbody > tr:nth-of-type(odd) { background-color: #f9f9f9 !important; }
					.left-justify{ text-align: left; }
					.center-justify{ text-align: center; }
					.right-justify{ text-align: right; }
					.bdl { border-left: 3px solid #000 !important; }
					.bdd { border-left: 1px solid #CC0000 !important; border-top: 1px solid #CC0000 !important; border-right: 1px solid #CC0000 !important; border-bottom: 1px solid #CC0000 !important; }
					.fs-S { color: #ff4444 !important; font-weight: bold; }
					.fs-B { color: #00C851 !important; font-weight: bold; }
					.bdh { border: 1px solid #FFF; }
					table { width: 100%; }
					table thead th { background-color: #DEDEDE; color: #000; font-size: 10px; font-family: Calibri; }
					table tbody td { font-size: 10px; font-family: Calibri; }
					a { text-decoration: none !important; color: #000 !important; }
					.lbtit { font-family: Calibri; font-weight: bold !important; }
					.bgtot { background-color: #DEDEDE !important; }
					.lbtot { font-weight: bold !important; font-size: 11px !important; }
					.w100-rpt { width: 100% !important; }
					.div-chart { padding-bottom: 30px !important; padding-top: 20px !important; width: 100% !important; 	}
					.new-page {	page-break-before: always;	}
				</style>
			</head>
			<body>';

			if ( $cTipoExporta == 'EXCEL' ) {
				$cHTMLFILE .= '
					<table class="tbl_header" width="1000"> 
						<tr>  
							<td align="center" class="tdHeader" style="font-size: 11px; font-weigth: bold;"><strong>CADASTRACERTO: '.$cNomeTabela.'</strong></td>  
						</tr>
					</table> <br>
					'.$cTabelaHTML.'
				</body>
				</html>';

				$cExcelFile 	= 'CADASTRACERTO - '.$cNomeTabela.'_'.date('d-m-Y').'.xls';
				// Configurações header para forçar o download
				header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
				header ("Cache-Control: no-cache, must-revalidate");
				header ("Pragma: no-cache");
				header ("Content-type: application/x-msexcel");
				header ("Content-Disposition: attachment; filename=\"{$cExcelFile}\"" );
				header ("Content-Description: PHP Generated Data" );
				// Envia o conteúdo do arquivo
				echo $cHTMLFILE;

				$cHTMLFILE = null;	
				$cTabelaHTML = null;

				echo '
				<script>
					window.close();
				</script>';
			} elseif ( $cTipoExporta == 'PDF' ) {
				$cHTMLFILE .= $cTabelaHTML.'
				</body>
				</html>';

				$cHeader 	= '<strong>'.$cNomeTabela.'</strong>';
				$cFooter 	= 'Relatório gerado por '.SEC_USER_NOME.' em '.Date('d/m/Y').' ás '.Date('H:i:s');
				$cCamPDF	= $security->base_patch.'/assets/relatorios/';
				$cNomePDF	= 'CADASTRACERTO: '.$cNomeTabela.'_'.date('d-m-Y').'.pdf';
				
				//Para usar localhost \/
				$WKBIN	= $_SERVER['DOCUMENT_ROOT'].$security->base_patch.'/assets/lib/snappy/vendor/wkhtmltopdf/bin/wkhtmltopdf64';
				// $WKBIN	= '/usr/local/bin/wkhtmltopdf';

				require_once("class/snappyReport.class.php");
				$report = new snappyReportPDF("", $cNomePDF);
				$report->BuildPDF($WKBIN, $cHeader, $cFooter, 'A4', 'landscape','15','15','18','18', true);
				$report->snappyPDF->setTimeout(3600);

				// $report->SalvaPDF($cHTMLFILE, $cCamPDF.$cNomePDF);
				$report->ExibirPDF($cHTMLFILE);
				
				exit();	
			} elseif ( $cTipoExporta == 'PDFP' ) {
				$cHTMLFILE .= $cTabelaHTML.'
				</body>
				</html>';

				$cHeader 	= '<strong>'.$cNomeTabela.'</strong>';
				$cFooter 	= 'Relatório gerado por '.SEC_USER_NOME.' em '.Date('d/m/Y').' ás '.Date('H:i:s');
				$cCamPDF	= $security->base_patch.'/assets/relatorios/';
				$cNomePDF	= 'CADASTRACERTO: '.$cNomeTabela.'_'.date('d-m-Y').'.pdf';
				
				//Para usar localhost \/
				$WKBIN	= $_SERVER['DOCUMENT_ROOT'].$security->base_patch.'/assets/lib/snappy/vendor/wkhtmltopdf/bin/wkhtmltopdf64';
				// $WKBIN	= '/usr/local/bin/wkhtmltopdf';

				require_once("class/snappyReport.class.php");
				$report = new snappyReportPDF("", $cNomePDF);
				$report->BuildPDF($WKBIN, $cHeader, $cFooter, 'A4', 'portrait','18','18','18','18', true);
				$report->snappyPDF->setTimeout(3600);

				// $report->SalvaPDF($cHTMLFILE, $cCamPDF.$cNomePDF);
				$report->ExibirPDF($cHTMLFILE);
				
				exit();	
			} elseif ( $cTipoExporta == 'PDFPDEB' ) {
				$cHTMLFILE .= $cTabelaHTML.'
				</body>
				</html>';

				$cHeader 	= '<strong>'.$cNomeTabela.'</strong>';
				$cFooter 	= 'Relatório gerado por '.SEC_USER_NOME.' em '.Date('d/m/Y').' ás '.Date('H:i:s');
				$cCamPDF	= $security->base_patch.'/assets/relatorios/';
				$cNomePDF	= 'CADASTRACERTO: '.$cNomeTabela.'_'.date('d-m-Y').'.pdf';
				
				//Para usar localhost \/
				$WKBIN	= $_SERVER['DOCUMENT_ROOT'].$security->base_patch.'/assets/lib/snappy/vendor/wkhtmltopdf/bin/wkhtmltopdf64';
				// $WKBIN	= '/usr/local/bin/wkhtmltopdf';

				require_once("class/snappyReport.class.php");
				$report = new snappyReportPDF("", $cNomePDF);
				$report->BuildPDF($WKBIN, $cHeader, $cFooter, 'A4', 'portrait','5','5','18','18', true);
				$report->snappyPDF->setTimeout(3600);

				// $report->SalvaPDF($cHTMLFILE, $cCamPDF.$cNomePDF);
				$report->ExibirPDF($cHTMLFILE);
				
				exit();	
			} elseif ( $cTipoExporta == 'PDFLDEB' ) {
				$cHTMLFILE .= $cTabelaHTML.'
				</body>
				</html>';

				$cHeader 	= '<strong>'.$cNomeTabela.'</strong>';
				$cFooter 	= 'Relatório gerado por '.SEC_USER_NOME.' em '.Date('d/m/Y').' ás '.Date('H:i:s');
				$cCamPDF	= $security->base_patch.'/assets/relatorios/';
				$cNomePDF	= 'CADASTRACERTO: '.$cNomeTabela.'_'.date('d-m-Y').'.pdf';
				
				//Para usar localhost \/
				$WKBIN	= $_SERVER['DOCUMENT_ROOT'].$security->base_patch.'/assets/lib/snappy/vendor/wkhtmltopdf/bin/wkhtmltopdf64';
				// $WKBIN	= '/usr/local/bin/wkhtmltopdf';

				require_once("class/snappyReport.class.php");
				$report = new snappyReportPDF("", $cNomePDF);
				$report->BuildPDF($WKBIN, $cHeader, $cFooter, 'A4', 'landscape','5','5','18','18', true);
				$report->snappyPDF->setTimeout(3600);

				// $report->SalvaPDF($cHTMLFILE, $cCamPDF.$cNomePDF);
				$report->ExibirPDF($cHTMLFILE);
				
				exit();	
			}
		
		} else {			
			echo '
			<script>
				window.close();
			</script>';
		}
	}
	exit();
?>