<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\ParametroDAO;
	use DateTime, Exception;

	$security 			= new Security();
	$parametroDAO 		= new ParametroDAO();

	if ( $security->Exist() ) {
		$post = file_get_contents("php://input");

		if( $post ) {
			$postData 				= json_decode($post);
			$lOk  					= false;
			$cReturnMsg				= '';

			$data_inicial			= $postData->data_inicial;
			$hora_inicial			= $postData->hora_inicial;
			$data_final				= $postData->data_final;
			$hora_final				= $postData->hora_final;

			$esforco_dia			= 0;
			$esforco_hora			= 0;
			
			$aHorasTrabalhadasDia 	= explode(';', $parametroDAO->getParametro('HRTRABDIA'));

			//Aqui vai calcular a qtde de horas trabalhadas por dia
			//Período da manhã
			$dHoraTrabInicial 		= new DateTime( Date('Y-m-d').' '.$aHorasTrabalhadasDia[0]);
			$dHoraTrabFinal 		= new DateTime( Date('Y-m-d').' '.$aHorasTrabalhadasDia[1]);

			$aHoraTrabaDiferenca	= $dHoraTrabInicial->diff($dHoraTrabFinal);
			$nHorasTrabalhadasDia	= $aHoraTrabaDiferenca->h + ($aHoraTrabaDiferenca->i / 60);

			//+ Período da tarde			
			$dHoraTrabInicial 		= new DateTime( Date('Y-m-d').' '.$aHorasTrabalhadasDia[2]);
			$dHoraTrabFinal 		= new DateTime( Date('Y-m-d').' '.$aHorasTrabalhadasDia[3]);

			$aHoraTrabaDiferenca	= $dHoraTrabInicial->diff($dHoraTrabFinal);
			$nHorasTrabalhadasDia	+= $aHoraTrabaDiferenca->h + ($aHoraTrabaDiferenca->i / 60);

			try {
				$lOk  					= true;
				$cReturnMsg				= 'Horas calculadas com sucesso.';
				$esforco_dia			= null;


				if ( $data_inicial < $data_final ) {
					$esforco_dia			= calcula_diferenca($data_inicial, '00:00:00', $data_final, '00:00:00', 'D');

					if ( $esforco_dia > 1 ) {
						$esforco_hora 	= 0;
						$diaInicial		= (int)Date('d', strtotime(implode("-", array_reverse(explode("/", $data_inicial)))));
						$diaFinal		= (int)Date('d', strtotime(implode("-", array_reverse(explode("/", $data_final)))));
						for ($i=1; $i <= $esforco_dia; $i++) { 
							if ( ($i <= $esforco_dia) AND ($i == 1) ) {
								if ( $hora_inicial < Date('H:i', strtotime($aHorasTrabalhadasDia[1])) ) {
									$esforco_hora			+= calcula_diferenca($data_inicial, $hora_inicial, $data_inicial, $aHorasTrabalhadasDia[1], 'H');
									$esforco_hora			+= calcula_diferenca($data_inicial, $aHorasTrabalhadasDia[2], $data_inicial, $aHorasTrabalhadasDia[3], 'H');

								} elseif ( $hora_inicial >= Date('H:i', strtotime($aHorasTrabalhadasDia[1])) ) {
									$esforco_hora			+= calcula_diferenca($data_inicial, $hora_inicial, $data_inicial, $aHorasTrabalhadasDia[3], 'H');

								}
							} elseif ( ($i < $esforco_dia) AND ($i > 1) ) {
								$esforco_hora			+= calcula_diferenca($data_inicial, $aHorasTrabalhadasDia[0], $data_inicial, $aHorasTrabalhadasDia[1], 'H');
								$esforco_hora			+= calcula_diferenca($data_inicial, $aHorasTrabalhadasDia[2], $data_inicial, $aHorasTrabalhadasDia[3], 'H');
							} elseif ( $i == $esforco_dia ) {
								if ( $hora_final <= Date('H:i', strtotime($aHorasTrabalhadasDia[2])) ) {
									$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[0], $data_final, $hora_final, 'H');
								} elseif ( $hora_final > Date('H:i', strtotime($aHorasTrabalhadasDia[2])) ) {
									$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[0], $data_final, $aHorasTrabalhadasDia[1], 'H');
									$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[2], $data_final, $hora_final, 'H');
								}
							}
						}
					} else {
						if ( ($hora_inicial < Date('H:i', strtotime($aHorasTrabalhadasDia[1]))) AND ($hora_final <= Date('H:i', strtotime($aHorasTrabalhadasDia[2]))) ) {
							$esforco_hora			= calcula_diferenca($data_inicial, $hora_inicial, $data_inicial, $aHorasTrabalhadasDia[1], 'H');
							$esforco_hora			+= calcula_diferenca($data_inicial, $aHorasTrabalhadasDia[2], $data_inicial, $aHorasTrabalhadasDia[3], 'H');

							$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[0], $data_final, $hora_final, 'H');

						} elseif ( ($hora_inicial < Date('H:i', strtotime($aHorasTrabalhadasDia[1]))) AND ($hora_final > Date('H:i', strtotime($aHorasTrabalhadasDia[2]))) ) {
							$esforco_hora			= calcula_diferenca($data_inicial, $hora_inicial, $data_inicial, $aHorasTrabalhadasDia[1], 'H');
							$esforco_hora			+= calcula_diferenca($data_inicial, $aHorasTrabalhadasDia[2], $data_inicial, $aHorasTrabalhadasDia[3], 'H');

							$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[0], $data_final, $aHorasTrabalhadasDia[1], 'H');
							$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[2], $data_final, $hora_final, 'H');

						} elseif ( ($hora_inicial >= Date('H:i', strtotime($aHorasTrabalhadasDia[1]))) AND ($hora_final <= Date('H:i', strtotime($aHorasTrabalhadasDia[2]))) ) {
							$esforco_hora			= calcula_diferenca($data_inicial, $hora_inicial, $data_inicial, $aHorasTrabalhadasDia[3], 'H');
							
							$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[0], $data_final, $hora_final, 'H');

						} elseif ( ($hora_inicial >= Date('H:i', strtotime($aHorasTrabalhadasDia[1]))) AND ($hora_final > Date('H:i', strtotime($aHorasTrabalhadasDia[2]))) ) {
							$esforco_hora			= calcula_diferenca($data_inicial, $hora_inicial, $data_inicial, $aHorasTrabalhadasDia[3], 'H');
							
							$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[0], $data_final, $aHorasTrabalhadasDia[1], 'H');
							$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[2], $data_final, $hora_final, 'H');
						}
					}
				} elseif ( $data_inicial == $data_final ) {
					$esforco_dia			= calcula_diferenca($data_inicial, $hora_inicial, $data_final, $hora_final, 'D');
					if ( ($hora_inicial < Date('H:i', strtotime($aHorasTrabalhadasDia[1]))) AND ($hora_final > Date('H:i', strtotime($aHorasTrabalhadasDia[1]))) ) {

						$esforco_hora			= calcula_diferenca($data_inicial, $hora_inicial, $data_final, $aHorasTrabalhadasDia[1], 'H');
							
						$esforco_hora			+= calcula_diferenca($data_final, $aHorasTrabalhadasDia[2], $data_final, $hora_final, 'H');

					} else {
						$esforco_hora			= calcula_diferenca($data_inicial, $hora_inicial, $data_final, $hora_final, 'H');
					}
				} elseif ( $data_inicial > $data_final ) {	
					$esforco_diferenca		= null;
					$esforco_dia			= null;
					$esforco_hora			= null;				
					$lOk  					= false;
					$cReturnMsg				= 'Erro ao calcular as horas. <br/><b>Erro:</b> Data inicial maior que a data Final.';
				}

			} catch (Exception $e) {
				$esforco_diferenca		= null;
				$esforco_dia			= null;
				$esforco_hora			= null;
				$lOk  					= false;
				$cReturnMsg				= 'Erro ao calcular as horas. <br/><b>Erro:</b> '.$e->getMessage();
			}

			echo json_encode(array("return"=>$lOk, "msg"=>$cReturnMsg, "esforco_dia"=>round($esforco_dia, 2), "esforco_hora"=>round($esforco_hora, 2)));
		}
	}

	function calcula_diferenca($cDataIni, $cHoraIni, $cDataFim, $cHoraFim, $cTipo = 'H') {
		$dDataInicio 	= new DateTime( implode("-", array_reverse(explode("/", $cDataIni))).' '.$cHoraIni);
		$dDataFim 		= new DateTime( implode("-", array_reverse(explode("/", $cDataFim))).' '.$cHoraFim);
		$aDiferenca		= $dDataInicio->diff($dDataFim);
		if ( $cTipo == 'H' ) {
			return			$aDiferenca->h + ($aDiferenca->i / 60);
		} elseif ( $cTipo == 'D' ) {
			return			$aDiferenca->d;
		} else {
			return 0;
		}
	}
?>
