define(['jquery',
    'underscore',
    'backbone',
    'models/employee',
    'text!templates/user-details.html'],
  function ($, _, Backbone, Employee, UserDetailsTemplate) {
  	var UserDetailsView = Backbone.View.extend({
  		compile: _.template(UserDetailsTemplate),

      initialize: function(options) {
        this.model = new Employee();
        this.id = options.id;
        _.bindAll(this, 'fetchSuccess', 'fetchFailed', 'fetch', 'render');
      },

      fetch: function() {
          if (_.isEmpty(this.model.toJSON())) {
              this.model.fetch({
                  data: {
                      id: this.emp_no,
                  },
                  reset: true,
                  success: this.fetchSuccess,
                  error: this.fetchFailed
              });
          } else {
              this.showDetails(this.model.get('data'));
          }
      },

      showDetails: function(data) {
          var template = this.compile({
              data: data
          });
          $(this.el).html(template);

          //hide control elements
          $(this.el).find('.modal-footer').remove();
          $(this.el).find('a.close').remove();
      },

      fetchSuccess: function(model, response) {
          if (response && response.data) {
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
              //show error here
          }
      },

      fetchFailed: function() {

      },

  		render: function() {
          this.fetch();
          return true;
  		}
  	});

  	return UserDetailsView;
});