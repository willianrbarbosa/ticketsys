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
				<a href="#/indicators" class="kt-subheader__breadcrumbs-link bold">&nbsp;Indicadores</a>
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
					
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-primary btn-salvar" ng-click="newForm.$invalid || newIndicador(newInd)" ng-if="aUserAccess.pta_nivel >= 2" ng-disabled="newForm.$invalid">
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
								<a href="#/indicators" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-chevron-left ft-primary"></i> <span class="kt-nav__link-text">Voltar à listagem</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Novo Parâmetro', '9. Configurações', '#'+url)" class="kt-nav__link">
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
					<a href="#/indicators" class="btn-xs btn-link" >Voltar à listagem</a>
				</div>

				<form name="newForm" role="form" ng-show="aUserAccess.pta_nivel >= 2">
					<br>	

					<div class="form-group row">
			        	<div class="col-sm-4">
				        	<label>Chave</label><span class="txt-obg">*</span>
				            <input type="text" name="ind_chave" id="ind_chave" ng-maxlength="15" class="form-control my-control" ng-model="newInd.ind_chave" style="text-transform:uppercase" required/>
				        </div>
				        <div class="col-sm-4">		        	
					        <div ng-class="{ 'has-error': edtForm.ind_param.$dirty && (edtForm.ind_param.$error.required || edtForm.ind_param.$error.maxlength) }" >
					        	<label>Tipo</label><span class="txt-obg">*</span>
					        	<selectize name="newInd.ind_param" id="newInd.ind_param" config="cfgTipoInd" options="aTipoInd" ng-model="newInd.ind_param" required></selectize>
					        </div>				       			        
				        </div>
				        <div class="col-sm-4">
					        <div ng-class="{ 'has-error': edtForm.ind_valor.$dirty && edtForm.ind_valor.$error.required }" >
				        		<label>Valor</label><span class="txt-obg">*</span>		        		
					        	<input type="text" class="form-control my-control" ng-model="newInd.ind_valor" required>
					        </div>
				        </div>
				    </div>

					<div class="form-group row">
				        <div class="col-sm-12">
					        <div ng-class="{ 'has-error': edtForm.ind_descricao.$dirty && edtForm.ind_descricao.$error.required }" >
				        		<label>Descrição</label><span class="txt-obg">*</span>		        		
					        	<textarea class="form-control my-control-txta" ng-model="newInd.ind_descricao" required></textarea>
					        </div>
				        </div>
				    </div>
				</form>

			</div>
		</div>
	</div>
</div>
<br>