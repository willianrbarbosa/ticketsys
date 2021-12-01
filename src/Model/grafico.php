<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\GraficosDAO;	
	include('session_vars.php');
	
	$graficosDAO 		= new GraficosDAO();
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