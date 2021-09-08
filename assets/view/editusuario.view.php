<?php
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	include('../model/class/security.class.php');
	$security = new Security();	
?>

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title bold">Cadastros</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<a href="#/users" class="kt-subheader__breadcrumbs-link bold">&nbsp;Usuários</a>
			</div>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<span class="kt-subheader__breadcrumbs">&nbsp;Editar</span>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-primary btn-salvar" ng-click="edtForm.$invalid || editUser(eUsuario)" ng-if="aUserAccess.pta_nivel >= 2" ng-disabled="edtForm.$invalid">
						Salvar
					</a>

					<a href="" class="btn btn-sm  btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações Relacionadas">
						<i class="fa fa-ellipsis-h"></i>
					</a>

					<div class="dropdown-menu dropdown-menu-fit dropdown-menu-md dropdown-menu-right">
						<!--begin::Nav-->
						<ul class="kt-nav">
							<li class="kt-nav__head"><strong>Ações relacionadas:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="#/users" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-chevron-left ft-primary"></i> <span class="kt-nav__link-text">Voltar à listagem</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Editar Usuário ' + eUsuario.user_nome, '2. Cadastros', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-question-circle"></i> <span class="kt-nav__link-text">Ajuda</span>
								</a>
							</li>
						</ul>
						<!--end::Nav-->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="row">
		<div class="col-xs-12 kt-portlet" >	

			<div class="kt-portlet__body">

				<div class="alert alert-warning" ng-if="!aUserAccess.pta_nivel || aUserAccess.pta_nivel < 2">
					Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.
					<a href="#/users" class="btn-xs btn-link" >Voltar à listagem</a>
				</div>

				<form name="edtForm" role="form" ng-show="aUserAccess.pta_nivel >= 2">
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
					        <div class="col-sm-10">
						        <div ng-class="{ 'has-error': edtForm.user_nome.$dirty && (edtForm.user_nome.$error.required || edtForm.user_nome.$error.maxlength) }" >
						        	<label>Nome</label><span class="txt-obg">*</span>
					        		<input type="text" name="user_nome" id="user_nome" ng-maxlength="120" maxlength="120" class="form-control my-control" placeholder="Nome completo" ng-model="eUsuario.user_nome" required />
						        </div>			       			        
					        </div>
					    </div>

						<div class="form-group row">
					        <div class="col-sm-6">
						        <div ng-class="{ 'has-error': edtForm.user_email.$dirty && (edtForm.user_email.$error.required || edtForm.user_email.$error.maxlength || edtForm.user_email.$error.email) }" >
						        	<label>E-mail</label><span class="txt-obg">*</span>
					        		<input type="email" name="user_email" id="user_email" maxlength="80" ng-maxlength="80" class="form-control my-control" placeholder="E-mail" ng-model="eUsuario.user_email" required readonly />
						        </div>		     
					        </div>
					        <div class="col-sm-3">
					        	<label>Senha de acesso</label>
					            <input type="password" name="user_passwd" id="user_passwd" maxlength="40" class="form-control my-control" ng-model="eUsuario.user_passwd" placeholder="Digite apenas caso queira alterar..." />
					        </div>
					        <div class="col-sm-3">
						        <div ng-class="{ 'has-error': edtForm.user_tipo.$dirty && edtForm.user_tipo.$error.required }" >
					        		<label>Tipo</label><span class="txt-obg">*</span>
									<selectize name="user_tipo" id="user_tipo" config="cfgType" options="aTypes" ng-model="eUsuario.user_tipo" required></selectize>
						        </div>
					        </div>
					    </div>


						<div class="form-group row">
					        <div class="col-sm-4">
				        		<label>Perfil de Acesso</label><span class="txt-obg">*</span>
								<selectize name="user_pfa_id" id="user_pfa_id" config="cfgPerfisAcesso" options="aPerfisAcesso" ng-model="eUsuario.user_pfa_id" required ng-disabled="eUsuario.user_tipo >= 3"></selectize>		        
					        </div>
							<div class="col-sm-4">
								<label>Pasta de Trabalho Padrão</label>
								<selectize id="user_pst_id" name="user_pst_id" config="cfgPastaTrabalho" options="aPastaTrabalho" ng-model="eUsuario.user_pst_id"></selectize>
							</div>
					        <div class="col-sm-4" ng-if="eUsuario.user_tipo == 1">
					        	<label>&nbsp;</label>
								<label class="control control--checkbox">Usuário Resp. Ticket?
									<input type="checkbox" value="None" id="user_resp_ticket" name="user_resp_ticket" ng-model="eUsuario.user_resp_ticket" />
									<div class="control__indicator"></div>
								</label>
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