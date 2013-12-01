define(['jquery',
    'underscore',
    'backbone',
    'models/employee',
    'text!templates/employee-item.html',
    'text!templates/user-details.html'],
  function ($, _, Backbone, Employee,
    EmployeeItemTemplate, UserDetailsTemplate) {
    var EmployeeItemView = Backbone.View.extend({
        //template used to render the table row
        compile: _.template(EmployeeItemTemplate),
        //template used to render the user details modal
        compileUserDetails: _.template(UserDetailsTemplate),
        tagName: 'tr',
        emp_no: null,

        initialize: function(options) {
            this.model = new Employee();
            this.id = options.id;
            this.root = options.root;
            _.bindAll(this, 'fetchSuccess', 'fetchFailed', 'fetch', 'render');
        },

        events: {
            'click': 'fetch'
        },

        fetch: function() {
            if (_.isEmpty(this.model.toJSON())) {
                this.model.fetch({
                    data: {
                        id: this.emp_no,
                    },
                    reset: true,
                    success: this.fetchSuccess,
                    error: this.fetchFailed
                });
            }
        },

        fetchSuccess: function(model, response) {
            if (response && response.data) {
                console.log(response);
                //format the to_date from data
                if (response.data.to_date == '9999-01-01') {
                    response.data.to_date = 'Today';
                }
                //render the user details modal
                var template = this.compileUserDetails({
                    data: response.data
                });
                $('#user-details-modal').html(template);
                $('#user-details-modal').modal('show');
            } else {
                //show error here
            }
        },

        fetchFailed: function() {

        },

        render: function(model) {
            this.emp_no = model.get('emp_no');
            //NOTE: model shown here is not the same as the model we are
            //gonna get from fetching. (different API)
            var template = this.compile({data: model.toJSON()});
            var element = $(this.el).html(template);
            $(this.root).append(element);
            return true;
        }
    });

    return EmployeeItemView;
});