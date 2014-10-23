## OvRride.com

OvRride.com is built using WordPress 3.5.2, and the WooCommerce plugin. The OvRride theme is based off of the [Quark Starter Theme](http://quarktheme.com/).


### Dependancies:

[normalize.css v2.1.2](git.io/normalize)  
[Font Awesome v3.2.1](http://fortawesome.github.io/Font-Awesome/)  
[Quark Starter Theme v1.2.4](https://github.com/maddisondesigns/Quark)  

### Running a local development copy of ovrride.com on Mac OS X:

1. Download and install:
  - [Homebrew](http://brew.sh)
     - package manager for OS X, this will make installing some programs easier
  - [RVM](http://rvm.io) - Ruby Version Manager
  - [Vagrant](http://vagrantup.com) - Virtual machine manager
  - [Virtual Box](https://www.virtualbox.org/wiki/Downloads)
  - Kife solo gem - part of chef solo which is used to configure the virtual machine the development environment runs on
    - ```gem install knife-solo --no-ri --no-rdoc```
  - librian-chef - helps manage chef cookbooks
    - ```gem install librarian-chef —-no-ri —-no-rdoc```
  - vagrant-hostmanager - lets vagrant write to /etc/hosts
    - ```vagrant plugin install vagrant-hostmanager```
  - s3cmd - allows commandline access to amazon web services
  	- ```brew install s3cmd```
  
2. Download a copy of the database
  - options:
    - log into database server and export through commandline
	- use s3cmd to get a copy from the backup bucket
	  - ```s3cmd get S3://ovrdatabase/latest/ovrride*```
	  - uncompress and rename to ovrride.sql
3. Start virtual machine
  - ```vagrant up``` this needs to be run in terminal from inside the project folder
  - The VM looks at the project directory for files so changes will show up immediatley
  - checkout http://local.ovrride.com to see your local copy of the site

####SSL isn't working yet
Haven't setup SSL yet in dev env
**NOTES:**

- If you'll need to test the Payment Gateway, make sure to (Enable PayPal Sandbox/Test Mode](http://docs.woothemes.com/document/paypal-pro/)

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
