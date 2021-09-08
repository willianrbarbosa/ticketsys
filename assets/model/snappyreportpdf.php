<?php
	session_start();
	
	ini_set('max_execution_time', 10000);
	set_time_limit(10000);
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);

	ini_set('memory_limit', -1); 
	ini_set("pcre.backtrack_limit", "5000000");
	ini_set('default_socket_timeout', 50000);
		
	require_once('class/security.class.php');
	require_once('class/clienteDAO.class.php');
	include('session_vars.php');


	$security = new Security();
	if ( SESSION_EXISTS ) {	
		$aHTMLFile		= array(
			'/assets/relatorios/REL_EDITADO_HTML.html'
		);

		$nCliTk 		= 1012;

		$clienteDAO		= new clienteDAO();
		$aCliente		= $clienteDAO->buscaById($nCliTk);

		$cHeader 		= '<strong>RELATÓRIO DE PRODUTOS DIVERGENTES</strong><br>'.$aCliente['cli_id'].' - '.$aCliente['cli_razao_social'].' <strong>(CNPJ: '.$aCliente['cli_cnpj'].')</strong>';
		$cFooter 		= 'Relatório gerado por '.SEC_USER_NOME.' em '.Date('d/m/Y').' ás '.Date('H:i:s');
		$cCamPDF		= $security->base_patch.'/assets/relatorios/';
		$cNomePDF		= 'CADASTRA CERTO - RELATÓRIO DE PRODUTOS DIVERGENTES - '.$security->sanitizeString($aCliente['cli_razao_social']).' - '.$security->OnlyNumbers($aCliente['cli_cnpj']).'_'.date('d-m-Y').'.pdf';

		
		//Para usar localhost \/
		// $WKBIN	= $_SERVER['DOCUMENT_ROOT'].$security->base_patch.'/assets/lib/snappy/vendor/wkhtmltopdf/bin/wkhtmltopdf64';
		$WKBIN	= '/usr/local/bin/wkhtmltopdf';

		require_once("class/snappyReport.class.php");
		$report = new snappyReportPDF("", $cNomePDF);
		$report->BuildPDF($WKBIN, $cHeader, $cFooter, 'landscape','15','15','18','18');

		$cPrintPDF	= '';
		for ($h=0; $h < count($aHTMLFile); $h++) {
			$cPrintPDF		.= file_get_contents(trim($_SERVER['DOCUMENT_ROOT'].$security->base_patch.$aHTMLFile[$h]));
		}	


		// $report->SalvaPDF($cPrintPDF, $cCamPDF.$cNomePDF);
		$report->ExibirPDF($cPrintPDF);
		
		exit();
	} else {
		echo '
		<script>
			alert("Usuário não logado."); 
			window.close();
		</script>';
	}
?>