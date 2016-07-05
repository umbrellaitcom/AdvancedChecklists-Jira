module.exports = function (grunt) {

	var webpack = require('webpack');
	
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		watch: {
			compass: {
				files: ['public-src/sass/**'],
				tasks: ['compass:compile']
			},
			webpack: {
				files: ['public-src/js/**'],
				tasks: ['uglify']
			}
		},
		compass: {
			compile: {
				options: {
					basePath: 		'./',
					cacheDir: 		'public-src/sass/.sass-cache',
					sassDir: 			'public-src/sass/',
					cssDir: 			'web/public/css',
					imagesDir: 		'web/public/images',
					fontsDir: 		'web/public/fonts',
					outputStyle: 	'compressed'
				}
			}
		},
		uglify: {
			options: {
				mangle: false
			},
			build: {
				files: {
					'web/public/js/app.min.js': [
						'public-src/js/libs/*.js', 
						'public-src/js/*.js',
						'public-src/js/helpers/*.js',
						'public-src/js/controllers/*.js',
						'public-src/js/directives/*.js'
					]
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default tasks to create a build
	grunt.registerTask('default', [
		'compass:compile',
		'uglify'
	]);

	// Default tasks to create a build and continue watching
	grunt.registerTask('run', [
		'default',
		'watch'
	]);
};