define(['jquery',
    'underscore',
    'backbone',
    'models/employee'],
  function ($, underscore, backbone, Employee) {
  	var EmployeeCollection = Backbone.Collection.extend({
  		  model: Employee,
        url: '/api/employee_service/getSubordinate'
  	});

  	return EmployeeCollection;
});