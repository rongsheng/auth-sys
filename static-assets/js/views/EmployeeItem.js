define(['jquery',
    'underscore',
    'backbone',
    'text!templates/employee-item.html'],
  function ($, _, Backbone, EmployeeItemTemplate) {
    var EmployeeItemView = Backbone.View.extend({
        compile: _.template(EmployeeItemTemplate),
        tagName: 'tr',

        render: function(data) {
            $(this.el).append(this.compile(data));
            return true;
        }
    });

    return EmployeeItemView;
});