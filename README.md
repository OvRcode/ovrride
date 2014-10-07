TODO: Readme is not up to date. Setting up new Dev Environment then updating readme
## OvRride.com

OvRride.com is built using WordPress 3.5.2, and the WooCommerce plugin. The OvRride theme is based off of the [Quark Starter Theme](http://quarktheme.com/).


### Dependancies:

[normalize.css v2.1.2](git.io/normalize)  
[Font Awesome v3.2.1](http://fortawesome.github.io/Font-Awesome/)  
[Quark Starter Theme v1.2.4](https://github.com/maddisondesigns/Quark)  

### Running a local development copy of ovrride.com on Mac OS X:

1. Download and install [MAMP](http://www.mamp.info/en/index.html)

2. Configure MAMP Preferences:  
      Ports: PHP = port 80, MySQL = port 3306  
      Document Root = /path_to/public_html  
      [Increase MAMP's php.ini memory limits:](http://blog-en.mamp.info/2009/09/increase-php-memory-limit-with-mamp-pro.html)  
      - post_max_size = 256M  
      - memory_limit = 256M   
      - upload_max_filesize = 256M  
      - max_input_vars = 5000
      
3. Export and download a copy of the remote database in SQL format from cPanel phpMyAdmin.

4. Create a local database and database user with the values in wp-config.php using [phpMyAdmin](http://***REMOVED***/MAMP/?language=English)

5. Run [these](https://gist.github.com/AJ-Acevedo/0b09bedc776895fb6f93) SQL queries on the new local database:

6. Download a local copy of the OvRride codebase from bluehost. Here are two options:

    Transfer the contents of the remote directory `public_html` to a local directory  
    `rsync -avz -e ssh ovrridec@ovrride.com:public_html/ ~/public_html`

    or  

    Transfer the directory `public_html` to your home directory  
    `rsync -avz -e ssh ovrridec@ovrride.com:public_html/ ~`

    And there's always sftp.


**NOTES:**

- The .htaccess file may cause issues with a local install. Comment out every line outside of the BEGIN and END WordPress comments. Please DO NOT commit your local version of the .htaccess file to the repo.

- If you'll need to test the Payment Gateway, make sure to (Enable PayPal Sandbox/Test Mode](http://docs.woothemes.com/document/paypal-pro/)

- If your development environment is not setup to use SSL, comment out the 'Force Login page and Admin Dashboard to require SSL' options in wp-config.php. Should be lines 84 and 85.

### OvR Lists:

OvR Lists can be accessed via http://lists.ovrride.com.  
The files are located in the lists directory found in the root of this repo.

Authentication is currently using .htpasswd, This will be changed to utilize [php-login.net](http://php-login.net)

### OvR Lists Dependancies:

[Color Me Sass v1.3](http://www.richbray.me/cms/)  
[Bootstrap Sass v3.0.2](https://github.com/jlong/sass-bootstrap)  
[Grunt JS v0.4.1](http://gruntjs.com)  
[jQuery 1.10.2](http://jquery.com)  
[tablesorter v2.14.0](https://github.com/Mottie/tablesorter)  
[We are using a customized version php-login.net - 1-minimal version from September 21st 2013](https://github.com/panique/php-login)

Installing and getting Grunt.js configured can be pretty daunting. Check out the [Getting Started](http://gruntjs.com/getting-started) and [here is a great write-up](http://blog.raddevon.com/becoming-self-sufficient-with-grunt-js/)

Once you have node, npm and grunt installed. You can run grunt tasks from the projects root.

`grunt` from the CLI will run the default tasks specified in `Gruntfile.js`

`grunt watch` from the CLI will watch the Sass and JavaScript files for changes and automatically update all.min.js and all.css
