## OvRride.com

OvRride.com is built using WordPress 3.5.2, and the WooCommerce plugin. The OvRride theme is based off of the [Quark Starter Theme](http://quarktheme.com/).


### Dependancies:

[normalize.css v2.1.2](git.io/normalize)  
[Font Awesome v3.2.1](http://fortawesome.github.io/Font-Awesome/)  
[Quark Starter Theme v1.2.4](https://github.com/maddisondesigns/Quark)  

### Running a local development copy of ovrride.com on Mac OS X:

1. Download and install [MAMP](http://www.mamp.info/en/index.html)

2. Configure MAMP Preferences: ports: PHP = port 80, MySQL = port 3306 and Document Root = /path_to/public_html

3. Export and download a copy of the remote database in SQL format from cPanel phpMyAdmin.

4. Create a local database and database user with the values in wp-config.php using [phpMyAdmin](http://localhost/MAMP/?language=English)

5. Run [these](https://gist.github.com/AJ-Acevedo/0b09bedc776895fb6f93) SQL queries on the new local database:

6. Download a local copy of the OvRride codebase from bluehost. Here are two options:

    Transfer the contents of the remote directory `public_html` to a local directory  
    `rsync -avz -e ssh ovrridec@ovrride.com:public_html/ ~/public_html`

    or  

    Transfer the directory `public_html` to your home directory  
    `rsync -avz -e ssh ovrridec@ovrride.com:public_html/ ~`

    And there's always the sloooooow way using sftp.

**NOTE:** The .htaccess file will cause issue with a local install. Comment out every line outside of the BEGIN and END WordPress comments. Please DO NOT commit your local version of the .htaccess file to the repo.

### OvR Lists:

OvR Lists can be accessed via http://list.ovrride.com. The files are located in the lists directory found in the root of this repo.

Authentication is currently using .htpasswd, This will have to eventually change.

### OvR Lists Dependancies:

[Color Me Sass v1.3](http://www.richbray.me/cms/)  
[Bootstrap Sass v3.0.0](https://github.com/jlong/sass-bootstrap)  
[Grunt JS v0.4.1](http://gruntjs.com)  
[jQuery 1.10.2](http://jquery.com)  

Installing and getting Grunt.js configured can be pretty daunting. [Here is a great write-up](http://blog.raddevon.com/becoming-self-sufficient-with-grunt-js/)

Once you have node, npm and grunt installed. You can run grunt tasks from the projects root.

`grunt` from the CLI with run the default tasks specified in `Gruntfile.js`

`grunt watch` from the CLI will watch the sass and javascript files for changes and automatically update all.min.js and all.css
