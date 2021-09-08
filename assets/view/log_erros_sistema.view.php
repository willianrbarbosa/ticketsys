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
				<a href="#/customersearchlog" class="kt-subheader__breadcrumbs-link bold">&nbsp;LOG de Erros do Sistema</a>
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
								<a href="" ng-click="NewUserFavorite('LOG de Erros do Sistema', '9. Configurações', '#'+url)" class="kt-nav__link">
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
			<div class="kt-portlet__body" >

				<div ng-show="error" class="alert alert-warning">
					{{error}}
				</div>

				<div class="alert alert-warning" id="div-loading">
					<span id="loading"><div class="loading-img"></div></span>
				</div>

				<div class="table-responsive" ng-if="aErrorLog.length && aUserAccess">
					<table class="table table-striped display nowrap" id="tbErrosLog">
			  			<thead class="thead">
							<tr>
								<!-- <th class="text-nowrap"><a href="">Ações</a></th> -->
								<th class="text-nowrap" width="5%"><a href="">Ações</a></th>
								<th class="text-nowrap" width="55%"><a href="">Nome do Arquivo</a></th>
								<th class="text-nowrap" width="20%"><a href="">Data de Modificação</a></th>
								<th class="text-nowrap" width="20%"><a href="">Tamanho</a></th>
							</tr>
						</thead>			
			  			<tbody>		  				
							<tr ng-repeat="log in aErrorLog">
								<td class="text-nowrap" export-no-show>
									<a href="" class="btn btn-xs btn-link" ng-if="log.log_view == 'S'" ng-click="showLog($index);"><i class="fa fa-search-plus"></i></a>
									<a href="{{log.log_path}}" class="btn btn-xs btn-link" download="{{log.log_name}}"><i class="fa fa-download"></i></a>
									<a class="btn btn-xs btn-link" ng-click="deleteLog('delete',$index);"><i class="fa fa-trash"></i></a>
								</td>
								<td class="text-nowrap">{{log.log_name}}</td>
								<td class="text-nowrap">{{log.log_date | date:'dd/MM/yyyy HH:mm:ss'}}</td>
								<td class="text-nowrap">{{log.log_size}}</td>
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
<div id="mdShowLog" tabindex='-1' class="modal fade" style="overflow: auto;">				
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">				
		        <button type="button" class="Close btn btn-link ft-red" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				<h5 class="modal-title"><i class="fa fa-md fa-plus"></i> Conteúdo do Log: {{eLog.log_name}}</h5>
			</div>

			<div class="modal-body" style="text-align: justify;">
				<div class="row">
					<div class='col-sm-12'>
						<textarea class="form-control my-control-txta" rows="25" data-ng-bind-html="eLog.log_cont" readonly></textarea>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<div class="row">
			      	<div class="col-sm-12 right-justify">
						<div class="col-sm-2 col-sm-offset-10">
							<button type="button" class="btn-block btn btn-xs btn-link" data-dismiss="modal">Fechar</button>
						</div>
			      	</div>
			    </div>
			</div>

		</div>
	</div>
</div>
<br>