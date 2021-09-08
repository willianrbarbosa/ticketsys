angular.module('ticket_sys').config(['growlProvider', function (growlProvider) {
  	//Configuração do tempo que a mensagem ficará na tela
  	growlProvider.globalTimeToLive({success: 4000, error: 10000, warning: 5000, info: 4000});
  	growlProvider.globalPosition('bottom-left');
}]);