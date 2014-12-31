module.exports = function(grunt){
  
  // Project configuration.
  grunt.initConfig({
    pkg: gruntfile.readJSON('package.json'),

  });


  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-uglify');
  
  // Tasks
  grunt.registerTask('default', ['uglify']);

};