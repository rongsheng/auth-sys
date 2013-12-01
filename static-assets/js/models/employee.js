define(['jquery',
    'underscore',
    'backbone'],
  function ($, _, Backbone) {
  	var Employee = Backbone.Model.extend({
  		url: '/api/employee_service/getDetails',
  		
  		check: function() {
  			//@TODO
  		}
  	});

  	return Employee;
});