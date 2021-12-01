<?php
	namespace TicketSys\Model\Classes;
	use PDO, PDOException;
	
	class EmailHelper extends Security {
		/**
		* Função que gera uma nova senha e envia por e-mail
		* @param string $Email 	- E-mail a ser recuperado a senha.
		* @param String $cMsg 	- Retorna a mensagem de erro de acordo com a validação do envio/alteração da senha.
		* @return bool 			- Se e-mail foi enviado com êxito ou não (true/false).
		*/
		function recoveryPassword($Email, &$cMsg) {
			$cS = ($this->caseSensitive) ? 'BINARY' : '';
			// Usa a função addslashes para escapar as aspas
			$cEmail = addslashes($Email);
			// Monta uma consulta SQL (query) para procurar um usuário
			$sql = "SELECT user_id,user_nome,user_email,user_passwd,user_token
						FROM usuario 
						WHERE user_delete = ''
							AND ".$cS." user_email = '".$cEmail."' ";
			
			$stmt = $this->conn->prepare($sql);
		    $stmt->execute();
			// Verifica se encontrou algum registro
			if ($stmt->rowCount() > 0) {
				$resultado = $stmt->fetch(PDO::FETCH_ASSOC);				
				$cCodUser	= $resultado['user_id'];
				$cNomeUser	= $resultado['user_email'];
				$cCpfUser	= '';
				$cEmailUser	= $resultado['user_email'];			    
				$data_envio = date('d/m/Y');
				$hora_envio = date('H:i:s');
				$cNewSenha	= substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?"), 0, 8);
				
				$assunto		= "Redefinição da sua senha Cadastra Certo!";

				//Monta o corpo do e-mail para o envio
				$corpo = 'Olá '.$cNomeUser.', Tudo bem? <br/><br/>
				Sua solicitação de recuperação de senha na Minha Corrida foi recebida com êxito.<br/><br/>
				Dados da sua conta: <br/>
				<b>CPF:</b> <i>'.$cCpfUser.'</i><br/>
				<b>E-mail:</b> <i>'.$cEmailUser.'</i><br/><br/>
				<b>Nova senha:</b> <i>'.$cNewSenha.'</i>
				<br/><br/>
				Caso você não tenha solicitado a alteração da senha favor contatar a Equipe Codeware SI, ou acesse o site com sua nova senha e faça a alteração.
				<br/><br/>
				Para alterar sua senha basta acessar o sistema com a nova senha e ir no menu em "Configurações>Alterar Senha".
				<br/><br/>
				Este e-mail foi enviado em <b>'.$data_envio.'</b> às <b>'.$hora_envio.'</b>.
				<br/><br/>
				<b>© Equipe Codeware SI.</b><br/>
				<a href="www.Minha Corrida.codewaresistemas.com.br">www.Minha Corrida.codewaresistemas.com.br</a>';

				$mail = new PHPMailer();

				$mail->IsSMTP(); 
				$mail->SMTPAuth = true;
				$mail->Host  	= $this->cHost;
				$mail->Port  	= $this->cPort;
				$mail->Username = $this->emailenviar;
				$mail->Password = $this->cSenhaEmail;
				
				$mail->From  	= $this->emailenviar;
				$mail->FromName = utf8_decode($this->ServerName);			
				
				$mail->AddAddress($cEmailUser,utf8_decode($cNomeUser));
				$mail->IsHTML(true);
				$mail->Charset  = 'utf8_decode()';

				$mail->Subject  = utf8_decode($assunto);
				$mail->Body  	= utf8_decode($corpo);

				if($mail->Send()){
					$sqlupd = "UPDATE usuario SET user_passwd = '".MD5($cNewSenha)."' WHERE user_id = ".$cCodUser;
					$stmtupd = $this->conn->prepare($sqlupd);
				    $stmtupd->execute(); 

					return true;
				} else { 
					$cMsg = $mail->ErrorInfo;
					return false;
				}
				$mail->ClearAllRecipients();
				$mail->ClearAttachments();
			} else { 
				return false;
			}
		}


		function EmailTicket($emails_dest,$nomes_dest,$emails_copia,$nomes_copia,$corpo,$assunto,$cCamArq,$cArq,$lAuditor,&$cMsg) { 
			$cAssunto 		= addslashes($assunto);

			$aEmailsDest 	= explode(";", $emails_dest);
			$aNomesDest 	= explode(";", $nomes_dest);

			$aEmailsCopia 	= explode(";", $emails_copia);
			$aNomesCopia 	= explode(";", $nomes_copia);

			$mail = new PHPMailer();

			$mail->IsSMTP(); 
			$mail->SMTPAuth = true;
			$mail->Host  	= $this->cHost;
			$mail->Port  	= $this->cPort;
			$mail->Username = $this->emailenviar;
			$mail->Password = $this->cSenhaEmail;
			
			$mail->From  	= $this->emailenviar;
			$mail->FromName = utf8_decode(($lAuditor ? 'Auditor NFe' : $this->ServerName));
			
			for ($d=0; $d < count($aEmailsDest); $d++) { 
				if ( !Empty($aEmailsDest[$d]) ) {
					$mail->AddAddress($aEmailsDest[$d], (isset($aNomesDest[$d]) ? $aNomesDest[$d] : ''));
				}
			}

			if ( (!Empty($emails_copia)) AND (!Empty($nomes_copia)) ) {
				for ($c=0; $c < count($aEmailsCopia); $c++) { 
					if ( !Empty($aEmailsCopia[$c]) ) {
						$mail->AddCC($aEmailsCopia[$c], (isset($aNomesCopia[$c]) ? $aNomesCopia[$c] : ''));
					}
				}
			}

			$mail->IsHTML(true);
			$mail->Charset  = 'utf8_decode()';

			$mail->Subject  = utf8_decode($cAssunto);
			$mail->Body  	= utf8_decode($corpo);
			if ( (!Empty($cCamArq)) AND (!Empty($cArq)) ) {
				$mail->AddAttachment($cCamArq.$cArq, $cArq);
			}

			// $mail->AddCustomHeader( 'X-pmrqc: 1' );
			// $mail->AddCustomHeader( "X-Confirm-Reading-To: ".$this->emailenviar );
			// $mail->AddCustomHeader( "Return-receipt-to: ".$this->emailenviar );
			// $mail->AddCustomHeader( "Disposition-Notification-To:<".$this->emailenviar.">");
			// $mail->ConfirmReadingTo = $this->emailenviar;	

			if($mail->Send()){
				$cMsg = 'E-mail enviado com sucesso.';
				return true;
			} else { 
				$cMsg = $mail->ErrorInfo;
				return false;
			}
			$mail->ClearAllRecipients();
			$mail->ClearAttachments();
		}

	}
?>