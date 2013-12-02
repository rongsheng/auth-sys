define(['jquery',
    'views/Login'],
  function ($, LoginView) {
  	$(document).ready(function() {
  		var loginView = new LoginView();
  		loginView.render();
  	});
});