define(['jquery',
    'underscore',
    'backbone'],
  function ($, _, Backbone) {
    var LoginView = Backbone.View.extend({
        el: '#login-form',
        events: {
            'click #login-btn': 'login',
            'focus #username-input, #password-input': 'clear'
        },

        validateInputs: function() {
            var allGood = true;
            if (this.$username.val() == '' || this.$username.val().length > 31) {
                this.$username.parents('.form-group').addClass('has-error');
                allGood = false;
            }

            if (this.$password.val() == '' || this.$password.val().length > 11) {
                this.$password.parents('.form-group').addClass('has-error');
                allGood = false;
            }

            if (!allGood) {
                this.showError('Please check your username and password.');
            }
            return allGood;
        },

        clear: function(e) {
            $(e.currentTarget).parents('.form-group').removeClass('has-error');
        },

        login: function() {
            if (!this.validateInputs()) {
                return false;
            }

            $(this.el).removeClass('animated shake')

            //prepare input data
            var username = $(this.el).find('#username-input').val();
            var password = $(this.el).find('#password-input').val();
            $.ajax({
                url: '/api/auth_service/login',
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
                        this.showError(resp.reason, true);
                    }
                },
                error: function(resp) {
                    
                }
            });
        },

        showError: function(text, animated) {
            $('#error-message').text(text).css('visibility', 'visible');

            if (typeof animated != 'undefined' && animated) {
                $(this.el).addClass('animated shake');
            }
        },

        clearError: function() {
            $('#error-message').text('').css('visibility', 'hidden');
        },

        render: function() {
            this.$username = $(this.el).find('#username-input');
            this.$password = $(this.el).find('#password-input');
            return true;
        }
    });

    return LoginView;
});