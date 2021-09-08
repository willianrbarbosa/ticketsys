<?php
session_start();
include('../model/class/security.class.php');
$security = new Security();	
?>
<style type="text/css">
	md-tooltip .md-content {
		background-color: #FFF !important;
		opacity: 1 !important;
		border: 1px solid #323232 !important;
	}
</style>

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title bold">Dashboard 2.0</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<i class="ace-icon fa fa-angle-double-right"></i>
				<a href="#/dashboard" class="kt-subheader__breadcrumbs-link bold">&nbsp;Produtos Pendentes</a>
			</div>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<div class="dropdown dropdown-inline">

					<a href="" class="btn btn-md  btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ações Relacionadas">
						<i class="fa fa-ellipsis-h"></i>
					</a>

					<div class="dropdown-menu dropdown-menu-fit dropdown-menu-md dropdown-menu-right">
						<!--begin::Nav-->
						<ul class="kt-nav">
							<li class="kt-nav__head"><strong>Ações relacionadas:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="#/olddashboard{{(nCliTK ? '/'+nCliTK : '')}}" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-angle-double-left"></i> <span class="kt-nav__link-text">Usar versão antiga</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="<?php echo $security->base_patch; ?>/assets/view/visualizador_link.php" target="_Blank" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-link ft-primary"></i> <span class="kt-nav__link-text">Abrir Visualizador de Link</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="NewUserFavorite('Dashboard 2.0', '1. Dashboard', '#'+url)" class="kt-nav__link">
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

			<div class="row kt-portlet__head kt-portlet__head--sm" style="padding-top: 15px;">
				<div class="col-xs-12">

					<div class="row form-group" ng-show="aUserAccess.rtu_nivel">
						<div class="col-sm-6">
							<label>Cliente</label><span class="txt-obg">*</span>
							<selectize name="nCliTK" id="nCliTK" config="cfgCli" options="aClientes" ng-model="nCliTK" required></selectize>
						</div>
						<div class="col-sm-3" ng-init="filterNCM = ''">
							<label>Filtro NCM</label>
							<input type="text" name="filterNCM" id="filterNCM" class="form-control my-control" ng-model="filterNCM">
						</div>
						<div class="col-sm-3" ng-init="orderByField = optOrderBy[0]">
							<label>Ordenar por</label><span class="txt-obg">*</span>
							<select  class="form-control my-control-sel" ng-model="orderByField" ng-options="option.name for option in optOrderBy" required></select>
						</div>
					</div>

					<div class="row form-group" ng-show="aUserAccess.rtu_nivel">
						<div class="col-sm-2" ng-init="tipo_prod_imp = 'T'">
							<label class="control control--radio">Todos?
								<input type="radio" value="T" id="tipo_prod_imp0" name="tipo_prod_imp" ng-model="tipo_prod_imp" />
								<div class="control__indicator"></div>
							</label>
						</div>
						<div class="col-sm-2">
							<label class="control control--radio">Apenas Auditor?
								<input type="radio" value="A" id="tipo_prod_imp1" name="tipo_prod_imp" ng-model="tipo_prod_imp" />
								<div class="control__indicator"></div>
							</label>
						</div>
						<div class="col-sm-3">
							<label class="control control--radio">Apenas importação?
								<input type="radio" value="I" id="tipo_prod_imp2" name="tipo_prod_imp" ng-model="tipo_prod_imp" />
								<div class="control__indicator"></div>
							</label>
						</div>
						<div class="col-sm-3">
							<label class="control control--checkbox" ng-init="filtro_editado = false">Apenas não editados?
								<input type="checkbox" value="true" id="filtro_editado" name="filtro_editado" ng-model="filtro_editado" />
								<div class="control__indicator"></div>
							</label>
						</div>
						<div class="col-sm-2">
							<button type="button" class="btn btn-sm btn-block btn-primary" ng-click="!nCliTK || !orderByField || getClienteProdutoDashboard(nCliTK, orderByField, filterNCM, 'R', tipo_prod_imp, filtro_editado)" ng-disabled="!nCliTK || !orderByField" title="Iniciar Dashboard">&nbsp;<i class="fa fa-play"></i>&nbsp; </button>
						</div>
					</div>
				</div>
			</div>

			<div class="kt-portlet__body" >	
	
				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>

				<div class="alert alert-warning" id="div-loading">
					<span id="loading"><div class="loading-img"></div></span>
				</div>

				<div class="form-group row" ng-show="aProdCli.length > 0">					
					<div class="col-sm-1 col-sm-offset-3">
						<button type="button" class="btn btn-block btn-sm btn-default" title="Voltar para o primeiro produto" ng-click="setProdutoAtual(0)"> &nbsp;<i class="fa fa-angle-double-left bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1">
						<button type="button" class="btn btn-block btn-sm btn-default" title="Voltar para o produto anterior" ng-click="setProdutoAtual((nWorkProd > 0 ? nWorkProd - 1 : 0))"> &nbsp;<i class="fa fa-angle-left bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1" ng-if="aUserAccess.rtu_nivel >= 2">
						<button type="button" class="btn btn-block btn-sm btn-primary" title="Salvar produto" ng-click="edtForm.$invalid || editCustomerProduct2(aProdCli[nWorkProd], 'edit')" ng-disabled="edtForm.$invalid"> &nbsp;<i class="fa fa-save bold "></i>&nbsp; </button>
					</div>
					<div class="col-sm-1" ng-if="aUserAccess.rtu_nivel >= 3">
						<button type="button" class="btn btn-block btn-sm btn-danger" title="Descartar produto e ir para o próximo" ng-click="editCustomerProduct2(aProdCli[nWorkProd], 'delete')"> &nbsp;<i class="fa fa-trash bold "></i>&nbsp;&nbsp;<i class="fa fa-angle-right bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1">
						<button type="button" class="btn btn-block btn-sm btn-default" title="Pular esse produto" ng-click="setProdutoAtual((nWorkProd < (aProdCli.length-1) ? nWorkProd + 1 : (aProdCli.length-1)))"> &nbsp;<i class="fa fa-angle-right bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1">
						<button type="button" class="btn btn-block btn-sm btn-default" title="Ir para o último produto" ng-click="setProdutoAtual(aProdCli.length-1)"> &nbsp;<i class="fa fa-angle-double-right bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-2 col-sm-offset-1 right-justify" ng-init="nWorkProdAux = 1">
						Produto <input type="text" class="iptano" name="prod_atual" id="prod_atual" ng-model="nWorkProdAux" ng-change="nWorkProd = nWorkProdAux - 1" style="width: 30px; border: none; text-align: right;"> de {{aProdCli.length}}
					</div>
				</div>
				<hr/>

				<form name="edtForm" role="form" ng-show="aProdCli.length > 0" ng-show="aUserAccess.rtu_nivel">
					<div class="row row-title-fields">
						<button type="button" class="btn btn-lg btn-link" data-toggle="collapse" data-target="#principal"><i class="fa fa-caret-down"></i> PRODUTO DO CLIENTE - <span class="ft-danger bold">{{aProdCli[nWorkProd].ORIGEM_PROD}}</span></button>
					</div>
					<div id="principal" class="collapse in">
						<div class="form-group row">
							<div class="col-sm-1">
								<label>Reg.</label>
								<input type="text" name="cpd_id" id="cpd_id" ng-maxlength="11" class="form-control my-control" ng-model="aProdCli[nWorkProd].cpd_id" readonly />
							</div>
							<div class="col-sm-2">
								<div ng-class="{ 'has-error': edtForm.cpd_ncm.$dirty && (edtForm.cpd_ncm.$error.required || edtForm.cpd_ncm.$error.maxlength) }" >
									<label>NCM</label>
									<input type="text" name="cpd_ncm" id="cpd_ncm" maxlength="10" ng-maxlength="10" class="form-control my-control" placeholder="Cód. NCM" ng-model="aProdCli[nWorkProd].cpd_ncm" readonly />
								</div>
							</div>
							<div class="col-sm-1">
								<div ng-class="{ 'has-error': edtForm.CMI_CEST.$dirty && (edtForm.CMI_CEST.$error.required || edtForm.CMI_CEST.$error.maxlength) }" >
									<label>CEST</label>
									<input type="text" name="CMI_CEST" id="CMI_CEST" maxlength="10" ng-maxlength="10" class="form-control my-control" placeholder="Cód. NCM" ng-model="aProdCli[nWorkProd].CMI_CEST" readonly />
								</div>
							</div>
							<div class="col-sm-2">
								<div ng-class="{ 'has-error': edtForm.cpd_ean.$dirty && (edtForm.cpd_ean.$error.required || edtForm.cpd_ean.$error.maxlength) }" >
									<label>Cod. Barras (EAN)</label>
								    <div class="input-group input-group-info right-justify">
										<input type="text" name="cpd_ean" id="cpd_ean" maxlength="30" ng-maxlength="30" class="form-control my-control" placeholder="Cód. EAN" ng-model="aProdCli[nWorkProd].cpd_ean" readonly />
							        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="setGoogleURL(aProdCli[nWorkProd].cpd_ean)" title="Consultar EAN no Google"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
								    </div>
								</div>
							</div>
							<div class="col-sm-4">
								<div ng-class="{ 'has-error': edtForm.cpd_descricao.$dirty && (edtForm.cpd_descricao.$error.required || edtForm.cpd_descricao.$error.maxlength) }" >
									<label>Descrição</label>
								    <div class="input-group input-group-info right-justify">
										<input type="text" name="cpd_descricao" id="cpd_descricao" ng-maxlength="120" maxlength="120" class="form-control my-control" placeholder="Descrição do Produto" ng-model="aProdCli[nWorkProd].cpd_descricao" readonly disabled />
							        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="getCliProdMovCpdID(nCliTK, aProdCli[nWorkProd].cpd_id)" title="Consultar Movimentos desse produto"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
								    </div>
								</div>			       			        
							</div>
							<div class="col-sm-2">
								<label>U.M.</label>
								<input type="text" name="cpd_prod_um" id="cpd_prod_um" maxlength="6" ng-maxlength="6" class="form-control my-control" placeholder="U.M. da Unidade" ng-model="aProdCli[nWorkProd].cpd_prod_um" readonly disabled />
							</div>
						</div>
						<div class="form-group row" ng-if="aProdCli[nWorkProd].cpd_ean_un">
							<div class="col-sm-2 col-sm-offset-4">
								<label>
									Cod. Barras (EAN) Unidade 
									<i class="fa fa-lg fa-check ft-success" ng-if="aProdCli[nWorkProd].proun_id">
										<md-tooltip md-direction="top">
									    	<h5>
									    		EAN UNIDADE EXISTENTE NA BASE<br/>
									    		{{aProdCli[nWorkProd].proun_descricao}}<br/>
									    		{{aProdCli[nWorkProd].ncmun_codigo}} - {{aProdCli[nWorkProd].ncmun_cest}}
									    	</h5>
										</md-tooltip>
									</i>
								</label>
							    <div class="input-group input-group-info right-justify">
									<input type="text" name="cpd_ean_un" id="cpd_ean_un" maxlength="30" ng-maxlength="30" class="form-control my-control" placeholder="Cód. EAN Unidade" ng-model="aProdCli[nWorkProd].cpd_ean_un" readonly disabled />
						        	<span class="input-group-addon my-control search-addon"><a href="" ng-click="!aProdCli[nWorkProd].cpd_ean_un || setGoogleURL(aProdCli[nWorkProd].cpd_ean_un)" title="Consultar EAN no Google"><i class="ace-icon fa fa-search nav-search-icon"></i></a></span>
							    </div>
							</div>
							<div class="col-sm-4">
								<label>Descrição Unidade </label>
								<input type="text" name="cpd_descr_un" id="cpd_descr_un" maxlength="120" ng-maxlength="120" class="form-control my-control" placeholder="Descrição da Unidade" ng-model="aProdCli[nWorkProd].cpd_descr_un" readonly disabled/>
							</div>
							<div class="col-sm-2">
								<label>U.M. Unidade</label>
								<input type="text" name="cpd_prod_un_um" id="cpd_prod_un_um" maxlength="6" ng-maxlength="6" class="form-control my-control" placeholder="U.M. da Unidade" ng-model="aProdCli[nWorkProd].cpd_prod_un_um" readonly disabled />
							</div>
						</div>

						<div class="form-group row" ng-if="aPreCads.pcd_id">
							<div class="col-sm-12" ng-if="aPreCads.pcd_chave">
								<label>Chave de Acesso da NF-e</label><span class="nd-text">(vindo do pré-cadastro do cliente)</span><label ng-if="aPreCads.pcd_arq_xml"><a href="{{aPreCads.pcd_arq_xml}}" download target="_Blank">Fazer Download do XML da NF-e</a></label>
								<input type="text" name="pcd_chave" id="pcd_chave" class="form-control my-control" placeholder="Chave de acesso NFe" ng-model="aPreCads.pcd_chave" readonly />
							</div>
						</div>
					</div>
					<hr/>

					<div class="row row-title-fields">
						<button type="button" class="btn btn-lg btn-link" data-toggle="collapse" data-target="#produto_homolgoacao"><i class="fa fa-caret-down"></i> VALIDAÇÃO DO PRODUTO</button>
					</div>
					<div id="produto_homolgoacao" class="collapse in">
						<div class="row">
							<div class="col-sm-3">
								<label>&nbsp;</label>
								<button type="button" class="btn btn-xs btn-block " ng-class="{'bg-gray': aProdCli[nWorkProd].cpd_ncm_vld == 'N', 'btn-success': aProdCli[nWorkProd].cpd_ncm_vld == 'S'}" ng-click="aProdCli[nWorkProd].cpd_ncm_vld = (aProdCli[nWorkProd].cpd_ncm_vld == 'S' ? 'N' : 'S')">
									NCM validado
									<i class="fa fa-check" ng-if="aProdCli[nWorkProd].cpd_ncm_vld == 'S'"></i>
									<i class="fa fa-question" ng-if="aProdCli[nWorkProd].cpd_ncm_vld == 'N'"></i>
								</button>
							</div>
							<div class="col-sm-3">
								<label>&nbsp;</label>
								<button type="button" class="btn btn-xs btn-block " ng-class="{'bg-gray': aProdCli[nWorkProd].cpd_ean_vld == 'N', 'btn-success': aProdCli[nWorkProd].cpd_ean_vld == 'S'}" ng-click="aProdCli[nWorkProd].cpd_ean_vld = (aProdCli[nWorkProd].cpd_ean_vld == 'S' ? 'N' : 'S')">
									EAN validado
									<i class="fa fa-check" ng-if="aProdCli[nWorkProd].cpd_ean_vld == 'S'"></i>
									<i class="fa fa-question" ng-if="aProdCli[nWorkProd].cpd_ean_vld == 'N'"></i>
								</button>
							</div>
							<div class="col-sm-3">
								<label>&nbsp;</label>
								<button type="button" class="btn btn-xs btn-block " ng-class="{'bg-gray': aProdCli[nWorkProd].cpd_descr_vld == 'N', 'btn-success': aProdCli[nWorkProd].cpd_descr_vld == 'S'}" ng-click="aProdCli[nWorkProd].cpd_descr_vld = (aProdCli[nWorkProd].cpd_descr_vld == 'S' ? 'N' : 'S')">
									Descrição validada
									<i class="fa fa-check" ng-if="aProdCli[nWorkProd].cpd_descr_vld == 'S'"></i>
									<i class="fa fa-question" ng-if="aProdCli[nWorkProd].cpd_descr_vld == 'N'"></i>
								</button>   
							</div>
							<div class="col-sm-3">
								<label>&nbsp;</label>
								<button type="button" class="btn btn-xs btn-block " ng-class="{'bg-gray': aProdCli[nWorkProd].cpd_contraditorio == 'N', 'btn-success': aProdCli[nWorkProd].cpd_contraditorio == 'S'}" ng-click="aProdCli[nWorkProd].cpd_contraditorio = (aProdCli[nWorkProd].cpd_contraditorio == 'S' ? 'N' : 'S')">
									Produto contraditório
									<i class="fa fa-check" ng-if="aProdCli[nWorkProd].cpd_contraditorio == 'S'"></i>
									<i class="fa fa-question" ng-if="aProdCli[nWorkProd].cpd_contraditorio == 'N'"></i>
								</button>
							</div>
						</div>
						<hr/>

						<div class="form-group row">
							<div class="col-sm-12">
								<div ng-class="{ 'has-error': edtForm.cpd_ncm_id.$dirty && edtForm.cpd_ncm_id.$error.required }" >
									<label>Selecione o NCM Sistema</label><span class="txt-obg">*</span>
									<div ng-show="aNCMFiltered.length > 1">
										<div class="dropdown dropdown-inline">
											<a href="" class="btn btn-sm btn-link ft-danger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Exceções do NCM">	
												<span>*** ATENÇÃO: Esse NCM tem exceções (clique para ver)</span> <i class="fa fa-caret-down"></i>
											</a>

											<div class="dropdown-menu dropdown-menu-fit dropdown-menu-xl dropdown-menu-right">
												<!--begin::Nav-->
												<ul class="kt-nav">
													<li class="kt-nav__head"><strong>Exceções do NCM {{aProdCli[nWorkProd].cpd_ncm}}: </strong></li>
													<li class="kt-nav__separator"></li>
													<li class="kt-nav__item" ng-repeat="ncmExc in aNCMFiltered">
														<a href="" ng-click="aProdCli[nWorkProd].cpd_ncm_id = ncmExc.ncm_id" class="kt-nav__link" title="Selecionar esse NCM">
															{{ncmExc.ncm_codigo}} | {{ncmExc.ncm_descricao}} | Exce.: {{ncmExc.ncm_excecao_trib}}
														</a>
													</li>		
												</ul>
												<!--end::Nav-->
											</div>
										</div>
									</div>									
									<selectize name="cpd_ncm_id" id="cpd_ncm_id" config="cfgNCM" options="aNCM" ng-model="aProdCli[nWorkProd].cpd_ncm_id" required></selectize>
								</div>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-12" ng-if="!aProdCli[nWorkProd].cpd_descr_editada">
								<button type="button" class="btn btn-xs btn-link" ng-click="CopiaDescricao()" title="Copiar Descrição do cliente"><i class="fa fa-copy"></i></button>
								 - <label>Descrição para {{(aProdCli[nWorkProd].cpd_ean_un ? aProdCli[nWorkProd].cpd_prod_um : 'Sistema')}} <span class="nd-text">(Descrição do Produto Corrigida para salvar no banco Sistema)</span></label><span class="txt-obg">*</span>
							</div>
							<div class="col-sm-3" ng-if="!aProdCli[nWorkProd].cpd_descr_editada">
								<div ng-class="{ 'has-error': edtForm.cpd_nomeprod.$dirty && (edtForm.cpd_nomeprod.$error.required || edtForm.cpd_nomeprod.$error.maxlength) }" >
									<input type="text" name="cpd_nomeprod" id="cpd_nomeprod" ng-maxlength="40" maxlength="40" class="form-control my-control" placeholder="Nome do Produto" ng-model="aProdCli[nWorkProd].cpd_nomeprod" required />
								</div>
							</div>
							<div class="col-sm-2" ng-if="!aProdCli[nWorkProd].cpd_descr_editada">
								<div ng-class="{ 'has-error': edtForm.cpd_marcaprod.$dirty && (edtForm.cpd_marcaprod.$error.required || edtForm.cpd_marcaprod.$error.maxlength) }" >
									<input type="text" name="cpd_marcaprod" id="cpd_marcaprod" ng-maxlength="30" maxlength="30" class="form-control my-control" placeholder="Marca" ng-model="aProdCli[nWorkProd].cpd_marcaprod"  />
								</div>			       			        
							</div>
							<div class="col-sm-2" ng-if="!aProdCli[nWorkProd].cpd_descr_editada">
								<div ng-class="{ 'has-error': edtForm.cpd_linhaprod.$dirty && (edtForm.cpd_linhaprod.$error.required || edtForm.cpd_linhaprod.$error.maxlength) }" >
									<input type="text" name="cpd_linhaprod" id="cpd_linhaprod" ng-maxlength="30" maxlength="30" class="form-control my-control" placeholder="Linha" ng-model="aProdCli[nWorkProd].cpd_linhaprod"  />
								</div>
							</div>
							<div class="col-sm-3" ng-if="!aProdCli[nWorkProd].cpd_descr_editada">
								<div ng-class="{ 'has-error': edtForm.cpd_tipoprod.$dirty && (edtForm.cpd_tipoprod.$error.required || edtForm.cpd_tipoprod.$error.maxlength) }" >
									<input type="text" name="cpd_tipoprod" id="cpd_tipoprod" ng-maxlength="30" maxlength="30" class="form-control my-control" placeholder="Sabor/Fragrância/Tipo/Cor/Outros" ng-model="aProdCli[nWorkProd].cpd_tipoprod"  />
								</div>
							</div>
							<div class="col-sm-1" ng-if="!aProdCli[nWorkProd].cpd_descr_editada">
								<div ng-class="{ 'has-error': edtForm.cpd_gramatura.$dirty && (edtForm.cpd_gramatura.$error.required || edtForm.cpd_gramatura.$error.maxlength) }" >
									<input type="text" name="cpd_gramatura" id="cpd_gramatura" ng-maxlength="20" maxlength="20" class="form-control my-control" placeholder="Gram." ng-model="aProdCli[nWorkProd].cpd_gramatura" />
								</div>
							</div>

							<div class="col-sm-12" ng-if="aProdCli[nWorkProd].cpd_descr_editada">
								<button type="button" class="btn btn-xs btn-link" ng-click="aProdCli[nWorkProd].cpd_descr_editada = ''; aProdCli[nWorkProd].cpd_descr_editada_un = ''" title="Apagar Descrição editada"><i class="fa fa-trash"></i></button>
								 - <label>Descrição para {{(aProdCli[nWorkProd].cpd_ean_un ? aProdCli[nWorkProd].cpd_prod_um : 'Sistema')}} <span class="nd-text">(Descrição do Produto Corrigida para salvar no banco Sistema)</span></label><span class="txt-obg">*</span>
							</div>

							<div class="col-sm-11" ng-if="aProdCli[nWorkProd].cpd_descr_editada">
								<input type="text" name="cpd_descr_editada" id="cpd_descr_editada" ng-maxlength="120" maxlength="120" class="form-control my-control" placeholder="Nome do Produto" ng-model="aProdCli[nWorkProd].cpd_descr_editada" required disabled />
							</div>
							<div class="col-sm-1">
								<div ng-class="{ 'has-error': edtForm.cpd_um_editada.$dirty && (edtForm.cpd_um_editada.$error.required || edtForm.cpd_um_editada.$error.maxlength) }" >
									<input type="text" name="cpd_um_editada" id="cpd_um_editada" ng-maxlength="5" maxlength="5" class="form-control my-control" placeholder="U.M" ng-model="aProdCli[nWorkProd].cpd_um_editada" required />
								</div>
							</div>
						</div>

						<div class="form-group row" ng-if="aProdCli[nWorkProd].cpd_ean_un">
							<div class="col-sm-12" ng-if="!aProdCli[nWorkProd].cpd_descr_editada_un">
								<button type="button" class="btn btn-xs btn-link" ng-click="CopiaDescricaoUnidade(true)" title="Copiar Descrição do Editada" ng-if="aProdCli[nWorkProd].cpd_descr_editada"><i class="fa fa-copy"></i></button>
								<button type="button" class="btn btn-xs btn-link" ng-click="CopiaDescricaoUnidade(false)" title="Copiar Descrição do Editada" ng-if="!aProdCli[nWorkProd].cpd_descr_editada"><i class="fa fa-copy"></i></button>
								 - <label>Descrição para Unidade <span class="nd-text">(Descrição do Produto da UNIDADE Corrigida para salvar no banco Sistema)</span></label><span class="txt-obg">*</span>
							</div>
							<div class="col-sm-3" ng-if="!aProdCli[nWorkProd].cpd_descr_editada_un">
								<div ng-class="{ 'has-error': edtForm.cpd_nomeprod_un.$dirty && (edtForm.cpd_nomeprod_un.$error.required || edtForm.cpd_nomeprod_un.$error.maxlength) }" >
									<input type="text" name="cpd_nomeprod_un" id="cpd_nomeprod_un" ng-maxlength="40" maxlength="40" class="form-control my-control" placeholder="Nome do Produto para Unidade" ng-model="aProdCli[nWorkProd].cpd_nomeprod_un" required />
								</div>
							</div>
							<div class="col-sm-2" ng-if="!aProdCli[nWorkProd].cpd_descr_editada_un">
								<div ng-class="{ 'has-error': edtForm.cpd_marcaprod_un.$dirty && (edtForm.cpd_marcaprod_un.$error.required || edtForm.cpd_marcaprod_un.$error.maxlength) }" >
									<input type="text" name="cpd_marcaprod_un" id="cpd_marcaprod_un" ng-maxlength="30" maxlength="30" class="form-control my-control" placeholder="Marca para Unidade" ng-model="aProdCli[nWorkProd].cpd_marcaprod_un"  />
								</div>
							</div>
							<div class="col-sm-2" ng-if="!aProdCli[nWorkProd].cpd_descr_editada_un">
								<div ng-class="{ 'has-error': edtForm.cpd_linhaprod_un.$dirty && (edtForm.cpd_linhaprod_un.$error.required || edtForm.cpd_linhaprod_un.$error.maxlength) }" >
									<input type="text" name="cpd_linhaprod_un" id="cpd_linhaprod_un" ng-maxlength="30" maxlength="30" class="form-control my-control" placeholder="Linha para Unidade" ng-model="aProdCli[nWorkProd].cpd_linhaprod_un"  />
								</div>
							</div>
							<div class="col-sm-3" ng-if="!aProdCli[nWorkProd].cpd_descr_editada_un">
								<div ng-class="{ 'has-error': edtForm.cpd_tipoprod_un.$dirty && (edtForm.cpd_tipoprod_un.$error.required || edtForm.cpd_tipoprod_un.$error.maxlength) }" >
									<input type="text" name="cpd_tipoprod_un" id="cpd_tipoprod_un" ng-maxlength="30" maxlength="30" class="form-control my-control" placeholder="Sabor/Fragrância/Tipo/Cor/Outros para Unidade" ng-model="aProdCli[nWorkProd].cpd_tipoprod_un"  />
								</div>
							</div>
							<div class="col-sm-1" ng-if="!aProdCli[nWorkProd].cpd_descr_editada_un">
								<div ng-class="{ 'has-error': edtForm.cpd_gramatura_un.$dirty && (edtForm.cpd_gramatura_un.$error.required || edtForm.cpd_gramatura_un.$error.maxlength) }" >
									<input type="text" name="cpd_gramatura_un" id="cpd_gramatura_un" ng-maxlength="20" maxlength="20" class="form-control my-control" placeholder="Gram. para Unidade" ng-model="aProdCli[nWorkProd].cpd_gramatura_un" />
								</div>
							</div>

							<div class="col-sm-12" ng-if="aProdCli[nWorkProd].cpd_descr_editada_un">
								<button type="button" class="btn btn-xs btn-link" ng-click="CopiaDescricaoUnidade(true)" title="Copiar Descrição do Editada" ng-if="aProdCli[nWorkProd].cpd_descr_editada"><i class="fa fa-copy"></i></button>
								<button type="button" class="btn btn-xs btn-link" ng-click="CopiaDescricaoUnidade(false)" title="Copiar Descrição do Editada" ng-if="!aProdCli[nWorkProd].cpd_descr_editada"><i class="fa fa-copy"></i></button>
								- <label>Descrição para Unidade</label>
							</div>

							<div class="col-sm-11" ng-if="aProdCli[nWorkProd].cpd_descr_editada_un">
								<input type="text" name="cpd_descr_editada_un" id="cpd_descr_editada_un" maxlength="120" ng-maxlength="120" class="form-control my-control" placeholder="Descrição da Unidade" ng-model="aProdCli[nWorkProd].cpd_descr_editada_un" required disabled/>
							</div>
							<div class="col-sm-1">
								<div ng-class="{ 'has-error': edtForm.cpd_um_editada_un.$dirty && (edtForm.cpd_um_editada_un.$error.required || edtForm.cpd_um_editada_un.$error.maxlength) }" >
									<input type="text" name="cpd_um_editada_un" id="cpd_um_editada_un" maxlength="5" ng-maxlength="5" class="form-control my-control" placeholder="U.M. da Unidade" ng-model="aProdCli[nWorkProd].cpd_um_editada_un" required/>
								</div>
							</div>
						</div>

						<div class="form-group row" ng-if="aUserAccess.rtu_nivel >= 2">
							<div class="col-sm-12">
								<label>Produto Interno</label><span class="nd-text">(ASSOCIAÇÃO)</span>
								<selectize name="cpd_homo_pro_id" id="cpd_homo_pro_id" config="cfgProd" options="aProdutos" ng-model="aProdCli[nWorkProd].cpd_homo_pro_id" ng-change="getProduto2(aProdCli[nWorkProd].cpd_homo_pro_id)"></selectize>
							</div>
						</div>
					</div>

					<div class="row row-title-fields" ng-if="aUserAccess.rtu_nivel >= 3">
						<button type="button" class="btn btn-lg btn-link" data-toggle="collapse" data-target="#produ_interno"><i class="fa fa-caret-down"></i> PRODUTO INTERNO (ASSOCIAR PARA BASE DO SISTEMA)</button>
					</div>
					<div id="produ_interno" class="collapse in">
						<div class="form-group row">
							<div class="col-sm-12">
								<label class="ft-primary">Produto Interno <span class="nd-text">(ASSOCIAR)</span></label>
								<selectize name="cpd_pro_id" id="cpd_pro_id" config="cfgProd" options="aProdutos" ng-model="aProdCli[nWorkProd].cpd_pro_id"></selectize>
							</div>
						</div>
						<br/>
					</div>
				</form>
				<hr/>

				<!-- <div class="row form-group" ng-show="aProdCli.length > 0">
					<div class="col-sm-1 col-sm-offset-3">
						<button type="button" class="btn btn-block btn-default" title="Voltar para o primeiro produto" ng-click="setProdutoAtual(0)"> &nbsp;<i class="fa fa-angle-double-left bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1">
						<button type="button" class="btn btn-block btn-default" title="Voltar para o produto anterior" ng-click="setProdutoAtual((nWorkProd > 0 ? nWorkProd - 1 : 0))"> &nbsp;<i class="fa fa-angle-left bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1" ng-if="aUserAccess.rtu_nivel >= 2">
						<button type="button" class="btn btn-block btn-primary" title="Salvar produto e ir para o próximo" ng-click="edtForm.$invalid || editCustomerProduct2(aProdCli[nWorkProd], 'edit')" ng-disabled="edtForm.$invalid"> &nbsp;<i class="fa fa-save bold "></i>&nbsp;&nbsp;<i class="fa fa-angle-right bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1" ng-if="aUserAccess.rtu_nivel >= 3">
						<button type="button" class="btn btn-block btn-danger" title="Descartar produto e ir para o próximo" ng-click="editCustomerProduct2(aProdCli[nWorkProd], 'delete')"> &nbsp;<i class="fa fa-trash bold "></i>&nbsp;&nbsp;<i class="fa fa-angle-right bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1">
						<button type="button" class="btn btn-block btn-default" title="Pular esse produto" ng-click="setProdutoAtual((nWorkProd < (aProdCli.length-1) ? nWorkProd + 1 : (aProdCli.length-1)))"> &nbsp;<i class="fa fa-angle-right bold"></i>&nbsp; </button>
					</div>
					<div class="col-sm-1">
						<button type="button" class="btn btn-block btn-default" title="Ir para o último produto" ng-click="setProdutoAtual(aProdCli.length-1)"> &nbsp;<i class="fa fa-angle-double-right bold"></i>&nbsp; </button>
					</div>
				</div> -->

			</div>
		</div>

	</div>
