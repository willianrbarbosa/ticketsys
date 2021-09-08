<?php
	session_start();

	ini_set('max_execution_time', 2400);
	set_time_limit(2400);
	
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);
	
	include('class/security.class.php');
	include('class/graficosDAO.class.php');
	include('session_vars.php');

	$security 			= new Security();
	$graficosDAO 		= new graficosDAO();
	$aGrafico			= Array();

	if ( SESSION_EXISTS ) {
		if (!Empty($_GET)) {
			
			if ( $_GET['chart'] == 'TT' ) {
				$cDataDe  	= implode('-', array_reverse( explode('/', str_replace("'", "", $_GET['data_de']) ) ) );
				$cDataAte 	= implode('-', array_reverse( explode('/', str_replace("'", "", $_GET['data_ate']) ) ) );
				$aGrafico 	= $graficosDAO->getTotaisTickets($cDataDe, $cDataAte);
			} elseif ( $_GET['chart'] == 'TTH' ) {
				$cDataDe  	= implode('-', array_reverse( explode('/', str_replace("'", "", $_GET['data_de']) ) ) );
				$cDataAte 	= implode('-', array_reverse( explode('/', str_replace("'", "", $_GET['data_ate']) ) ) );
				$aGrafico 	= $graficosDAO->getTotaisHorasTickets($cDataDe, $cDataAte);
			}

			echo json_encode($aGrafico);
		}

	}
?>