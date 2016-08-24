This is a simple Web application that uses Silex Microframework, composer and MariaDB/MySQL database

To know more about Silex see: http://silex.sensiolabs.org/

For Apache and Nginx settings for Silex see: http://silex.sensiolabs.org/doc/master/web_servers.html

For Composer see: https://getcomposer.org/

Note: You will need to install composer and run it on root folder to get Silex and to application to work.
Also you need PHP MySQL PDO driver for database connection. Some path settings can
be unix dependable


'scripts/create.sql' has SQL statements to create required database + tables

'settings/settings.php' has the database settings user, password and host.
Change these to your local settings

'scripts/parsetodb.php' will import from 'sample-data.csv' to database
Just use from console: php 'scripts/parsetodb.php'

If webserver is configured to read index.php and has rewrite engine configured  
application should work after creating database + setting database settings.

From domain root you have configured for the application

