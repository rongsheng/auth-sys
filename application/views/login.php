<div class="error-wrapper alert alert-warning alert-dismissable" id="error-message"></div>
<div id="login-form" class="login-form">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-2 control-label" for="textinput">Username:</label>
      <div class="col-sm-8 input-group-wrapper">
        <div class="input-group">
          <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
          <input id="username-input" maxlength="31" name="textinput" type="text" placeholder="your username(case sensitive)" class="form-control">
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="passwordinput">Password:</label>
      <div class="col-sm-8 input-group-wrapper">
        <div class="input-group">
          <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
          <input id="password-input" maxlength="11" name="textinput" type="password" placeholder="your password" class="form-control">
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-7 input-group-wrapper">
        <button id="login-btn" class="btn btn-success pull-right">Login</button>
      </div>
    </div>
  </div>
</div>