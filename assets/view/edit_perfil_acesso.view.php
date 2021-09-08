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
				<a href="#/accessprofiles" class="kt-subheader__breadcrumbs-link bold">&nbsp;Perfis de Acesso</a>
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
					
					<a href="" ng-if="aUserAccess.pta_nivel >= 1" class="btn btn-xs btn-primary btn-salvar" ng-click="newForm.$invalid || editPerfilAcesso(ePerfilAcesso)" ng-if="aUserAccess.pta_nivel >= 1" ng-disabled="newForm.$invalid">
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
								<a href="#/accessprofiles" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-chevron-left ft-primary"></i> <span class="kt-nav__link-text">Voltar à listagem</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Novo Usuário', '2. Cadastros', '#'+url)" class="kt-nav__link">
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

			<div class="kt-portlet__body" >
		
				<div class="alert alert-warning" ng-if="!aUserAccess.pta_nivel || aUserAccess.pta_nivel < 1">
					Usuário sem acesso a essa Rotina. Contate o Administrador do Sistema.
					<a href="#/users" class="btn-xs btn-link" >Voltar à listagem</a>
				</div>

				<form name="newForm" role="form" ng-show="aUserAccess.pta_nivel >= 1">
					<br>	
					<div class="row row-title-fields">
						<button type="button" class="btn btn-xs btn-link" data-toggle="collapse" data-target="#principal"><i class="fa fa-caret-down"></i> Informações Gerais<span class="nd-text">(obrigatório)</span></button>
					</div>
					<div id="principal" class="collapse in">
						<div class="form-group row">
				        	<div class="col-sm-2">
					        	<label>Registro</label>
					            <input type="text" name="pfa_id" id="pfa_id" ng-maxlength="11" class="form-control my-control" ng-model="ePerfilAcesso.pfa_id" readonly />
					        </div>
					        <div class="col-sm-10">
						        <div ng-class="{ 'has-error': newForm.pfa_descricao.$dirty && (newForm.pfa_descricao.$error.required || newForm.pfa_descricao.$error.maxlength) }" >
						        	<label>Descrição</label><span class="txt-obg">*</span>
					        		<input type="text" name="pfa_descricao" id="pfa_descricao" ng-maxlength="120" maxlength="120" class="form-control my-control" placeholder="Descrição do perfil" ng-model="ePerfilAcesso.pfa_descricao" required />
						        </div>			       			        
					        </div>
					    </div>
					</div>
					<hr>
					<div class="row kt-portlet__head kt-portlet__head--sm" style="padding-top: 15px;">
						<div class="col-xs-12">					
							<div class="row form-group">
								<div class="col-sm-8">				
									<h4 class="ft-gray-dk">Selecione as Rotinas de acesso:</h4>
								</div>
								<div class="col-sm-4">
								    <div class="input-group input-group-info right-justify" ng-show="aUserAccess.pta_nivel">
							        	<input class="form-control my-control" type="text" placeholder="Digite para pesquisar uma rotina..." ng-model="iptsearch" autofocus>
							        	<span class="input-group-addon my-control search-addon"><i class="ace-icon fa fa-search nav-search-icon"></i></span>
								    </div>
								</div> 
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-striped display nowrap">
				  			<thead class="thead">
								<tr>
									<th>
										<label class="control control--checkbox" ng-if="aUserAccess.pta_nivel >= 3">
											<input type="checkbox" value="None" id="checkall2" name="checkall2" ng-click="selectAllRoutines()" />
											<div class="control__indicator"></div>
										</label>
									</th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('rot_nome')">Rotina</a></th>
									<th class="text-nowrap"><a href="" ng-click="ordenarPor('rot_descricao')">Descrição</a></th>
									<th class="text-nowrap"><a href="" >Nível de Acesso</a></th>
								</tr>
							</thead>			
				  			<tbody>		  				
								<tr ng-class="{negrito: rotina.selecionado}" ng-repeat="rotina in aRotinas | filter: iptsearch | orderBy: criterioOrdenacao: direcaoOrdenacao">
									<td>
										<label class="control control--checkbox">
											<input type="checkbox" value="None" id="rot_{{rotina.rot_nome}}" name="rot_{{rotina.rot_nome}}" ng-true-value="'true'" ng-false-value="'false'"  ng-model="rotina.selecionado" />
											<div class="control__indicator"></div>
										</label>
									</td>
									<td>{{rotina.rot_nome}}</td>
									<td class="text-nowrap">
										{{rotina.rot_descricao}}
									</td>
									<td>
										<select class="form-control my-control-sel" id="rtu_nivel_{{rotina.rot_nome}}" name="rtu_nivel_{{rotina.rot_nome}}" ng-model="rotina.nivel">
											<option value="0">0 - Apenas Visualizar.</option>
											<option value="1">1 - Visualizar e inserir novos registros.</option>
											<option value="2">2 - Visualizar, inserir e editar os registros.</option>
											<option value="3">3 - Permissão total, inserção, edição e exclusão.</option>
										</select>
									</td>
								</tr>						
				  			</tbody> 
						</table>
					</div>
				</form>


			</div>
		</div>
	</div>
</div>
<br>