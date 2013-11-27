<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet/less" type="text/css" href="/static-assets/less/base.less">  
  </head>
  <div id="container">
    <div id="body">
      <div class="login-form">
        <form class="form-horizontal" role="form">
          <div class="form-group">
            <label class="control-label" for="textinput">Username:</label>
            <div class="col-sm-10">
              <input id="textinput" name="textinput" type="text" placeholder="placeholder" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label" for="passwordinput">Password</label>
            <div class="col-sm-10">
              <input id="passwordinput" name="passwordinput" type="password" placeholder="placeholder" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-10">
              <button id="button1id" name="button1id" class="btn btn-success">Login</button>
              <button id="button2id" name="button2id" class="btn btn-danger">Forget Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="/js/less.min.js" type="text/javascript"></script>
  </body>
</html>