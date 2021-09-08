<?php
    include_once('class/security.class.php');
	$security 					= new Security();	

    if ( $security->Exist() ) {
        define("SESSION_EXISTS", $security->Exist());
        define("SEC_USER_ID", $security->getUser_id());
        define("SEC_USER_NOME", $security->getUser_nome());
        define("SEC_USER_EMAIL", $security->getUser_email());
        define("SEC_USER_TIPO", $security->getUser_tipo());
        define("SEC_USER_PFA_ID", $security->getUser_pfa_id());
        define("SEC_USER_TOKEN", $security->getUser_token());
    } else {
        define("SESSION_EXISTS", false);
        define("SEC_USER_ID", null);
        define("SEC_USER_NOME", null);
        define("SEC_USER_EMAIL", null);
        define("SEC_USER_TIPO", null);
        define("SEC_USER_PFA_ID", null);
        define("SEC_USER_TOKEN", null);
    }
    
	session_commit();
?>