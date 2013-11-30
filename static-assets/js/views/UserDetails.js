define(['jquery',
    'underscore',
    'backbone',
    'text!templates/user-details.html'],
  function ($, _, Backbone, UserDetailsTemplate) {
  	var UserDetailsView = Backbone.View.extend({
  		compile: _.template(UserDetailsTemplate),

  		render: function() {
  			console.log('WORKING!');
  		}
  	});

  	return UserDetailsView;
});