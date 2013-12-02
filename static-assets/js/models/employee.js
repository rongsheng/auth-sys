define(['jquery'],
  function ($) {
  	var Employee = Backbone.Model.extend({
  		url: '/api/employee_service/getDetails',
  	});

  	return Employee;
});