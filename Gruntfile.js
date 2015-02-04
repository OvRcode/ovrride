module.exports = function(grunt){
  
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    notify_hooks: {
      options: {
        enabled: true,
        title: "OvR Trip Lists",
        success: true
      }
    },
    manifest: {
      generate: {
        options:{
          basePath: "lists/",
          network:["/api", "/login","*"],
          fallback: [ 'fonts/fontawesome-webfont.woff fonts/fontawesome-webfont.woff',
                    'fonts/fontawesome-webfont.eot fonts/fontawesome-webfont.eot',
                    'fonts/fontawesome-webfont.svg fonts/fontawesome-webfont.svg',
                    'fonts/fontawesome-webfont.ttf fonts/fontawesome-webfont.ttf',
                    'fonts/FontAwesome.otf fonts/FontAwesome.otf'],
          timestamp: true,
          verbose: true,
          hash: true,
        },
      src: ["js/*.js",
            "images/*.gif",
            "images/*.jpg",
            "images/*.png",
            "images/ios/*.png",
            "images/ios/iconset/*.png",
            "fonts/*",
            "css/application.min.css",
            "*.html",
            "*.php",
            "lists.version"],
      dest: "lists/manifest.appcache"
      },
    },
    csslint: {
      strict: {
        options: {
          ids: false,
          important: false
        },
        src: ['lists/css/lists.css', 'lists/css/simple-sidebar.css']
      },
    },
    jshint: {
      grunt: {
        src: ['Gruntfile.js'],
      },
      admin: {
        src: ['lists/js/partials/_admin.js'],
      },
      common: {
        src: ['lists/js/partials/_common.js'],
      },
      lists: {
        src: ['lists/js/partials/_lists.js'],
      },
      message: {
        src: ['lists/js/partials/_message.js'],
      },
      reports: {
        src: ['lists/js/partials/_reports.js'],
      },
      settings: {
        src: ['lists/js/partials/_settings.js'],
      },
      summary: {
        src: ['lists/js/partials/_summary.js'],
      },
    },
    phplint: {
      api: {
        src: ['lists/api/index.php'],
      },
      main: {
        src: ['lists/index.php', 'lists/list.php', 'lists/message.php', 'lists/reports.php', 'lists/summary.php'],
      },
    },
    cssmin: {
      target: {
        src: ['lists/css/application.css'],
        dest: 'lists/css/application.min.css',
      }
    },
    concat: {
      css: {
        src: ['lists/css/bootstrap.css','lists/css/font-awesome.min.css','lists/css/simple-sidebar.css','lists/css/lists.css'],
        dest: 'lists/css/application.css',
      },
      vendor: {
        options: {
          separator: ';',
        },
        src: ['lists/js/vendor/jquery.js','lists/js/vendor/bootstrap.js','lists/js/vendor/jquery.storageapi.min.js', 'lists/js/vendor/jquery.mobile-events.js',
              'lists/js/vendor/jquery.chained.js','lists/js/vendor/jquery.tinysort.min.js', 'lists/js/vendor/detectmobilebrowser.js'],
        dest: 'lists/js/partials/_vendor.js',
      },
      lists: {
        options: {
          separator: ';',
        },
        src: ['lists/js/partials/_vendor.js','lists/js/partials/_common.js', 'lists/js/partials/_lists.js'],
        dest: 'lists/js/lists.min.js',
      },
      message: {
        options: {
          separator: ';',
        },
        src: ['lists/js/partials/_vendor.js','lists/js/partials/_common.js','lists/js/partials/_message.js'],
        dest: 'lists/js/message.min.js',
      },
      reports: {
        options: {
          separator: ';',
        },
        src: ['lists/js/partials/_vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_reports.js'],
        dest: 'lists/js/reports.min.js',
      },
      settings: {
        options: {
          separator: ';',
        },
        src: ['lists/js/partials/_vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_settings.js'],
        dest: 'lists/js/settings.min.js',
      },
      summary: {
        options: {
          separator: ';',
        },
        src: ['lists/js/partials/_vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_summary.js'],
        dest: 'lists/js/summary.min.js',
      },
      admin: {
        options: {
          separator: ';',
        },
        src: ['lists/js/partials/_vendor.js', 'lists/js/partials/_common.js', 'lists/js/partials/_admin.js'],
        dest: 'lists/js//admin.min.js',
      },
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
          'lists/js/admin.min.js': ['lists/js/admin.min.js']
        }
      },
      lists: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/lists.min.js': ['lists/js/lists.min.js']
        }
      },
      message: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/message.min.js': ['lists/js/message.min.js']
        }
      },
      reports: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/reports.min.js': ['lists/js/reports.min.js']
        }
      },
      settings: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/settings.min.js': ['lists/js/settings.min.js']
        }
      },
      summary: {
        options: {
          mangle: false,
          preserveComments: false
        },
        files: {
          'lists/js/summary.min.js': ['lists/js/summary.min.js']
        }
      },
    },
    watch: {
      grunt: {
        files: ['Gruntfile.js'],
      },
      admin: {
        files: ['lists/js/partials/_admin.js'],
        tasks: ['jshint:admin','concat:admin', 'uglify:admin','manifest'],
      },
      api: {
        files: ['lists/api/index.php'],
        tasks: ['phplint','manifest'],
      },
      common: {
        files: ['lists/js/partials/_common.js'],
        tasks: ['jshint:common', 'concat', 'uglify', 'manifest'],
      },
      css: {
        files: ['lists/css/lists.css', 'lists/css/simple-sidebar.css'],
        tasks: ['csslint','concat:css', 'cssmin','manifest'],
      },
      html: {
        files: ['lists/*.html'],
        tasks: ['manifest'],
      },
      lists: {
        files: ['lists/js/partials/_lists.js'],
        tasks: ['jshint:lists','concat:lists', 'uglify:lists','manifest'],
      },
      message: {
        files: ['lists/js/partials/_message.js'],
        tasks: ['jshint:message','concat:message', 'uglify:message','manifest'],
      },
      reports: {
        files: ['lists/js/partials/_reports.js'],
        tasks: ['jshint:reports','concat:reports', 'uglify:reports','manifest'],
      },
      settings: {
        files: ['lists/js/partials/_settings.js'],
        tasks: ['jshint:settings','concat:settings', 'uglify:settings','manifest'],
      },
      summary: {
        files: ['lists/js/partials/_summary.js'],
        tasks: ['jshint:summary','concat:summary', 'uglify:summary','manifest'],
      },
      vendor: {
        files: ['lists/js/vendor/*.js'],
        tasks: ['concat:vendor','concat', 'uglify', 'manifest'],
      },
      mainPHP: {
        files: ['lists/admin.php','lists/index.php', 'lists/list.php', 'lists/message.php', 'lists/reports.php', 'lists/summary.php'],
        tasks: ['phplint:main', 'manifest'],
      },
      version: {
        files: ['lists/lists.version'],
        tasks: ['manifest'],
      },
    },
  });


  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-csslint');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks("grunt-phplint");
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-notify');
  grunt.loadNpmTasks('grunt-manifest');
  // Tasks
  grunt.registerTask('default', ['csslint','jshint','concat','uglify','cssmin','phplint', 'manifest']);

};