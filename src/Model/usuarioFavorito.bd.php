<?php
	namespace TicketSys\Model;
	require_once "../../vendor/autoload.php";
	session_start();
	
	use TicketSys\Model\Classes\Security;
	use TicketSys\Model\Classes\UsuarioFavoritoDAO;
	use TicketSys\Model\Classes\UsuarioFavorito;

	$security = new Security();
	if ( $security->Exist() ) {
		$post = file_get_contents("php://input");

		if($post){
			$postData 			= json_decode($post);
			$usuarioFavoritoDAO = new UsuarioFavoritoDAO();	

			if ($postData->ctrlaction == 'new') {
				$ufv_id 		= 0;
				$ufv_user_id 	= (isset($postData->ufv_user_id) ? $postData->ufv_user_id : $security->getUser_id());
			} else {
				$ufv_id 		= $postData->ufv_id;
				$ufv_user_id 	= $postData->ufv_user_id;
			}
			$ufv_descricao 		= $postData->ufv_descricao;
			$ufv_categoria 		= $postData->ufv_categoria;
			$ufv_url 			= $postData->ufv_url;

		    $usuarioFavorito = new UsuarioFavorito($ufv_id,$ufv_user_id,$ufv_descricao,$ufv_categoria,$ufv_url);

			$nRetId = $ufv_id;

			if ($postData->ctrlaction == 'new') {
				if ($usuarioFavoritoDAO->Insere($usuarioFavorito)) {
					$lOk 	= true;
					$nRetId = $usuarioFavoritoDAO->id_inserido;
				} else {
					$lOk = false;
				}	
			} elseif ($postData->ctrlaction == 'edit') {			
				if ($usuarioFavoritoDAO->Altera($usuarioFavorito)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			} elseif ($postData->ctrlaction == 'delete') {			
				if ($usuarioFavoritoDAO->Deleta($usuarioFavorito)) {
					$lOk = true;
				} else {
					$lOk = false;
				}
			}

			echo json_encode(array("return"=>$lOk, "id"=>$nRetId));
		}
	}

?>