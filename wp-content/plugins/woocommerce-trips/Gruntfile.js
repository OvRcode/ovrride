module.exports = function(grunt){
  grunt.initConfig({
    csslint: {
      strict: {
        options: {
          ids: false,
          important: false
        },
        src: ['assets/css/trip_admin.css']
      },
    },
    cssmin: {
      target: {
        src: ['assets/css/trip_admin.css'],
        dest: 'assets/css/trip_admin.min.css',
      }
    },
    phplint: {
      adminViews: {
        src: ['includes/admin/views/*.php']
      },
      adminPHP: {
        src: ['includes/admin/*.php']
      },
      mainPHP: {
        src: ['woocommerce-trips.php']
      },
      includePHP: {
        src: ['includes/*.php']
      }
    },
    uglify: {
      admin: {
        options: {
          mangle: false,
          preserveComments: false,
          title: 'Admin Uglify',
          message: 'admin uglify complete',
        },
        files: {
          'assets/js/trips_admin.min.js': ['assets/js/trips_admin.js']
        }
      },
    },
    jshint: {
      grunt: {
        src: ['Gruntfile.js'],
      },
      admin: {
        src: ['assets/js/trips_admin.js'],
      },
      frontEnd: {
        src: ['assets/js/front_end.js'],
      }
    },
    watch: {
      grunt: {
        files: ['Gruntfile.js'],
        tasks: ['jshint:grunt'],
      },
      views: {
          files: ['includes/admin/views/*.php'],
          tasks: ['phplint:adminViews'],
        },
      adminPHP: {
        files: ['includes/admin/*.php'],
        tasks: ['phplint:adminPHP'],
      },
      adminCSS: {
        files: ['assets/css/trip_admin.css'],
        tasks: ['csslint', 'cssmin'],
      },
      mainPHP: {
          files: ['woocommerce-trips.php'],
          tasks: ['phplint:mainPHP'],
        },
      includePHP: {
        files: ['includes/*.php'],
        tasks: ['phplint:includePHP'],
      },
      adminJS: {
        files: ['assets/js/trips_admin.js'],
        tasks: ['jshint:admin', 'uglify:admin']
      },
      frontEndJS: {
        files: ['assets/js/front_end.js'],
        tasks: ['jshint:frontEnd'],
      }
    },
  });
  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-csslint');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-phplint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-notify');
  
  // Tasks
  grunt.registerTask('default', ['csslint','jshint','concat','cssmin','phplint']);
};