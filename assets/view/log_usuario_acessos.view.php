<?php
	session_start();
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
				<a href="#/customersearchlog" class="kt-subheader__breadcrumbs-link bold">&nbsp;LOG de Acesso dos usuários</a>
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
								<a href="" ng-click="NewUserFavorite('LOG de Acesso dos usuários', '9. Configurações', '#'+url)" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-star ft-yellow"></i> <span class="kt-nav__link-text">Salvar como Favorito</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" ng-click="" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-md fa-question-circle"></i> <span class="kt-nav__link-text">Ajuda</span>
								</a>
							</li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__head"><strong>Exportar dados:</strong></li>
							<li class="kt-nav__separator"></li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-file-excel-o ft-success"></i>
									<span class="kt-nav__link-text">EXCEL</span>
								</a>
							</li>
							<li class="kt-nav__item">
								<a href="" class="kt-nav__link">
									<i class="kt-nav__link-icon fa fa-file-pdf-o ft-danger"></i>
									<span class="kt-nav__link-text">PDF</span>
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

			<div class="row" style="padding-top: 15px;">
				<div class="col-xs-6">
					<label>Selecione o Cliente</label>
					<selectize name="filter_customer" id="filter_customer" config="cfgUser" options="aUsuarios" ng-model="reportFilter.userTk" required></selectize>
				</div>
				<div class="col-xs-2">
		        	<label>Data de</label>
		        	<div class="input-group">
		        		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
				    	<input type="text" name="data_de" id="data_de" class="form-control my-control iptdate" ng-model="reportFilter.data_de" placeholder="DD/MM/YYY" required/>
				    </div>
		        </div>
	        	<div class="col-xs-2">
		        	<label>Data até</label>
		        	<div class="input-group">
		        		<span class="input-group-addon" style="font-size: 11px;"><i class="fa fa-lg fa-calendar"></i></span>
				    	<input type="text" name="data_ate" id="data_ate" class="form-control my-control iptdate" ng-model="reportFilter.data_ate" placeholder="DD/MM/YYY" required/>
				    </div>
		        </div>
				<div class="col-xs-2">
					<label>&nbsp</label>
					<button type="button" id="btnSearch" class="btn btn-block btn-sm btn-primary" ng-click="loadLogAcessoUsuariosFilter(reportFilter)"><i class="fa fa-search"></i></button>
				</div>
			</div>

			<div class="kt-portlet__body" >

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>

				<div class="alert alert-warning" id="div-loading">
					<span id="loading"><div class="loading-img"></div></span>
				</div>

				<div class="table-responsive" ng-if="aLog.length && aUserAccess">
					<table class="table table-striped display nowrap" id="tbCliMoviments">
			  			<thead class="thead">
							<tr>
								<!-- <th class="text-nowrap"><a href="">Ações</a></th> -->
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('usa_data')">Data/Hora</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('usa_user_id')">Reg. Usuário</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('usa_user_email')">Usuário</a></th>
								<th class="text-nowrap"><a href="" ng-click="ordenarPor('usa_ip')">IP</a></th>
							</tr>
						</thead>			
			  			<tbody>		  				
							<tr ng-animate="'animate'" ng-class="{'selecionado negrito':log.selecionado}" ng-repeat="log in aLog | filter: iptsearch | startFrom:currentPage*numPerPage | limitTo:numPerPage | orderBy: criterioOrdenacao: direcaoOrdenacao">
								<td class="text-nowrap">{{log.usa_data | date:'dd/MM/yyyy HH:mm:ss'}}</td>
								<td class="text-nowrap">{{log.usa_user_id}}</td>
								<td class="text-nowrap">{{log.usa_user_email}}</td>
								<td class="text-nowrap">{{log.usa_ip}}</td>
							</tr>						
			  			</tbody> 
					</table>
				</div>
				<hr/>
				<div class="row">
					<div class="col-sm-12 center-justify">
						<pagination ng-init="cPg=currentPage+1" ng-model="cPg" total-items="nPgTotal" ng-change="currentPage=cPg-1" max-size="maxSize" boundary-links="true"></pagination>
					</div>
				</div> 

			</div>
		</div>
	</div>
</div>
<br>