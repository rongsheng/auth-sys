define(['jquery',
    'underscore',
    'backbone',
    'bootstrap',
    'views/UserDetails',
    'views/EmployeeTable'],
  function ($, _, Backbone, Bootstrap, UserDetailsView,
    EmployeeTableView) {
    'use strict';

    var elView = new EmployeeTableView({
        el: '#employee-table-wrapper'
    });

    var AppRouter = Backbone.Router.extend({
        routes: {
            ':type/:keyword/:page': 'search',
            ':page': 'goto_page',
            '*action': 'default'
        },

        search: function(type, keyword, page) {
            elView.render(page - 1, type, keyword);
        },

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