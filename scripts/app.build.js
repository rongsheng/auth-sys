/**
 * Application Builder Javascript (Config file)
 *
 * @sheldon rong
 */
({
    appDir: "../static-assets",
    baseUrl: "js",
    dir: "../build-assets",
    paths: {
        jquery: 'utils/jquery',
        underscore: 'utils/underscore',
        backbone: 'utils/backbone',
        bootstrap: 'utils/bootstrap',
        text: 'utils/text'
    },
    modules: [
      {  name: "login"  },
      {  name: "manager" },
      {  name: "employee"  }
    ]
})
