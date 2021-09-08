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
				<a href="#/tipoatividade" class="kt-subheader__breadcrumbs-link bold">&nbsp;Tipos de Atividade</a>
			</div>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<span class="kt-subheader__breadcrumbs">&nbsp;Novo</span>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">
					
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-primary btn-salvar" ng-click="newForm.$invalid || novoTipoAtividade(nTipoAtividade, true)" ng-disabled="newForm.$invalid">
						Salvar
					</a>
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-success btn-salvar" ng-click="newForm.$invalid || novoTipoAtividade(nTipoAtividade, false)" ng-disabled="newForm.$invalid">
						Salvar e Cad. Novo
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
								<a href="#/tipoatividade" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-chevron-left ft-primary"></i> <span class="kt-nav__link-text">Voltar à listagem</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Novo Tipo de Atividade', 'Cadastros', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="getAjuda('tipoatividade')" class="kt-nav__link">
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

				<div class="alert alert-warning" ng-if="!aUserAccess.pta_nivel || aUserAccess.pta_nivel < 1">
					Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.
					<a href="#/tipoatividade" class="btn-xs btn-link" >Voltar à listagem</a>
				</div>

				<form name="newForm" role="form" ng-show="aUserAccess.pta_nivel >= 1">
					<div class="form-group row">
				        <div class="col-sm-2">
				        	  <label>Registro</label>
				            <input type="text" name="nov_tav_id" id="nov_tav_id" ng-maxlength="11" class="form-control my-control" ng-model="nTipoAtividade.tav_id" readonly />
				        </div>
						<div class="col-sm-10" ng-class="{ 'has-error': (newForm.nov_tav_descricao.$dirty && newForm.nov_tav_descricao.$error.required) || newForm.nov_tav_descricao.$error.maxlength }">
							<label>Descrição</label><span class="txt-obg">*</span>
							<input type="text" name="nov_tav_descricao" id="nov_tav_descricao"  ng-maxlength="50" maxlength="50" class="form-control my-control" ng-model="nTipoAtividade.tav_descricao"  required/>
						</div>
                    </div>
               </form>

            </div>
        </div>
	</div>
</div>