module.exports = function(grunt) {

  // Project configuration
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // JSHint Gruntfile.js and any of the OvR Lists javascripts
    jshint: {
      files: ['Gruntfile.js', 'package.json', 'lists/assets/javascripts/lists.js'],
    },
    
    // Concatenate all OvR Lists javascripts except all.js
    concat: {
      options: {
        separator: ';',
      },
      dist: {
        src: ['lists/assets/javascripts/jquery.js', 'lists/assets/javascripts/bootstrap.js', 'lists/assets/javascripts/lists.js','lists/assets/tablesorter/js/jquery.tablesorter.min.js','lists/assets/tablesorter/js/jquery.tablesorter.widgets.min.js','lists/assets/tablesorter/js/widgets/widget-editable.js'],
        dest: 'lists/assets/javascripts/all.js',
      },
    },

    // Compile Sass to CSS -  destination : source
    // TODO: Add a banner with message DO NOT MODIFY css file
    // TODO: Add task to lint lists.scss
    sass: {
      compile: {
        options: {
          style: 'compressed',
          banner: '/*!!!!!!!!!!!!!!DO NOT MAKE CHANGES TO THIS FILE. THIS FILE IS COMPILED FROM CSS AND SCSS FILES IN THIS FOLDER, CHANGES WILL BE OVER WRITTEN  !!!!!!!!!!!!!!!!!!!\n! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
        },
        files: {
          'lists/assets/stylesheets/all.css': 'lists/assets/stylesheets/lists.scss',
        },
      },
    },

    // Minify javascript with Uglify
    uglify: {
      options: {
        banner: '/*!!!!!!!!!!!!!!DO NOT MAKE CHANGES TO THIS FILE. CHANGES ARE CONCATENATED AND MINIFIED USING GRUNT.JS !!!!!!!!!!!!!!!!!!!\n! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
        footer: '\n/*!!!!!!!!!!!!!!ANY CHANGES MADE TO THIS FILE WILL BE OVER WRITTEN, MAKE CHANGES TO SOURCE JAVASCRIPT FILES AND COMPILE WITH GRUNT JS !!!!!!!!!!!!!!*/',
        mangle: false,
      },
      build: {
        src: 'lists/assets/javascripts/all.js',
        dest: 'lists/assets/javascripts/all.min.js',
      },
    },

    // Simple config to run sass, jshint and uglify any time a js or sass file is added, modified or deleted
    watch: {
      sass: {
        files: ['lists/assets/stylesheets/lists.scss'],
        tasks: ['sass'],
      },
      jshint: {
        files: ['<%= jshint.files %>'],
        tasks: ['jshint'],
      },
      concat: {
        files : ['<%= concat.dist.src %>'],
        tasks: ['concat'],
      },
      uglify: {
        files: ['lists/assets/javascripts/lists.js'],
        tasks: ['uglify'],
      },
    },
  });

  // Load the plug-ins
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default tasks
  grunt.registerTask('default', ['jshint', 'concat', 'sass', 'uglify']);

};