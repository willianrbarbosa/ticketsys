 <!-- begin:: Header Mobile -->
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed " ng-show="userLogged">
  <div class="kt-header-mobile__logo">
    <a href="#/">
      <img class="" alt="Ticket SYS WEB" src="src/img/logo.png" title="Ticket SYS WEB" type="image/png">
    </a>
  </div>
  <div class="kt-header-mobile__toolbar">
    <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
    <!-- <button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button> -->
    <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
  </div>
</div>
<!-- end:: Header Mobile -->  


<div class="kt-grid kt-grid--hor kt-grid--root" ng-show="userLogged">
  <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
  <!-- begin:: Aside -->
           
    <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside" style="opacity: 1;">
      <!-- begin:: Aside -->
      <div id="side-menu-todo" class="kt-aside__brand kt-grid__item kt_aside_brand" kt-hidden-height="65">
        <div class="kt-aside__brand-logo">
          <a href="#/">
            <img alt="Ticket SYS WEB" src="src/img/logo.png" class="img-logo-menu" title="Ticket SYS WEB" type="image/png">
          </a>
        </div>

        <div class="kt-aside__brand-tools">
          <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) "></path>
                  <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) "></path>
                </g>
              </svg>
            </span>
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero"></path>
                  <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) "></path>
                </g>
              </svg>
            </span>
          </button>
          <!--
          <button class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left" id="kt_aside_toggler"><span></span></button>
          -->
        </div>
      </div>
      <!-- end:: Aside -->  
      
      <?php   include('src/Front/view/menu.html');?>  
    </div>
    <!-- end:: Aside -->
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
      <!-- begin:: Header -->
      <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">
        <!-- begin:: Header Menu -->
        <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper" style="opacity: 1;">

          <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
            <ul class="kt-menu__nav">
              <li class="kt-menu__item  kt-menu__item--submenu kt-menu__item--rel" data-ktmenu-submenu-toggle="click" aria-haspopup="true">
                <a href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-text"><i class="kt-nav__link-icon fa fa-lg fa-star ft-yellow"></i>&nbsp;Meus Favoritos</span></a>
                <div class="kt-menu__submenu  kt-menu__submenu--fixed kt-menu__submenu--left" style="width:1000px">
                  <div class="kt-menu__subnav" ng-if="aUserFavorites.length">
                    <ul class="kt-menu__content">
                      <li class="kt-menu__item" ng-if="categoria_fav.group_by_key" ng-repeat="categoria_fav in aUserFavorites | groupBy: 'ufv_categoria'">
                        <h4 class="kt-menu__heading kt-menu__toggle"><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                          <span class="kt-menu__link-text">{{categoria_fav.ufv_categoria}}</span>
                          <i class="kt-menu__ver-arrow la la-angle-right"></i>
                        </h4>
                        <ul class="kt-menu__inner">
                          <li class="kt-menu__item " aria-haspopup="true" ng-repeat="favoritos in aUserFavorites | filter: {ufv_categoria:categoria_fav.ufv_categoria}">
                            <a href="{{favoritos.ufv_url}}" class="kt-menu__link ">
                              <span class="kt-menu__link-icon">
                                <i class="fa fa-sm fa-chevron-right"></i>&nbsp;
                              </span>
                              <span class="kt-menu__link-text">{{favoritos.ufv_descricao}}</span>
                            </a>
                            <span class="kt-menu__link-badge del-favorite-icon"><button type="button" ng-click="deleteUserFavorite(favoritos)" class="btn btn-sm btn-link ft-danger"><i class="fa fa-trash"></i></button></span>
                          </li>
                        </ul>
                      </li>
                    </ul>
                  </div>
                  <ul class="kt-menu__subnav" ng-if="!aUserFavorites.length">
                    <ul class="kt-menu__content">
                      <li class="kt-menu__item">
                        <ul class="kt-menu__inner">
                          <li class="kt-menu__item " aria-haspopup="true">
                            <a href="" class="kt-menu__link">
                              <span class="kt-menu__link-text ft-warning bold">Você ainda não adicionou nenhum item aos seus favoritos.</span>
                            </a>
                          </li>
                        </ul>
                      </li>
                    </ul>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <!-- end:: Header Menu -->
        
        <!-- begin:: Header Topbar -->
        <div class="kt-header__topbar">
          <!--begin: Triagem Tickets -->    
          <div class="kt-header__topbar-item dropdown" ng-show="aTriagemTickets.length && AccessRotFilter('triagemTicket')">
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px" aria-expanded="true">
              <span class="kt-header__topbar-icon kt-pulse kt-pulse--brand">
                <i class="fa fa-lg fa-address-card ft-info"></i>
                <span class="kt-pulse__ring"><span class="label label-danger">{{aTriagemTickets.length}}</span></span>
              </span>
            </div>
            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
              <form>
                <!--begin: Head -->
                <div class="kt-head kt-head--skin-dark kt-head--fit-x kt-head--fit-b" style="background-image: url(<?php echo $security->base_patch; ?>/src/img/bg-1.jpg)">
                  <h3 class="kt-head__title">
                    <span class="label label-danger">{{aTriagemTickets.length}}</span> Novos tickets pendentes para triagem
                  </h3>
                  <br/>
                </div>
                <!--end: Head -->

                <div class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll ps" data-scroll="true" data-height="300" data-mobile-height="200" style="height: 300px; overflow: hidden;">
                  <a ng-repeat="ticket in aTriagemTickets" href="#/triagemticket" class="kt-notification__item">
                    <div class="kt-notification__item-icon">
                      <i class="flaticon-more-v5 kt-font-info"></i>
                    </div>
                    <div class="kt-notification__item-details">
                      <div class="kt-notification__item-title">
                        <strong>#{{ticket.tkt_id}}</strong> | {{ticket.tkt_titulo}}
                      </div>
                      <div class="kt-notification__item-time">
                        <strong>{{ticket.abert_user_nome}}</strong> em {{ticket.tkt_abertura_data_comp | date:'dd/MM/yyyy HH:mm:ss'}}
                      </div>
                    </div>
                  </a>
                </div>
              </form>
            </div>
          </div>
          <!--end: Triagem Tickets -->

          <!--begin: Quick panel toggler -->
          <div class="kt-header__topbar-item kt-header__topbar-item--quick-panel" data-toggle="kt-tooltip" title="" data-placement="right" data-original-title="Minhas notificações">
            <span class="kt-header__topbar-icon" id="kt_quick_panel_toggler_btn">
              <i class="fa fa-lg fa-bell ft-primary"></i>
              <span class="label label-danger" ng-show="nUnRead > 0">{{nUnRead}}</span>
            </span>
          </div>

          <!--begin: User Bar -->
          <div class="kt-header__topbar-item kt-header__topbar-item--user">    
            <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
              <div class="kt-header__topbar-user">
                <span class="kt-header__topbar-welcome kt-hidden-mobile">Olá,</span>
                <span class="kt-header__topbar-username kt-hidden-mobile">{{aUserData.user_nome}}</span>
                <!--<img class="kt-hidden" alt="Pic" src="">
                use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                <span class="kt-badge kt-badge--username kt-badge--unified-primary kt-badge--lg kt-badge--rounded kt-badge--bold">{{aUserData.user_nome.substr(0,1)}}</span>
              </div>
            </div>

            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
              <!--begin: Head -->
              <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url(<?php echo $security->base_patch; ?>/src/img/bg-1.jpg)">
                <div class="kt-user-card__avatar">
                  <!--<img class="kt-hidden" alt="Pic" src="">
                  use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                  <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-primary ft-white">{{aUserData.user_nome.substr(0,1)}}</span>
                </div>
                <div class="kt-user-card__name">{{aUserData.user_nome}}</div>
                <div class="kt-user-card__badge" ng-show="nUnRead > 0" ><span class="btn btn-success btn-sm btn-bold btn-font-md"><span class="badge badge-important">{{nUnRead}}</span> Notificações</span></div>
              </div>
              <!--end: Head -->

              <!--begin: Navigation -->
              <div class="kt-notification" >
                <a href="" data-toggle="modal" data-target="#mdNewTicket" class="kt-notification__item">
                  <div class="kt-notification__item-icon"><i class="fa fa-address-card ft-warning ft-bold"></i></div>
                  <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title kt-font-bold">Novo Ticket</div>
                    <div class="kt-notification__item-time">Abrir novo Ticket para a equipe de T.I.</div>
                  </div>
                </a>
                <a href="#/meustickets" class="kt-notification__item">
                  <div class="kt-notification__item-icon"><i class="fa fa-address-card ft-warning ft-bold"></i></div>
                  <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title kt-font-bold">Meus Tickets</div>
                    <div class="kt-notification__item-time">Acompanhe seus tickets abertos com a equipe de T.I.</div>
                  </div>
                </a>
                <a href="#/myaccount/{{aUserData.user_token}}" class="kt-notification__item">
                  <div class="kt-notification__item-icon"><i class="fa fa-user ft-primary ft-bold"></i></div>
                  <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title kt-font-bold">Meu perfil</div>
                    <div class="kt-notification__item-time">Alterar dados cadastrais e senha.</div>
                  </div>
                </a>
                <a href="#/notifications" class="kt-notification__item">
                  <div class="kt-notification__item-icon"><i class="fa fa-bell ft-success"></i></div>
                  <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title kt-font-bold"><span class="badge badge-important" ng-show="nUnRead > 0">{{nUnRead}}</span><span ng-show="nUnRead == 0">&nbsp;</span></div>
                    <div class="kt-notification__item-time">Notificações do sistema.</div>
                  </div>
                </a>
                <a href="#/registrationlog" class="kt-notification__item">
                  <div class="kt-notification__item-icon"><i class="fa fa-wpforms ft-gray-dk"></i></div>
                  <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title kt-font-bold">Minhas atividades</div>
                    <div class="kt-notification__item-time">Logs e histórico de registros.</div>
                  </div>
                </a>

                <div class="row">
                  <div class="col-sm-4">
                    <span>Tema</span><br>
                    <label class="switch" ng-if="aUserData.user_tema != 'D'" title="Mudar tema">
                      <input type="checkbox" ng-click="setDarkTheme(true)">
                      <span class="slider round"></span>
                    </label>
                    <label class="switch" ng-if="aUserData.user_tema == 'D'" title="Mudar tema">
                      <input type="checkbox" checked ng-click="setDarkTheme(true)">
                      <span class="slider round"></span>
                    </label>
                    <br/>
                  </div>
                   <div class="col-sm-4">
                    <br/>
                    <a href="" class="btn btn-block btn-primary btn-sm btn-bold" ng-click="loadUserClientes()" title="Selecionar outra Empresa">&nbsp;<i class="fa fa-lg fa-exchange"></i>&nbsp;</a>
                    <br/>
                  </div>
                  <div class="col-sm-4">
                    <br/>
                    <a href="src/Model/Logout.php" class="btn btn-block btn-danger btn-sm btn-bold">Logout</a>
                    <br/>
                  </div>
                </div>
              </div>
              <!--end: Navigation -->
            </div>
          </div>
          <!--end: User Bar --> 

        </div>
        <!-- end:: Header Topbar -->
      </div>
      <!-- end:: Header -->
      
      <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <!-- begin:: Content -->
        <div ng-view="">          
          <script type="text/javascript">
            $('#file_ticket').on('click', function() {
              $('#ticket_files').trigger('click');
            });
            $('#ticket_files').on('change', function() {
              $('#file_ticket').hide("slow");
              $('#div-selected-files').show("slow");
              $('#selected-files').html($(this)[0].files[0].name);
            });
          </script>

        </div>
        <!-- end:: Content -->        
      </div>           
    </div>
  </div>
