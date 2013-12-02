 requirejs.config({
  baseUrl: '/static-assets/js',
  paths: {
      jquery: 'utils/jquery',
      underscore: 'utils/underscore',
      backbone: 'utils/backbone',
      bootstrap: 'utils/bootstrap',
      text: 'utils/text'
  },
  shim: {
      'jquery': { exports: '$' },
      'underscore': { deps: ['jquery'], exports: '_' },
      'backbone': { deps: ['underscore', 'jquery'], exports: 'backbone' },
      'bootstrap': { deps: ['jquery'], exports: 'bs' }
  }
});
 
requirejs(['jquery',
    'views/Login'],
  function ($, LoginView) {
  	$(document).ready(function() {
  		var loginView = new LoginView();
  		loginView.render();
  	});
});