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
    'views/UserDetails'],
  function ($, underscore, backbone, UserDetailsView) {
    'use strict';   
    $(document).ready(function() {
        //initlaise user details view, this display 
        //the info for current user
        var udView = new UserDetailsView({
            el: '#user-details-wrapper',
            id: user_id,
            hideControl: true
        });

        //and show it on page
        udView.render();
    });
  });
})();