</div>

<div id="mdGoogleEANSearch" tabindex='-1' class="modal fade" style="overflow: auto;">				
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">

			<div class="modal-header">				
				<button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><i class="fa fa-md fa-search-plus"></i> Pesquisa de EAN</h4>
			</div>

			<div class="modal-body">
				<iframe id="google-frame" width="100%" style="min-height: 680px !important; height: 80% !important;" src="https://www.google.com/webhp?igu=1" frameborder="0"></iframe>
			</div>

		</div>
	</div>
</div>

<div id="mdProdMovs" tabindex='-1' class="modal fade" style="overflow: auto;">				
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">

			<div class="modal-header">				
				<button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><i class="fa fa-md fa-dollar"></i> Movimentos do Produto do Cliente</h4>
			</div>

			<div class="modal-body">
				<div class="table-responsive" ng-if="aCliProdMovs.length && aUserAccess">
					<table class="table table-striped display nowrap">
						<thead class="thead">
							<tr>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_id')">Reg.</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_origem')">Origem</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_operacao')">Operação</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_ori_compra')">Origem da Compra</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_uf')">UF</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_data')">Data</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_cfop')">CFOP</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_icms_cst')">CST ICMS</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_icms_aliquota')">ALQ. ICMS</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_pis_cst')">CST PIS</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_pis_aliquota')">ALQ. PIS</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_cofins_cst')">CST COFINS</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_cofins_aliquota')">ALQ. COFINS</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_total_item')">Valor Total</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cmv_imu_id')">ID. Arq.</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('cpd_incdate')">Data inc.</a></th>
							</tr>
						</thead>			
						<tbody>		  				
							<tr ng-animate="'animate'" ng-repeat="movimento in aCliProdMovs">
								<td class="text-nowrap">{{movimento.cmv_id}}</td>
								<td class="text-nowrap">{{movimento.cmv_origem}}</td>
								<td class="text-nowrap">{{movimento.cmv_operacao}}</td>
								<td class="text-nowrap">{{movimento.cmv_ori_compra}}</td>
								<td class="text-nowrap">{{movimento.cmv_uf}}</td>
								<td class="text-nowrap">{{movimento.cmv_data | date:'dd/MM/yyyy'}}</td>
								<td class="text-nowrap">{{movimento.cmv_cfop}}</td>
								<td class="text-nowrap">{{movimento.cmv_icms_cst}}</td>
								<td class="text-nowrap">{{movimento.cmv_icms_aliquota | number: 2}}</td>
								<td class="text-nowrap">{{movimento.cmv_pis_cst}}</td>
								<td class="text-nowrap">{{movimento.cmv_pis_aliquota | number: 2}}</td>
								<td class="text-nowrap">{{movimento.cmv_cofins_cst}}</td>
								<td class="text-nowrap">{{movimento.cmv_cofins_aliquota | number: 2}}</td>
								<td class="text-nowrap">{{movimento.cmv_valor_total | number: 2}}</td>
								<td class="text-nowrap">
									{{movimento.cmv_imu_id}}
									<a href="{{movimento.imu_content.substr(28)}}" class="btn btn-xs btn-link" ng-if="movimento.imu_typefile != 'X' && movimento.imu_typefile != 'F'" title="Fazer Download do arquivo" target="_Blank" download><i class="fa fa-download"></i></a>
									<a href="" class="btn btn-xs btn-link" ng-if="movimento.imu_typefile == 'X' || movimento.imu_typefile == 'F'" title="Fazer Download do arquivo" ng-click="baixaXMLECF(movimento.cmv_imu_id)"><i class="fa fa-download"></i></a>
								</td>
								<td class="text-nowrap">{{movimento.cmv_incdate | date:'dd/MM/yyyy'}}</td>
							</tr>						
						</tbody> 
					</table>
				</div>
			</div>

			<div class="modal-footer">
				<div class="row">
					<div class="col-sm-4 col-sm-offset-8">
						<button class="btn btn-block btn-xs btn-default" data-dismiss="modal" aria-label="Close">Fechar </button>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>