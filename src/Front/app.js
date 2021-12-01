angular.module("ticket_sys", ["ngMessages", "ngRoute", "ngAnimate", "ngMaterial", "angular-growl", "ui.calendar", "ui.bootstrap", "ui.utils", "ngTagsInput", "selectize", "bootstrap-switch", "textAngular", "dx", "angular-loading-bar", "colorpicker.module", "ngDragDrop", "ngQuill"]).run(function ($rootScope, $location, AuthenticationService, paginationConfig) {
  console.log("  ____          _                               ____ ___ \n / ___|___   __| | _____      ____ _ _ __ ___  / ___|_ _|\n| |   / _ \\ / _` |/ _ \\ \\ /\\ / / _` | '__/ _ \\ \\___ \\| | \n| |__| (_) | (_| |  __/\\ V  V / (_| | | |  __/  ___) | | \n \\____\\___/ \\__,_|\\___| \\_/\\_/ \\__,_|_|  \\___| |____/___|");

  $(".iptcur").mask('000.000.000,00',{reverse: true});
  $(".iptper").mask('000.00',{reverse: true});
  $(".iptint").mask('00000000000');
  $(".iptcest").mask('00.000.00');
  $('.iptdate').mask('00/00/0000', { placeholder: "DD/MM/AAAA" });
  $('.ipthora').mask('H0:M0',{
    translation: {
      'H': {pattern: /[0-2]/},
      'M': {pattern: /[0-5]/}
    },
    placeholder: "HH:MM"
  });
  $('.iptphone').mask('(00)00000-0000', { placeholder: "(__)_____-____" });
  $('.iptano').mask('0000');
  $(".iptcur-negative").mask('Z999,99',{
    reverse: true,
    translation: {
      '0': {pattern: /\d/},
      '9': {pattern: /\d/, optional: true},
      'Z': {pattern: /[\-\+]/, optional: true}
    }
  });

  paginationConfig.firstText = '<<';
  paginationConfig.previousText = '<';
  paginationConfig.lastText = '>>';
  paginationConfig.nextText = '>';

  $("#kt_aside_menu").scroll(function() {
    $("#kt_aside_menu").addClass("ps--active-y");
  });

  //Rotas que necessitam do login
  var userblockedroute = [
    'notifications', 
        
    'users', 'newuser', 'edituser', 'viewuser', 'myaccount',
    'userroutines', 'edituserroutine',
    
    'parameters', 'editparameter', 'newparameter',
    
    'accessprofiles','newaccessprofile','editaccessprofile',
    
    'categoriaticket', 'novocategoriaticket', 'editacategoriaticket',
    'grupotrabalho', 'novogrupotrabalho', 'editagrupotrabalho',
    'pastatrabalho', 'novopastatrabalho', 'editapastatrabalho',
    'origemticket', 'novoorigemticket', 'editaorigemticket',
    'prioridadeticket', 'novoprioridadeticket', 'editaprioridadeticket',
    'situacaoticket', 'novosituacaoticket', 'editasituacaoticket',
    'tipoatividade', 'novotipoatividade', 'editatipoatividade',
    'triagemticket', 'meustickets', 'meustrabalhos', 'ticket', 'novoticket',
    'detalheticket', 'ticketkanban', 'ticketagenda','ticketdashboard',
    'relatoriogerencial', 'relatoriodesempenho',

    'indicators', 'newindicator', 'editindicator'
  ];

  $rootScope.$on('$locationChangeStart', function () {
    $('html, body').animate({
        scrollTop: 0
    }, 400, 'linear');    
    verSession(); 
  }); 

  var verSession = function(){    
    AuthenticationService.verifySession().then(function(response){
      $rootScope.userLogged = response.data.session;

      if ($rootScope.userLogged == null)
          $rootScope.userLogged = false;
      if( ($rootScope.userLogged == false) && (userblockedroute.indexOf($location.path().replace(/[^a-z]+/g, "")) != -1) ){
        // $location.path('/login'); 
        // window.location.reload();
        $("#mdNaoLogado").modal("show");
        window.scrollTo(0,0);
      } else if( ($rootScope.userLogged == true) && (userblockedroute.indexOf($location.path().replace(/[^a-z]+/g, "")) == -1) ){
        $("#sidebar-toggle-icon").click();
        $location.path('/ticketdashboard');
      }

    }).catch(function(response){
      $rootScope.userLogged = false;
    });
  };
});