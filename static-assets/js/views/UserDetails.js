define(['jquery',
    'underscore',
    'backbone',
    'models/employee',
    'text!templates/user-details.html',
    'text!templates/error.html'],
  function ($, underscore, backbone, Employee,
    UserDetailsTemplate, ErrorTemplate) {
  	var UserDetailsView = Backbone.View.extend({
  		compile: _.template(UserDetailsTemplate),
      compileError: _.template(ErrorTemplate),

      initialize: function(options) {
        this.model = new Employee();
        this.id = options.id;
        this.hideControl = options.hideControl;
        _.bindAll(this, 'fetchSuccess', 'fetchFailed', 'fetch', 'render');
      },

      /**
       * fetch user details info, callbacks are fetchSuccess
       * and fetchFailed.
       */
      fetch: function() {
          if (_.isEmpty(this.model.toJSON())) {
              this.model.fetch({
                  data: {
                      id: this.id,
                  },
                  reset: true,
                  success: this.fetchSuccess,
                  error: this.fetchFailed
              });
          } else {
              this.showDetails(this.model.get('data'));
          }
      },

      /**
       * render details for user
       * @param  {[type]} data
       * @return {[type]}
       */
      showDetails: function(data) {
          var template = this.compile({
              data: data
          });
          $(this.el).html(template);

          if (this.hideControl) {
            //hide control elements
            $(this.el).find('.modal-footer').remove();
            $(this.el).find('a.close').remove();
          }
      },

      fetchSuccess: function(model, response) {
          if (response && response.status == 'success') {
              //format the to_date from data
              if (response.data.to_date == '9999-01-01') {
                  response.data.to_date = 'Today';
                  this.model.set({
                      to_date: 'Today'
                  });
              }
              //render the user details modal
              this.showDetails(response.data);
          } else {
              this.showError('Failed to fetch user details');
          }
      },

      fetchFailed: function() {
          this.showError('Failed to fetch user details, please refresh and try again.');
      },

      showError: function(message) {
          $('#error-message').html(this.compileError({message:message}));
      },

      clearError: function() {
          $('#error-message').html('');
      },

      /**
       * when view is rendered, immediately fetch data
       * and render the user details view
       * @return {boolean}
       */
  		render: function() {
          this.fetch();
          return true;
  		}
  	});

  	return UserDetailsView;
});