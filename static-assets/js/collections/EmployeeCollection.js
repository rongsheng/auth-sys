define(['jquery',
    'underscore',
    'backbone',
    'models/employee'],
  function ($, _, Backbone, Employee) {
  	var EmployeeCollection = Backbone.Collection.extend({
  		  model: Employee,
        url: '/api/employee/getSubordinate',
    		check: function() {
    			//@TODO
    		}
  	});

  	return EmployeeCollection;
});