<?php
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	include('../model/class/security.class.php');
	$security = new Security();	
?>
<style type="text/css">	
	.navbar {
		height: 80px !important;		
	}
	.navbar-brand span {
	  margin-left: 10px;
	  font-size: 18px;
	  font-weight: bold;
	}
	.navbar-brand img {
	  height: 60px;
	}
	body {
		margin: 0 !important;
	    background-color: #FFF;
	}
	html {
		padding: 0 !important
	    background-color: green;
	    max-height: 50% !important;
	}
	.footer {
		position: absolute;
		bottom: 0;
		width: 100%;
		height: 60px; /* Set the fixed height of the footer here */
		line-height: 60px; /* Vertically center the text there */
		background-color: #f5f5f5;
		margin: 0 !important;
	}
	.modal-content  {
	    -webkit-border-radius: 0px !important;
	    -moz-border-radius: 0px !important;
	    border-radius: 0px !important; 
	}
</style>

<div id="navbar" class="navbar navbar-default navbar-bd <?php echo ($security->base_patch == '/base_teste' ? 'bg-red' : ''); ?>">	
	<a href="http://ticketfiscal.com.br/" class="navbar-brand">
  		<div class="ft-white"><img src="<?php echo $security->base_patch.'/assets/img/'.($security->base_patch == '/base_teste' ? 'testes.png' : 'ticket.png'); ?>" title="Ticket SYS WEB" type="image/png"></div>
  	</a>
</div>	

<div class="container" style="margin-top: 100px;">

	<div class="row">
		<div class="col-sm-4 col-sm-offset-4 shadow">

        	<div class="row form-group">
				<div class="col-sm-12 center-justify">
					<h3 class="ft-ticket"><strong> CONFIRMAÇÃO CADASTRAL <br/> PERÍODO DE AVALIAÇÃO </strong></h3>
				</div>
			</div>
			<hr/>

			<div ng-if="eDadosClienteSite.cls_id">
				<div class="form-group row">
		        	<div class="col-sm-12">
			        	<label>CNPJ: </label><br/>
			            <span>{{eDadosClienteSite.cls_cnpj}}</span>
			        </div>
			    </div>

				<div class="form-group row">
		        	<div class="col-sm-12">
			        	<label>Razão Social: </label><br/>
			            <span>{{eDadosClienteSite.cls_razao_social}}</span>
			        </div>
			    </div>

				<div class="form-group row">
		        	<div class="col-sm-12">
			        	<label>Nome Fantasia: </label><br/>
			            <span>{{eDadosClienteSite.cls_nome_fantasia}}</span>
			        </div>
			    </div>

				<div class="form-group row">
		        	<div class="col-sm-6">
			        	<label>Cidade/UF: </label><br/>
			            <span>{{eDadosClienteSite.cls_cidade}}/{{eDadosClienteSite.cls_estado}}</span>
			        </div>
		        	<div class="col-sm-6">
			        	<label>Contato: </label><br/>
			            <span>{{eDadosClienteSite.cls_user_nome}}</span>
			        </div>
			    </div>
			</div>

			<div class="form-group row" ng-if="!eDadosClienteSite.cls_id">		
		        <div class="col-sm-12">	
					<div class="alert alert-warning center-justify">
						<strong><i class="fa fa-3x fa-meh-o"></i></strong> <br/> Cadastro não solicitado ou já validado.
					</div>
				</div>
			</div>

	    </div>

	</div>
	<div class="row" ng-if="eDadosClienteSite.cls_id">
		<div class="col-sm-4 col-sm-offset-4 bg-primary shadow">
			<button type="button" class="btn btn-md btn-block btn-link ft-white" ng-click="ValidAccount(eDadosClienteSite)">CONFIRMAR MEU CADASTRO</button>
		</div>
	</div>

</div>

<footer class="footer">
	<div class="container">
		<span class="ft-ticket"><strong>EQUIPE Ticket SYS</strong></span>
	</div>
</footer>

<div id="mdValidaCadastro" class="modal fade">				
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-body">
		
				<div class="row form-group" ng-if="!lValidouCNPJ">
					<div class="col-sm-12 center-justify">
						<div class="alert alert-warning center-justify">
							<span id="loading">
								<div class="loading-img">
								Validando seu cadastro. Aguarde...</div>
							</span>
						</div>
					</div>
				</div>

				<div ng-if="lValidouCNPJ">					
					<div class="row form-group">
						<div class="col-sm-12 center-justify">
							<i class="fa fa-5x fa-check-circle ft-success"></i>
							<h4 class="ft-ticket"><strong> SUCESSO </strong></h4>
						</div>
					</div>

					<div class="row form-group">
						<div class="col-sm-12 center-justify">
							<label class="ft-gray-dk">Cadastro confirmado com sucesso.<br/>A partir de agora você já pode testar o sistema cadastracerto <br/>durante 7 dias totalmente gratuito. Seja bem-vindo!</label>
						</div>
					</div>

					<div class="row form-group" ng-if="eDadosClienteSite.cls_id">
						<div class="col-sm-12">
							<a href="https://cadastracerto.com.br/app/#/login" class="btn btn-md btn-block btn-default ft-success shadow"><strong>ACESSAR O SISTEMA</strong></a>
						</div>
					</div>
				</div>


			</div>

		</div>
	</div>
</div>