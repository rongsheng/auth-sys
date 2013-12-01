define(['jquery',
    'underscore',
    'backbone',
    'collections/EmployeeCollection',
    'views/EmployeePanel',
    'views/EmployeeItem',
    'text!templates/employee-table.html',
    ],
  function ($, _, Backbone, EmployeeCollection,
    EmployeePanelView, EmployeeItemView, EmployeeTableTemplate) {
    'use strict'
    var EmployeeTableView = Backbone.View.extend({
        compileTable: _.template(EmployeeTableTemplate),
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

        fetchSuccess: function(model, response) {
            this.total = response.total;
            this.hit = response.hit;
            this.start = response.start;
            this.collection = new EmployeeCollection(response.data);

            this.renderTableContent();
            this.allevents.trigger('panel:refresh', this.start, this.total, this.size);
        },

        fetchFailed: function(resp) {

        },

        renderPanel: function() {
            this.epView = new EmployeePanelView({
                el: '#control-panel',
                allevents: this.allevents,
                start: this.start,
                size: this.size
            });
            this.epView.render();
        },

        renderTableContent: function() {
            //render table header
            var $table = $(this.el).find('#employee-table');
            $table.html(this.compileTable());

            //clear content
            $table.find('#table-body').html('');
            //render table rows
            _.each(this.collection.models, function(employee) {

                console.log(employee);
                var eiView = new EmployeeItemView({
                    root: '#table-body'
                });
                eiView.render(employee);
            });
        },

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