define(['jquery', 'underscore', 'backbone'],
  function ($, underscore, backbone) {
  	var Employee = Backbone.Model.extend({
  		url: '/api/employee_service/getDetails',
  	});

  	return Employee;
});