define(['jquery',
    'views/Login'],
  function ($, LoginView) {
  	var loginView = new LoginView();
  	loginView.render();
});