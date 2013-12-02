(function() {
define(['jquery',
    'underscore',
    'backbone',
    'views/UserDetails'],
  function ($, _, Backbone, UserDetailsView) {
    'use strict';   
    $(document).ready(function() {
        //initlaise user details view, this display 
        //the info for current user
        var udView = new UserDetailsView({
            el: '#user-details-wrapper',
            id: user_id
        });

        //and show it on page
        udView.render();
    });
  });
})();