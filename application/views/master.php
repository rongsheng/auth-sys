<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Employee Management</title>
    {nocache}
    <link rel="stylesheet/less" type="text/css" href="{$static_less}">
    {/nocache}
  </head>
  <body>
    <div id="container">
      <mp:Content />
    </div>
    <script src="/js/less.min.js" type="text/javascript"></script>
    <script src="/js/require.js"></script>
    <script>
      requirejs.config({
          baseUrl: '/static-assets/js',
          paths: {
              jquery: '/js/jquery',
              underscore: '/js/underscore',
              backbone: '/js/backbone',
              bootstrap: '/js/bootstrap',
              text: '/js/text'
          },
          shim: {
              'jquery': {
                  exports: '$'
              },
              'underscore': {
                  deps: ['jquery'],
                  exports: '_'
              },
              'backbone': {
                  deps: ['underscore', 'jquery'],
                  exports: 'Backbone'
              },
              'bootstrap': {
                  deps: ['jquery'],
                  exports: 'bs'
              }
          }
      });
      {nocache}
      requirejs(['{$load_js}']);
      {/nocache}
    </script>
  </body>
</html>