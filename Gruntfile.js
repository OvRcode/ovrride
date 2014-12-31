module.exports = function(grunt){
  
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    concat: {
      css: {
        src: ['lists/css/bootstrap.css','lists/css/font-awesome.min.css','lists/css/simple-sidebar.css','lists/css/lists.css'],
        dest: 'lists/css/application.css',
      },
      vendor: {
        options: {
          separator: ';',
        },
        src: ['lists/js/vendor/jquery.js','lists/js/vendor/bootstrap.js','lists/js/vendor/dropdown.js','lists/js/vendor/jquery.mobile-1.4.5.js',
              'lists/js/vendor/jquery.storageapi.min.js', 'lists/js/vendor/jquery.chained.js','lists/js/vendor/jquery.tinysort.min.js'],
        dest: 'lists/js/vendor.js',
      },
      lists: {
        options: {
          separator: ';',
        },
        src: ['lists/js/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_lists.js'],
        dest: 'lists/js/lists.js',
      },
      message: {
        options: {
          separator: ';',
        },
        src: ['lists/js/vendor.js','lists/js/partials/_common.js','lists/js/partials/_message.js'],
        dest: 'lists/js/message.js',
      },
      notes: {
        options: {
          separator: ';',
        },
        src: ['lists/js/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_notes.js'],
        dest: 'lists/js/notes.js',
      },
      settingsJS: {
        options: {
          separator: ';',
        },
        src: ['lists/js/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_settings.js'],
        dest: 'lists/js/settings.js',
      },
      summary: {
        options: {
          separator: ';',
        },
        src: ['lists/js/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_summary.js'],
        dest: 'lists/js/summary.js',
      },
      admin: {
        options: {
          separator: ';',
        },
        src: ['lists/js/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_admin.js'],
        dest: 'lists/js/admin.js',
      },
    },
    jshint: {
      files: ['Gruntfile.js', 'package.json', 'lists/js/partials/*.js'],
    },
  });


  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  // Tasks
  grunt.registerTask('default', ['jshint','concat']);

};