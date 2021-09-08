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
				<a href="#/parameters" class="kt-subheader__breadcrumbs-link bold">&nbsp;Parâmetros</a>
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
					
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-primary btn-salvar" ng-click="edtForm.$invalid || editParametro(eParam)" ng-if="aUserAccess.pta_nivel >= 2" ng-disabled="edtForm.$invalid">
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
								<a href="#/parameters" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-chevron-left ft-primary"></i> <span class="kt-nav__link-text">Voltar à listagem</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Editar Parâmetro ' + eParam.par_key, '9. Configurações', '#'+url)" class="kt-nav__link">
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
					<a href="#/parameters" class="btn-xs btn-link" >Voltar à listagem</a>
				</div>

				<form name="edtForm" role="form" ng-show="aUserAccess.pta_nivel >= 2">
					<br>	

					<div class="form-group row">
			        	<div class="col-sm-2">
				        	<label>Chave</label>
				            <input type="text" name="par_key" id="par_key" ng-maxlength="15" class="form-control my-control" ng-model="eParam.par_key" required disabled readonly />
				        </div>
				    </div>

					<div class="form-group row">
				        <div class="col-lg-12">		        	
					        <div ng-class="{ 'has-error': edtForm.par_conteudo.$dirty && (edtForm.par_conteudo.$error.required || edtForm.par_conteudo.$error.maxlength) }" >
					        	<label>Conteúdo</label><span class="txt-obg">*</span>
					        	<textarea class="form-control my-control-txta" ng-model="eParam.par_conteudo" required></textarea>
					        </div>				       			        
				        </div>
				    </div>

					<div class="form-group row">
				        <div class="col-sm-12">
					        <div ng-class="{ 'has-error': edtForm.par_descricao.$dirty && edtForm.par_descricao.$error.required }" >
				        		<label>Descrição</label><span class="txt-obg">*</span>		        		
					        	<textarea class="form-control my-control-txta" ng-model="eParam.par_descricao" required></textarea>
					        </div>
				        </div>
				    </div>

				</form>

			</div>
		</div>
	</div>
</div>
<br>