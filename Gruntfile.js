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
        src: ['lists/js/vendor/jquery.js','lists/js/vendor/bootstrap.js','lists/js/vendor/jquery.storageapi.min.js', 
              'lists/js/vendor/jquery.chained.js','lists/js/vendor/jquery.tinysort.min.js'],
        dest: 'lists/js/uncompressed/vendor.js',
      },
      lists: {
        options: {
          separator: ';',
        },
        src: ['lists/js/uncompressed/vendor.js', 'lists/js/vendor/jquery.mobile-1.4.5.js','lists/js/partials/_common.js', 'lists/js/partials/_lists.js'],
        dest: 'lists/js/uncompressed/lists.js',
      },
      message: {
        options: {
          separator: ';',
        },
        src: ['lists/js/uncompressed/vendor.js','lists/js/partials/_common.js','lists/js/partials/_message.js'],
        dest: 'lists/js/uncompressed/message.js',
      },
      notes: {
        options: {
          separator: ';',
        },
        src: ['lists/js/uncompressed/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_notes.js'],
        dest: 'lists/js/uncompressed/notes.js',
      },
      settings: {
        options: {
          separator: ';',
        },
        src: ['lists/js/uncompressed/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_settings.js'],
        dest: 'lists/js/uncompressed/settings.js',
      },
      summary: {
        options: {
          separator: ';',
        },
        src: ['lists/js/uncompressed/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_summary.js'],
        dest: 'lists/js/uncompressed/summary.js',
      },
      admin: {
        options: {
          separator: ';',
        },
        src: ['lists/js/uncompressed/vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_admin.js'],
        dest: 'lists/js/uncompressed/admin.js',
      },
    },
    jshint: {
      files: ['Gruntfile.js', 'package.json', 'lists/js/partials/*.js'],
    },
    uglify: {
      admin: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/admin.min.js': ['lists/js/uncompressed/admin.js']
        }
      },
      lists: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/lists.min.js': ['lists/js/uncompressed/lists.js']
        }
      },
      message: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/message.min.js': ['lists/js/uncompressed/message.js']
        }
      },
      notes: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/notes.min.js': ['lists/js/uncompressed/notes.js']
        }
      },
      settings: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/settings.min.js': ['lists/js/uncompressed/settings.js']
        }
      },
      summary: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/summary.min.js': ['lists/js/uncompressed/summary.js']
        }
      },
    },
    watch: {
      css:{
        files: ['lists/css/*.css'],
        tasks: ['concat:css'],
      },
      vendor: {
        files: ['lists/js/vendor/*.js'],
        tasks: ['concat:vendor', 'uglify:vendor'],
      },
      common: {
        files: ['lists/js/partials/_common.js'],
        tasks: ['concat','uglify'],
      },
      admin: {
        files: ['lists/js/partials/_admin.js'],
        tasks: ['concat:admin', 'uglify:admin'],
      },
      lists: {
        files: ['lists/js/partials/_lists.js'],
        tasks: ['concat:lists', 'uglify:admin'],
      },
      message: {
        files: ['lists/js/partials/_message.js'],
        tasks: ['concat:message', 'uglify:message'],
      },
      notes: {
        files: ['lists/js/partials/_notes.js'],
        tasks: ['concat:notes', 'uglify:notes'],
      },
      settings: {
        files: ['lists/js/partials/_settings.js'],
        tasks: ['concat:settings', 'uglify:settings'],
      },
      summary: {
        files: ['lists/js/partials/_summary.js'],
        tasks: ['concat:settings', 'uglify:settings'],
      },
    },
  });


  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  // Tasks
  grunt.registerTask('default', ['concat','uglify']);

};