nodejs r.js -o app.build.js

mkdir ../build-assets/css
lessc -x --yui-compress ../static-assets/less/login.less > ../build-assets/css/login.css;
lessc -x --yui-compress ../static-assets/less/main.less > ../build-assets/css/main.css;
