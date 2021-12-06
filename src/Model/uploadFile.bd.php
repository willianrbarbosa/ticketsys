<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use Exception;
	
	setlocale( LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese' );
	date_default_timezone_set( 'America/Sao_Paulo' );

    $lFileExt	= false;
    $lOk	 	= false;
    $nRetMsg  	= '';

    $cExtensoesBloqueadas = '.zip;.rar;.7z;';
    $cExtensoesBloqueadas .= '.exe;.msi;.bat;.ini;.msc;.com;.cmd;.hta;.scr;.pif;.reg;.bin;.cab;.cdi;.cfg;.lnk;.rtf;.tmp;';
    $cExtensoesBloqueadas .= '.mfd;.mdb;';
    $cExtensoesBloqueadas .= '.bak;.bkp;.log;.sql;.dbf;.gdb;.fdb;.php;.htm;.html;.js;.vbs;.wsf;.cpl;.jar;.js;.ws;.thmx;.css;';
    $cExtensoesBloqueadas .= '.avi;.mp4;.mp3;.mid;.wmv;.wma;.mpg;.wav;';

    $letras 	= "/^[a-z]+$/";
    $numeros 	= "/^[0-9]+$/";
    $patterntot	= "/^[a-z0-9]+$/";
	
	$security 		= new Security();

	if ( $security->Exist() ) {
	    try {
			if ( isset( $_FILES[ 'file' ][ 'name' ] ) && $_FILES[ 'file' ][ 'error' ] == 0 ) {
				$oArquivo = $_FILES['file'];
				if ( $oArquivo['size'] > 4971520 ) {
				    $nRetMsg 	= 'Arquivo selecionado é maior do que tamanho máximo permitido (20 Mb). Verifique!!';
			        $lOk		= false;
				} else {
					$cPastaBase	= '';
					if ( isset($_POST['origem']) ) {
						if ( $_POST['origem'] == 'T' ) {
							$cPastaBase	= 'tickets/';
						} elseif ( $_POST['origem'] == 'U' ) {
							$cPastaBase	= 'usuarios/';
						}
					}

					$fileUpload_tmp = $oArquivo['tmp_name'];
				    // Pega a extensão
				    $extensao = pathinfo ( $oArquivo['name'], PATHINFO_EXTENSION );		 
				    // Converte a extensão para minúsculo
				    $extensao = strtolower ( $extensao );
				    // Somente imagens, arquivos TXT e arquivos Excel, .jpg;.jpeg;.gif;.png;.txt;.xls;.xlsx;.csv
				    if ( strstr ( $cExtensoesBloqueadas, $extensao ) ) {
				    	$lFileExt = false;
				    } else {
				        // Concatena a pasta com o nome
				        $fileUpload_new = strtolower($security->sanitizeString('../../src/importacoes/'.$cPastaBase.$security->getUser_id().'_'.Date('Y-m-d-H-m').'_'.$oArquivo['name']));
				        $lFileExt = true;
				    }

				 	if ( $lFileExt ) {
				        // tenta mover o arquivo para o destino		        
				        if ( move_uploaded_file ($fileUpload_tmp, $fileUpload_new) ) {
				            $nRetMsg 	= 'Arquivo '.$oArquivo['name'].' salvo no servidor com sucesso.';
				            $lOk		= true;

				        } else {
				            $nRetMsg 	=  'Erro ao salvar o arquivo no servidor.';
				            $lOk		= false;
				        }
				    } else {
					    $nRetMsg 	= 'É permitido apenas envio de arquivos do tipo "*.jpg;*.jpeg;*.gif;*.png;*.pdf;*.txt;*xls;*xlsx;*.csv;*.doc;*.docx;*.ppt;*.pptx;".';
				        $lOk		= false;
				    }
				}
			} else {
				$nRetMsg  	= 'Erro no recebimento do arquivo. Verifique o tamanho/extensão e tente novamente.';	
				$lOk	 	= false;		
			}    		
		} catch (Exception $e) {
			$nRetMsg  	= $e->getMessage();	
			$lOk	 	= false;
		}	

		echo json_encode(array("return"=>$lOk, "retmsg"=>$nRetMsg));
	}

?>