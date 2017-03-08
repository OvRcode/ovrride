/* jshint node:true */
/* global module */
module.exports = function( grunt ) {
	require( 'matchdep' ).filterDev( ['grunt-*', '!grunt-legacy-util'] ).forEach( grunt.loadNpmTasks );
	grunt.util = require( 'grunt-legacy-util' );

	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),
		jshint: {
			options: grunt.file.readJSON( '.jshintrc' ),
			grunt: {
				src: ['gruntfile.js']
			},
			all: ['gruntfile.js', 'js/*.js']
		},
		checktextdomain: {
			options: {
				correct_domain: false,
				text_domain: 'wp-statuses',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'_n:1,2,4d',
					'_ex:1,2c,3d',
					'_nx:1,2,4c,5d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: ['**/*.php', '!**/node_modules/**'],
				expand: true
			}
		},
		clean: {
			all: [ 'js/*.min.js', '<%= pkg.name %>.zip' ]
		},
		makepot: {
			target: {
				options: {
					domainPath: 'languages',
					exclude: ['/node_modules'],
					mainFile: 'wp-statuses.php',
					potFilename: 'wp-statuses.pot',
					processPot: function( pot ) {
						pot.headers['last-translator']      = 'imath <imath@cluster.press>';
						pot.headers['language-team']        = 'FRENCH <imath@cluster.press>';
						pot.headers['report-msgid-bugs-to'] = 'https://github.com/imath/wp-statuses/issues';
						return pot;
					},
					type: 'wp-plugin'
				}
			}
		},
		uglify: {
			minify: {
				extDot: 'last',
				expand: true,
				ext: '.min.js',
				src: ['js/*.js', '!*.min.js']
			},
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
				'<%= grunt.template.today("UTC:yyyy-mm-dd h:MM:ss TT Z") %> - ' +
				'https://github.com/imath/wp-statuses */\n'
			}
		},
		jsvalidate:{
			src: ['js/*.js'],
			options:{
				globals: {},
				esprimaOptions:{},
				verbose: false
			}
		},
		compress: {
			main: {
				options: {
					archive: '<%= pkg.name %>.zip'
				},
				files: [{
					expand: true,
					src: [
						'**/*',
						'!node_modules/**',
						'!npm-debug.log',
						'!.editorconfig',
						'!.git/**',
						'!.gitignore',
						'!.gitattributes',
						'!grunt/**',
						'!.jshintrc',
						'!.jshintignore',
						'!gruntfile.js',
						'!package.json'
					],
					dest: './'
				}]
			}
		}
	} );

	grunt.registerTask( 'jstest', ['jsvalidate', 'jshint'] );

	grunt.registerTask( 'shrink', ['clean', 'uglify'] );

	grunt.registerTask( 'commit',  ['checktextdomain', 'jstest'] );

	grunt.registerTask( 'release', ['checktextdomain', 'makepot', 'jstest', 'clean', 'uglify', 'compress'] );

	// Default task.
	grunt.registerTask( 'default', ['commit'] );
};
