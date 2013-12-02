define(['jquery',
    'underscore',
    'backbone',
    'collections/EmployeeCollection',
    'views/EmployeePanel',
    'views/EmployeeItem',
    'text!templates/employee-table.html',
    'text!templates/error.html'
    ],
  function ($, underscore, backbone, EmployeeCollection,
    EmployeePanelView, EmployeeItemView,
    EmployeeTableTemplate, ErrorTemplate) {
    'use strict'
    var EmployeeTableView = Backbone.View.extend({
        compileTable: _.template(EmployeeTableTemplate),
        compileError: _.template(ErrorTemplate),
        allevents: {},
        start: 0,
        size: 15,
        initialized: false,
        epView: null,

        initialize: function() {
            _.bindAll(this, 'fetchSuccess', 'fetchFailed', 'fetch', 'render');
            this.collection = new EmployeeCollection();
            _.extend(this.allevents, Backbone.Events);
            this.allevents.on('table:fetch', this.fetch);
        },

        /**
         * fetch data to get all subordinates,
         * callbacks are fetchSuccess and fetchFailed
         * @param  {int} page
         * @param  {int} size
         * @param  {string} keyword
         * @param  {string} column
         */
        fetch: function(page, size, keyword, column) {
            this.collection.fetch({
                data: {
                    p: page,
                    s: size,
                    k: keyword,
                    c: column
                },
                reset: true,
                success: this.fetchSuccess,
                error: this.fetchFailed
            });
        },

        /**
         * when success, save the data into collection
         * and render table. send event to panel to refresh
         * and update pagination infomation
         * @param  {[type]} model
         * @param  {[type]} response
         * @return {[type]}
         */
        fetchSuccess: function(model, response) {
            if (response && response.status == 'success') {
                response = response.data;

                this.total = response.total;
                this.hit = response.hit;
                this.start = response.start;
                this.collection = new EmployeeCollection(response.data);

                this.renderTableContent();
                this.allevents.trigger('panel:refresh', this.start, this.total, this.size);
            } else {
                this.showError('Failed to fetch all subordinate staff.');
            }
        },

        fetchFailed: function() {
            this.showError('Failed to fetch all subordinate staff, please refresh and try again.');
        },

        showError: function(message) {
            $('#error-message').html(this.compileError({message:message}));
        },

        clearError: function() {
            $('#error-message').html('');
        },

        renderPanel: function() {
            this.epView = new EmployeePanelView({
                el: '#table-panel',
                allevents: this.allevents,
                start: this.start,
                size: this.size
            });
            this.epView.render();
        },

        /**
         * render the table using collection data
         */
        renderTableContent: function() {
            //render table header
            var $table = $(this.el).find('#employee-table');
            $table.html(this.compileTable());

            //clear content
            $table.find('#table-body').html('');
            //render table rows
            if (_.size(this.collection.models)) {
                _.each(this.collection.models, function(employee) {
                    var eiView = new EmployeeItemView({
                        root: '#table-body'
                    });
                    eiView.render(employee);
                });
            } else {
                var eiView = new EmployeeItemView({
                    root: '#table-body'
                });
                eiView.renderEmptyRow();
            }
            
        },

        /**
         * render panel first and also fetch data from backend
         * so we could render the table when data are ready
         * @param  {[type]} id
         * @param  {[type]} type
         * @param  {[type]} keyword
         * @return {[type]}
         */
        render: function(id, type, keyword) {
            if (typeof id != 'undefined') {
                this.start = id;
            }
            if (!this.initialized) {
                this.renderPanel();
            }
            
            this.fetch(this.start, this.size, keyword, type);
            this.initialized = true;
            return;
        }
    });

    return EmployeeTableView;
});