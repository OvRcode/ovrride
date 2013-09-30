## OvRride.com

The OvRride Theme is based off of the [Quark Starter Theme](http://quarktheme.com/).


### Dependancies:

[normalize.css v2.1.2](git.io/normalize)  
[Font Awesome v3.2.1](http://fortawesome.github.io/Font-Awesome/)  
[Quark Starter Theme v1.2.4](https://github.com/maddisondesigns/Quark)  

### Running a local development copy of ovrride.com on Mac OS X:

1. Download and install [MAMP](http://www.mamp.info/en/index.html)

2. Configure MAMP Preferences: ports: PHP = port 80, MySQL = port 3306 and Document Root = /path_to/public_html

3. Export and download a copy of the database in SQL format from cPanel phpMyAdmin.

4. Create a database and database user with the values in wp-config.php using [phpMyAdmin](http://***REMOVED***/MAMP/?language=English)

5. Run [these](https://gist.github.com/AJ-Acevedo/0b09bedc776895fb6f93) SQL queries on the new local database:

6. Download a local copy of the OvRride codebase from bluehost. Here are two options:

    Transfer the contents of the remote directory `public_html` to a local directory  
    `rsync -avz -e ssh ovrridec@ovrride.com:public_html/ ~/public_html`

    or  

    Transfer the directory `public_html` to your home directory  
    `rsync -avz -e ssh ovrridec@ovrride.com:public_html/ ~`  
