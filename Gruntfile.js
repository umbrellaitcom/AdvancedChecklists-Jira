module.exports = function (grunt) {

	var webpack = require('webpack');
	
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		watch: {
			compass: {
				files: ['web/public-src/sass/**'],
				tasks: ['compass:compile']
			},
			webpack: {
				files: ['web/public-src/js/**'],
				tasks: ['uglify']
			}
		},
		compass: {
			compile: {
				options: {
					basePath: 		'./web',
					cacheDir: 		'public-src/sass/.sass-cache',
					sassDir: 			'public-src/sass/',
					cssDir: 			'public/css',
					imagesDir: 		'public/images',
					fontsDir: 		'public/fonts',
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
						'web/public-src/js/libs/*.js', 
						'web/public-src/js/*.js', 
						'web/public-src/js/controllers/*.js',
						'web/public-src/js/directives/*.js'
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