</div>

<!-- begin::Quick Panel -->
<div id="kt_quick_panel" class="kt-quick-panel" style="opacity: 1;" ng-show="userLogged">
  <a href="https://keenthemes.com/metronic/preview/demo1/crud/forms/controls/base.html#" class="kt-quick-panel__close" id="kt_quick_panel_close_btn"><i class="flaticon2-delete"></i></a>

  <div class="kt-quick-panel__nav bg-primary ft-white" kt-hidden-height="66">
    <div class="row form-group">
      <div class="col-sm-12">
        <h4 class="ft-white"><strong><i class="fa fa-lg fa-bell ft-white"></i> Notificações</strong></h4>
      </div>
    </div>
  </div>

  <div class="kt-quick-panel__content">
    <div class="kt-notification">
      <div ng-repeat="notificacao in aNotificacoes">
        <a href="#" ng-click="ReadNotification(notificacao, 'S')" class="kt-notification__item">
          <div class="kt-notification__item-icon">
            <i class="btn btn-xs no-hover fa fa-1x"
              ng-class="{'fa-exclamation-triangle ft-warning': notificacao.ntf_tipo_alerta == 'warning', 
                     'fa-ban ft-danger': notificacao.ntf_tipo_alerta == 'danger',
                     'fa-plus ft-primary': notificacao.ntf_tipo_alerta == 'primary',
                     'fa-check ft-success': notificacao.ntf_tipo_alerta == 'success'}">
            </i>
          </div>
          <div class="kt-notification__item-details">
            <div class="kt-notification__item-title">{{notificacao.ntf_notificacao}}</div>
            <div class="kt-notification__item-time">{{notificacao.ntf_data_hora | date:'dd/MM/yyyy HH:mm:ss'}}</div>
          </div>
        </a>
      </div>
      <a href="#/notification" class="kt-notification__item">
        <div class="kt-notification__item-icon">
          <i class="flaticon-more kt-font-success"></i>
        </div>
        <div class="kt-notification__item-details">
          <div class="kt-notification__item-title">
            Veja todas as notificações
          </div>
        </div>
      </a>
    </div>
  </div>
</div>
<!-- end::Quick Panel -->


<div ng-view="" ng-if="!userLogged"></div>