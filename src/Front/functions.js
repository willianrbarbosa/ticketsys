$(function () {
  $('.navbar-toggle').click(function () {
      $('.navbar-nav').toggleClass('slide-in');
      $('.side-body').toggleClass('body-slide-in');
      $('#search').removeClass('in').addClass('collapse').slideUp(200);
      $('.absolute-wrapper').toggleClass('slide-in');      
  });
 
 $('#search-trigger').click(function () {
      $('.navbar-nav').removeClass('slide-in');
      $('.side-body').removeClass('body-slide-in');
      $('.absolute-wrapper').removeClass('slide-in');
  });
});


$(document).ready(function() {
  $('[data-toggle="popover"]').popover();
  var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
  };

  if(!isMobile.any()) {
    new WOW().init();
  }  

  $('.toggle-nav').click(function(e) {
      jQuery(this).toggleClass('active');
      jQuery('.menu ul').toggleClass('active');
      e.preventDefault();
  });
  try{ace.settings.loadState('main-container')}catch(e){}
  try{ace.settings.loadState('sidebar')}catch(e){}
});
    
function toggleVisibility(item) {
    if(item.isVisible()) {
        item.hide();
    } else { 
        item.show();
    }
}