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
        src: ['lists/assets/javascripts/jquery.js', 'lists/assets/javascripts/bootstrap.js', 'lists/assets/javascripts/lists.js'],
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
        },
        files: {
          'lists/assets/stylesheets/all.css': 'lists/assets/stylesheets/lists.scss',
        },
      },
    },

    // Minify javascript with Uglify
    // TODO: Add a banner with message DO NOT MODIFY js file
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
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