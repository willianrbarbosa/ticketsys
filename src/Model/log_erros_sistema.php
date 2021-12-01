<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\PerfilAcessoRotinaDAO;	
	include('session_vars.php');

	$perfilAcessoRotinaDAO 	= new PerfilAcessoRotinaDAO();

	if ( SESSION_EXISTS ) {
		$aUserRotina = $perfilAcessoRotinaDAO->buscaByPerfilRotina('errorslog', SEC_USER_PFA_ID);

		if($_GET){
			if ( $aUserRotina <> false ) {
				$dir = "../../log/";	
				
				if($_GET['action'] == 'search'){
					$logErrosSistema = Array();
					// Abre um diretorio conhecido, e faz a leitura de seu conteudo
					if (is_dir($dir)) {
					    if ($dh = opendir($dir)) {
					        while (($file = readdir($dh)) !== false) {
					        	$log_content = '';
					        	if(($file <> '.') AND ($file <> '..')){

					        		//CONVERTER DE BYTES PARA OUTROS TAMANHOS
						        	$size 			= (filesize($dir.$file));
								    // $units 		= array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
								    $units 			= array( 'B', 'KB', 'MB', 'GB');
								    $power 			= $size > 0 ? floor(log($size, 1024)) : 0;
								    $convertedSize 	= number_format($size / pow(1024, $power), 2);

								    $path = $security->base_patch.'/assets/arquivos/'.$file;

								    //Verifica se é maior do que 20mb ou se é GB, caso seja, não deixará visualizar.
								    if(($units[$power] == 'MB')){
								    	if($convertedSize > 20){
											$logView = 'N';							    		
								    	}else{
								    		$logView ='S';
								    		$log_content = file_get_contents($dir.$file);
								    	}
								    }elseif(($units[$power] == 'GB')){
								    	$logView = 'N';
									}else{
								    	$logView = 'S';
								    	$log_content = file_get_contents($dir.$file);
								    }


						            array_push($logErrosSistema, array("log_name"=>$file,
						            								   "log_size"=>$convertedSize." ".$units[$power],
						            								   "log_path"=>$path,
						            								   "log_date"=>date ("d-m-Y H:i:s", filemtime($dir.$file)),
						            								   "log_view"=>$logView,
						            								   "log_cont"=>$log_content));
						        }
					        }
					        closedir($dh);
					    }
					}

					echo json_encode($logErrosSistema);
				}elseif($_GET['action'] == 'delete'){
					if (is_dir($dir)) {
					    if ($dh = opendir($dir)) {

							$file = $_GET['file'];
							unlink($dir.$file);

					        closedir($dh);
					    }
					}

					echo json_encode(array("return"=>true, "file"=>$file));
				}
			} else {
				echo json_encode(array('error' => 'Usuario sem acesso a essa rotina.'));			
			}
		}
	} else {
		echo json_encode(array('error' => 'Usuario nao logado.'));			
	}
?>