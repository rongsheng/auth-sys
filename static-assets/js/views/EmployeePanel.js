define(['jquery',
    'underscore',
    'backbone',
    'views/EmployeePagination',
    'text!templates/employee-panel.html'
    ],
  function ($, underscore, backbone, EmployeePaginationView,
    EmployeePanelTemplate) {
    'use strict'
    var EmployeePanelView = Backbone.View.extend({
        compilePanel: _.template(EmployeePanelTemplate),
        start: 0,
        size: 15,
        searchColumn: null,
        tmpColumn: 'all',
        events: {
            'click #filter-clear': 'clear',
            'click .filter-column': 'setSearchColumn',
            'keyup #search-input' : 'search'
        },

        initialize: function(options) {
            _.bindAll(this, 'refresh');
            this.start = options.start;
            this.size = options.size;
            this.keyword = null;
            this.allevents = options.allevents;
            this.allevents.on('panel:refresh', this.refresh);
        },

        /**
         * clear the search options, including search type and
         * keyword, then call the default router
         * @return {null}
         */
        clear: function() {
            $(this.el).find('#search-label').text('Any');
            $(this.el).find('#search-input').val('');
            
            this.searchColumn = null,
            this.tmpColumn = 'all';
            this.start = 0;
            this.keyword = null,
            Backbone.history.navigate(
                this.getUrlBase(this.searchColumn, this.keyword) + (this.start + 1),
            true);
        },

        getUrlBase: function(column, keyword) {
            if (column && keyword) {
                return '#' + column + '/' + keyword + '/';
            } else {
                return '#';
            }
        },

        setSearchColumn: function(e) {
            var $target = $(e.currentTarget);
            /* save the temporary column to tmpColumn, the searchColumn is set
               until user hit enter for search */
            this.tmpColumn = $target.data('column');
            $(this.el).find('#search-label').text($target.text());
            $(this.el).find('#search-input').focus();
        },

        search: function(e) {
            if(e.which == 13) {
                //formly set the searchColumn here
                this.searchColumn = this.tmpColumn;
                this.keyword = $(e.currentTarget).val();
                Backbone.history.navigate(
                    this.getUrlBase(this.searchColumn, this.keyword) + (this.start + 1),
                true);
            }
        },

        renderPagination: function(start, totalPage) {
            var index = start;
            var start = start - 2 
            var end = start + (totalPage >= 5 ? 5 : totalPage);
            //@NEED TO DOUBLE CHECK THE LOGIC HERE
            if (start < 0) {
                start = 0;
                end = 5;
            } 
            if (end > totalPage) { 
                start = start - (end - totalPage) >= 0 ? start - (end - totalPage) : 0;
                end = totalPage;
            }
            if (this.pView) {
                this.pView.remove();
            }
            
            if (!this.epView) {
               this.epView = new EmployeePaginationView(); 
            }

            this.epView.setBaseUrl(this.getUrlBase(this.searchColumn, this.keyword));
            this.epView.render(start, end, index, totalPage);
        },

        refresh: function(start, total, size) {
            var totalPage = 1;
            if (total % size == 0) {
                totalPage = Math.floor(total / size);
            } else {
                totalPage = Math.floor(total / size) + 1;
            }
            this.renderPagination(start, totalPage);
        },

        render: function() {
            $(this.el).append(this.compilePanel());
        }
    });

    return EmployeePanelView;
});