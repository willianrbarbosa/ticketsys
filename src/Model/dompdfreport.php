<?php
    // ob_start();
	// session_start();
	
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

	$security = new Security();

	$aHTMLFile		= array(
		'/assets/relatorios/1_divergentReport_2020-10-23_11-10-09_1012_S_Administrador.html',
		'/assets/relatorios/2_divergentReport_2020-10-23_11-10-09_1012_S_Administrador.html',
		'/assets/relatorios/3_divergentReport_2020-10-23_11-10-09_1012_S_Administrador.html',
		'/assets/relatorios/4_divergentReport_2020-10-23_11-10-09_1012_S_Administrador.html',
		'/assets/relatorios/5_divergentReport_2020-10-23_11-10-09_1012_S_Administrador.html',
		'/assets/relatorios/6_divergentReport_2020-10-23_11-10-09_1012_S_Administrador.html',
		'/assets/relatorios/7_divergentReport_2020-10-23_11-10-09_1012_S_Administrador.html',
		'/assets/relatorios/8_divergentReport_2020-10-23_11-10-09_1012_S_Administrador.html',
		'/assets/relatorios/9_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/10_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/11_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/12_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/13_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/14_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/15_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/16_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/17_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html',
		'/assets/relatorios/18_divergentReport_2020-10-23_11-11-09_1012_S_Administrador.html'
	);
	// echo $_SERVER['DOCUMENT_ROOT'].'<br/>';
	// var_dump($aHTMLFile); exit();

	$nCliTk 		= 1012;

	$clienteDAO		= new clienteDAO();
	$aCliente		= $clienteDAO->buscaById($nCliTk);

	$cHeader = '<strong>RELATÓRIO DE PRODUTOS DIVERGENTES</strong><br>'.$aCliente['cli_id'].' - '.$aCliente['cli_razao_social'].' <strong>(CNPJ: '.$aCliente['cli_cnpj'].')</strong>';
	
	// echo $cHTMLFILE;
	// gc_enable();

	require_once("class/report.class.php"); //../lib/bootstrap/css/bootstrap.min.css
	$report = new reportCliente("", "CADASTRA CERTO: RELATÓRIO DE PRODUTOS DIVERGENTES", "Produtos Divergentes");  
	$report->BuildPDF($cHeader, 'Relatório gerado por Administrador', 'A4-L','15','15','28','18');
	$report->pdf->SetTitle('CADASTRA CERTO: RELATÓRIO DE PRODUTOS DIVERGENTES');

	for ($h=0; $h < count($aHTMLFile); $h++) {
		$cPirntPDF		= file_get_contents(trim($_SERVER['DOCUMENT_ROOT'].$aHTMLFile[$h]));
		$report->pdf->WriteHTML($cPirntPDF);
	}
	
	// gc_collect_cycles();
	// ob_end_clean();	
	$cHTMLFILE = null;		
	// $report->BaixaPDF('SÍBIA - RELATÓRIO DE PRODUTOS DIVERGENTES - '.$aCliente['cli_razao_social'].' - '.$aCliente['cli_cnpj'].'_'.date('d-m-Y').'.pdf');
	$cNomePDF	= $security->base_patch.'/assets/prod_divergente.pdf';
	$report->SalvaPDF($_SERVER['DOCUMENT_ROOT'].$cNomePDF);
	
	// header("Location: http://localhost/".$security->base_patch.$cNomePDF);

	echo '<script>
		window.location.href = "https://app.cadastracerto.com.br/'.$cNomePDF.'";
	</script>';

	exit();
?>