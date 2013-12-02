(function() {
define(['jquery',
    'underscore',
    'backbone',
    'views/UserDetails'],
  function ($, _, Backbone, UserDetailsView) {
    'use strict';   
    $(document).ready(function() {
        var udView = new UserDetailsView({
            el: '#user-details-wrapper',
            id: user_id
        });
        udView.render();
    });
  });
})();