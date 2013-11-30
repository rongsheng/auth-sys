define(['jquery',
    'underscore',
    'backbone'],
  function ($, _, Backbone) {
    var LoginView = Backbone.View.extend({
        el: '#login-form',
        events: {
            'click #login-btn': 'login'
        },

        login: function() {
            //prepare input data
            var username = $(this.el).find('#username-input').val();
            var password = $(this.el).find('#password-input').val();
            $.ajax({
                url: '/api/auth/login',
                type: 'POST',
                context: this,
                data: {
                    u: username,
                    p: password
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.status == 'success') {
                        window.location = '/main';
                    } else {
                        this.showError(resp.reason);
                    }
                },
                error: function(resp) {
  
                }
            });
        },

        showError: function() {
            alert('YAHOOOOOO!');
        },

        render: function() {
            return true;
        }
    });

    return LoginView;
});