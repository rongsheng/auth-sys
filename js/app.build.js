/**
 * Application Builder Javascript (Config file)
 *
 * @sheldon rong
 */

({
    appDir: 'static-assets',
    baseUrl: 'static-assets',
	paths: {
	    jquery: 'utils/jquery',
	    underscore: 'utils/underscore',
	    backbone: 'utils/backbone',
	    bootstrap: 'utils/bootstrap',
	    text: 'utils/text'
	},
	shim: {
      'jquery': { exports: '$' },
      'underscore': { deps: ['jquery'], exports: '_' },
      'backbone': { deps: ['underscore', 'jquery'], exports: 'Backbone' },
      'bootstrap': { deps: ['jquery'], exports: 'bs' }
	},
    dir: 'build-assets',
    modules: [
    	{ name: 'login', exclude: ["jquery", 'underscore', 'backbone'] },
    	{ name: 'manager', exclude: ["jquery", 'underscore', 'backbone'] },
    	{ name: 'employee', exclude: ["jquery", 'underscore', 'backbone'] }
    ]
})