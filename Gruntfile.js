module.exports = function(grunt) {

  // Project configuration
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // JSHint Gruntfile.js and any of the OvR Lists javascripts
    jshint: {
      files: ['Gruntfile.js', 'lists/assets/javascripts/lists.js'],
    },
    
    // Concatenate all OvR Lists javascripts except all.js
    concat: {
      options: {
        separator: ';',
      },
      dist: {
        src: ['lists/assets/javascripts/**/*.js', '!lists/assets/javascripts/all.js'],
        dest: 'lists/assets/javascripts/all.js',
      },
    },

    // Make the javascript ugly with Uglify
    // TODO: Add a banner with message DO NOT MODIFY js file
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
      },
      build: {
        src: 'lists/assets/stylesheets/all.js',
        dest: 'lists/assets/stylesheets/all.min.js',
      },
    },

    // Compile Sass to CSS -  destination : source
    // TODO: Add a banner with message DO NOT MODIFY css file
    // TODO: Add task to lint lists.scss
    sass: {
      dist: {
        files: {
          'lists/assets/stylesheets/all.css': 'lists/assets/stylesheets/lists.scss',
        },
      },
    },

    // Simple config to run jshint and sass any time a js or sass file is added, modified or deleted
    watch: {
      sass: {
        files: ['lists/assets/stylesheets/lists.scss'],
        tasks: ['sass'],
      },
      jshint: {
        files: ['<%= jshint.files %>'],
        tasks: ['jshint'],
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