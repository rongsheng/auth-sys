(function() {
define(['jquery',
    'underscore',
    'backbone',
    'views/EmployeeTable'],
  function ($, _, Backbone,
    EmployeeTableView) {
    'use strict';

    $(document).ready(function() {
        var elView = new EmployeeTableView({
            el: '#employee-table-wrapper'
        });

        var AppRouter = Backbone.Router.extend({
            routes: {
                ':type/:keyword/:page': 'search',
                ':page': 'goto_page',
                '*action': 'default'
            },

            /**
             * search subordinate based on type and keyword,
             * and render the table view
             * @param  {string} type
             * @param  {string} keyword
             * @param  {integer} page
             */
            search: function(type, keyword, page) {
                elView.render(page - 1, type, keyword);
            },

            /**
             * render the page {page}
             * @param  {integer} page
             */
            goto_page: function(page) {
                if (!isNaN(page) && page > 0) {
                    elView.render(page - 1);
                }
            },

            default: function() {
                elView.render();
            }
        });

        var appRouter = new AppRouter();
        Backbone.history.start();
    });
  });
})();