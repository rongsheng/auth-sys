 requirejs.config({
      baseUrl: '/static-assets/js',
      paths: {
          jquery: 'utils/jquery',
          underscore: 'utils/underscore',
          backbone: 'utils/backbone',
          bootstrap: 'utils/bootstrap',
          text: 'utils/text'
      },
      shim: {
          'jquery': { exports: '$' },
          'underscore': { deps: ['jquery'], exports: '_' },
          'backbone': { deps: ['underscore', 'jquery'], exports: 'backbone' },
          'bootstrap': { deps: ['jquery'], exports: 'bs' }
      }
});
(function() {
define(['jquery',
    'underscore',
    'backbone',
    'views/EmployeeTable',
    'views/UserDetails'],
  function ($, underscore, backbone,
    EmployeeTableView, UserDetailsView) {
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

        //special treatment to profile me
        $('.profile-me').click(function() {
            var udView = new UserDetailsView({
                el: '#user-details-modal',
                id: user_id,
                hideControl: false
            });

            //and show it on page
            udView.render();
            $('#user-details-modal').modal().modal('show');
        })
    });
  });
})();