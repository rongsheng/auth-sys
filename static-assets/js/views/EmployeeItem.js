define(['jquery',
    'underscore',
    'backbone',
    'bootstrap',
    'models/employee',
    'text!templates/employee-item.html',
    'text!templates/user-details.html',
    'text!templates/error.html'],
  function ($, underscore, backbone, bs, Employee,
    EmployeeItemTemplate, UserDetailsTemplate,
    ErrorTemplate) {
    'use strict'
    var EmployeeItemView = Backbone.View.extend({
        //template used to render the table row
        compile: _.template(EmployeeItemTemplate),
        //template used to render the user details modal
        compileUserDetails: _.template(UserDetailsTemplate),
        compileError: _.template(ErrorTemplate),
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
            this.clearError();
            if (_.isEmpty(this.model.toJSON())) {
                this.model.fetch({
                    data: {
                        id: this.emp_no,
                    },
                    reset: true,
                    success: this.fetchSuccess,
                    error: this.fetchFailed
                });
            } else {
                this.showDetails(this.model.get('data'));
            }
        },

        fetchSuccess: function(model, response) {
            if (response && response.status == 'success') {
                //format the to_date from data
                if (response.data.to_date == '9999-01-01') {
                    response.data.to_date = 'Today';
                    this.model.set({
                        to_date: 'Today'
                    });
                }
                //render the user details modal
                this.showDetails(response.data);
            } else {
                this.showError('Failed to fetch user details');
            }
        },

        fetchFailed: function() {
            this.showError('Failed to fetch user details, please refresh and try again.');
        },

        showError: function(message) {
            $('#error-message').html(this.compileError({message:message}));
        },

        clearError: function() {
            $('#error-message').html('');
        },

        showDetails: function(data) {
            var template = this.compileUserDetails({
                data: data
            });
            $('#user-details-modal').html(template);
            $('#user-details-modal').modal('show');
        },

        renderEmptyRow: function() {
            $(this.root).append("<td class='empty-row' colspan='5'>" +
                "Hmm, we can't find the person you've been looking for.</td>");
            return true;
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