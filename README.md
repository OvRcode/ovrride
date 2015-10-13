## OvRride.com

OvRride.com is built using WordPress 4.3.1, and the WooCommerce plugin. The OvRride theme is based off of the [Quark Starter Theme](http://quarktheme.com/).


### Dependancies:

[normalize.css v2.1.2](git.io/normalize)  
[Font Awesome v3.2.1](http://fortawesome.github.io/Font-Awesome/)  
[Quark Starter Theme v1.3.1](https://github.com/maddisondesigns/Quark)  

### Running a local development copy of ovrride.com on Mac OS X:

1. Download and install:
  - [Homebrew](http://brew.sh)
     - package manager for OS X, this will make installing some programs easier
     - used by RVM to install versions of ruby
  - [RVM](http://rvm.io) - Ruby Version Manager
    - once rvm is installed ```rvm install 2.1```
  - [Chef](https://downloads.getchef.com/chef-dk/mac/#/)
  - [Vagrant](http://vagrantup.com) - Virtual machine manager
  - Your choice of hypervisor:
    - [VMware Fusion 7](http://store.vmware.com/store/vmware/en_US/DisplayProductDetailsPage/ThemeID.2485600/productID.304322400)
    - [VirtualBox](http://virtualbox.org)
  - Kife solo gem - part of chef solo which is used to configure the virtual machine the development environment runs on
  - librian-chef - helps manage chef cookbooks
  - Both can be installed by running ```bundle install```
  - vagrant-hostmanager - lets vagrant write to /etc/hosts
    - ```vagrant plugin install vagrant-hostmanager```
  - vagrant-omnibus
    - ``` vagrant plugin install vagrant-omnibus```
2. Clone repository to your machine
  - if you have a copy of the wp-content/uploads directory then copy it to the correct location
  - if you don't it could take up to 40min to download from amazon, it is automated by chef during the vagrant up/provision process
3. Add the 'secret_keys' data bag to the ```/data_bags/``` directory
  - This contains keys for Amazon S3 access and Twilio
  - If you are working with OvR contact devops@ovrride.com for a copy of this file
  - If you are not working with OvR, sorry guys I can't leave access keys for this stuff in the open.
    - There is an example version commited that just needs some keys filled in
4. install vmware fusion provider
  - ```vagrant plugin install vagrant-vmware-fusion```
  - install licence for plugin: ```vagrant plugin license vagrant-vmware-fusion ~/license.lic```
  - check plugin installation: ```vagrant plugin list```
5. Start virtual machine
  - ```vagrant up``` this needs to be run in terminal from inside the project folder
  - Grab a cold beverage, this is going to spin up 4 VMs and will probably take at lease a few minutes

  - The VM looks at the project directory for files so changes will show up immediately
  - checkout http://local.ovrride.com to see your local copy of the site
6. After the web servers have been provisioned make sure to add ```local.ovrride.com.crt``` to your keychain file
  - This will help avoid SSL errors due to self signed certificate for development
  - It is recommended to set trust setting to "Always Trust"


**Architecture**

- All hosts are running ubuntu server 14.04 64bit

- There are 5 hosts that make up the web site
    
   1) haproxy.local.ovrride.com
      
   - HAPROXY load balancer
   
   - SSL Connections are terminated at proxy
	  
  2) web1.local.ovrride.com
  
  - web server
  
  - NGINX 1.4.6, with HHVM and fastcgi/php5-fpm fallback
	  
  3) web2.local.ovrride.com
      
  - same as web1
	  
  4) mysql.local.ovrride.com
      
  - mysql 5.5

  5) lists.local.ovrride.com
  
  - web server
  
  - APACHE MPM-Worker + PHP5-FPM
  
  - Needs to be switched over to nginx setup that normal web servers run
  
  - Hosts files for lists web app
  
  

**Vagrant Management**
- running ```vagrant up```, ```vagrant provision```, ```vagrant halt``` will effect ALL hosts
- you can add the name of any box after a normal vagrant command to apply to one box only ```vagrant provision web1```
- check the [vagrant docs](http://docs.vagrantup.com/v2/multi-machine/) for more details

**NOTES:**
- Email is setup on a development key, errors will no be generated but emails will not be sent from the dev system
- If you'll need to test the Payment Gateway, make sure to (Enable PayPal Sandbox/Test Mode](http://docs.woothemes.com/document/paypal-pro/)

### OvR Lists:

OvR Lists can be accessed via http://lists.ovrride.com.  
The files are located in the lists directory found in the root of this repo.

Authentication is utilizing [php-login.net](http://php-login.net)

### OvR Lists Dependancies:

[Bootstrap CSS v3.3.0](http://getbootstrap.com/)  
[Grunt JS v0.4.2](http://gruntjs.com)  
[jQuery v1.11.1](http://jquery.com)  
[jQuery Mobile v1.4.5](http://jquerymobile.com/)
[jQuery Detect Mobile Browser](http://detectmobilebrowser.com/)
[jQuery Storage API v1.7.3](https://github.com/julien-maurel/jQuery-Storage-API)
[jQuery Tiny Sort v1.5.6](http://tinysort.sjeiti.com/)
[jQuery Chained v0.9.8](http://www.appelsiini.net/projects/chained)
[Flight PHP v1.1.10](http://http://flightphp.com/)


Installing and getting Grunt.js configured can be pretty daunting. Check out the [Getting Started](http://gruntjs.com/getting-started) and [here is a great write-up](http://blog.raddevon.com/becoming-self-sufficient-with-grunt-js/)

Once you have node, npm and grunt installed. You can run grunt tasks from the projects root.

`grunt` from the CLI will run the default tasks specified in `Gruntfile.js`

`grunt watch` from the CLI will watch the Sass and JavaScript files for changes and automatically update all.min.js and all.css
