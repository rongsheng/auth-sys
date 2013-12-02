<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Employee Management</title>
    {nocache}
    {if $ENVIRONMENT == 'development'}
    <link rel="stylesheet/less" type="text/css" href="{$less}">
    {/if}
    {if $ENVIRONMENT == 'production'}
    <link rel="stylesheet" type="text/css" href="{$css}">
    {/if}
    {/nocache}
  </head>
  <body>
    <div id="container">
      <mp:Content />
    </div>
    {nocache}
    {if $ENVIRONMENT == 'development'}
      <script src="/js/less.min.js" type="text/javascript"></script>
      <script data-main="{$static_js}" src="js/require.js"></script>
    {/if}
    {if $ENVIRONMENT == 'production'}
      <script data-main="{$build_js}" src="js/require.js"></script>
    {/if}
    {/nocache}
  </body>
</html>