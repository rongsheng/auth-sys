define(['jquery',
    'underscore',
    'backbone',
    'text!templates/employee-pagination.html'],
  function ($, underscore, backbone, EmployeePaginationTemplate) {
    var EmployeePaginationView = Backbone.View.extend({
        compilePagination: _.template(EmployeePaginationTemplate),
        el: '#pagination-wrapper',
        url: '',
        totalPage: 0,

        events: {
            'click .pagination .inner': 'navigate',
            'click .pagination .first': 'first',
            'click .pagination .last': 'last'
        },

        setBaseUrl: function(url) {
            this.url = url;
        },
        
        navigate: function(e) {
            e.preventDefault();
            var page = $(e.currentTarget).text();
            Backbone.history.navigate(this.url + page, true);
        },

        first: function() {
            Backbone.history.navigate(this.url + '1', true);
        },

        last: function() {
            Backbone.history.navigate(this.url + this.totalPage, true);
        },

        render: function(start, end, index, totalPage) {
            this.totalPage = totalPage;
            var template = this.compilePagination({
                start: start,
                end: end,
                index: index,
                total: totalPage
            });

            $(this.el).html(template);
            
            $(this.el).find('#start-page').text(index + 1);
            $(this.el).find('#total-page').text(totalPage);
        }
    });

    return EmployeePaginationView;
});