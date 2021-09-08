<?php
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	include('../model/class/security.class.php');
	$security = new Security();	
?>

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title bold">Configurações</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<a href="#/myaccount/{{eUsuario.user_token}}" class="kt-subheader__breadcrumbs-link bold">&nbsp;Meus dados</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">					
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-primary btn-salvar" ng-click="edtForm.$invalid || editUser(eUsuario)" ng-if="aUserAccess.pta_nivel >= 2" ng-disabled="edtForm.$invalid">
						Salvar
					</a>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="row">
		<div class="col-xs-12 kt-portlet" >	

			<div class="kt-portlet__body">

				<form name="edtForm" role="form" >
					<br>	

					<div class="row row-title-fields">
						<button type="button" class="btn btn-xs btn-link" data-toggle="collapse" data-target="#principal"><i class="fa fa-caret-down"></i> Informações Gerais<span class="nd-text">(obrigatório)</span></button>
					</div>
					<div id="principal" class="collapse in">
						<div class="form-group row">
				        	<div class="col-sm-2">
					        	<label>Registro</label>
					            <input type="text" name="user_id" id="user_id" ng-maxlength="11" class="form-control my-control" ng-model="eUsuario.user_id" readonly />
					        </div>
					    </div>

						<div class="form-group row">
					        <div class="col-sm-12">
						        <div ng-class="{ 'has-error': edtForm.user_nome.$dirty && (edtForm.user_nome.$error.required || edtForm.user_nome.$error.maxlength) }" >
						        	<label>Nome</label><span class="txt-obg">*</span>
					        		<input type="text" name="user_nome" id="user_nome" ng-maxlength="120" maxlength="120" class="form-control my-control" placeholder="Nome completo" ng-model="eUsuario.user_nome" required />
						        </div>			       			        
					        </div>
					    </div>

						<div class="form-group row">
					        <div class="col-sm-8">
						        <div ng-class="{ 'has-error': edtForm.user_email.$dirty && (edtForm.user_email.$error.required || edtForm.user_email.$error.maxlength || edtForm.user_email.$error.email) }" >
						        	<label>E-mail</label><span class="txt-obg">*</span>
					        		<input type="email" name="user_email" id="user_email" maxlength="80" ng-maxlength="80" class="form-control my-control" placeholder="E-mail" ng-model="eUsuario.user_email" required readonly />
						        </div>		     
					        </div>
					        <div class="col-sm-4">
					        	<label>Senha de acesso</label>
					            <input type="password" name="user_passwd" id="user_passwd" maxlength="40" class="form-control my-control" ng-model="eUsuario.user_passwd" placeholder="Digite apenas caso queira alterar..." />
					        </div>
					    </div>


					</div>
					<hr>

				</form>

			</div>
		</div>
	</div>
</div>
<br>