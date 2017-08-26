# Parameters

The parameters relative to your installation is stored in the app/config/parameters.yml file. 
This is the explanation of each parameter you can find in this file.

```
NOTE : At the first installation of Kentel, when you execute the
 composer.phar update, composer ask you about this parameters 
 and update them. If you decide to change your setting you'll 
 have to manually change this parameters.
```


* database_driver : it's the driver you'll use to access to your database. By default you'll have pdo_mysql but you could use pdo_postgres by exemple.
* database_host : the hostname or IP of your database server.
* database_port : the port at which your database server can be reach.
* database_name : the database where kentel will store this tables. Attention, you have to create it BEFORE the Kentel installation and grant all access to the user defined after.
* database_user : the username which has been granted on the database.
* database_password : the password of your user granted on the database.
* locale : used to set the default language. By default en for english. 
* secret : random chain of characters used for security operations inside the application (like the generation of csrf_token). Recommanded length is 32 characters, and you should modify with your own sequence.
* debug_toolbar : true or false, to obtain the debug toolbar in the dev mode.
* debug_redirects : To intercept redirects in the dev mode and so be able to debug.
* use_assetic_controller : specify true or false to use or not the assetic controller to publish your web assets.
* ldap_enabled : true or false to enable the ldap authentication.
* ldap_user : the user you used to connect to your ldap for you users authentication.
* ldap_password : the password of the user listed up.
* ldap_host : the hostname or IP adress of your LDAP/AD server.
* ldap_port : the TCP port on which the LDAP server is listenning.
* ldap_baseDn : the baseDN of the users you'll have to retrieve in the LDAP/AD.
* ldap_ssl : true or false, if the connection is SSL protected or not. Adapt the ldap_port in accordance with this parameter.
* mail_user : the user used to connect to your email server. Kentel must have the capacity to send email for notifications and so you have to declare your mail server parameters.
* mail_password : the password of your email user.
* mail_transport : smtp usually.
* mail_auth : if your mail server need authentication then indicate login otherwise null.
* mail_host : the server hostname or IP adress of your email server.
* mailer_user : this parameter is indeed by the fos_user plugin. Specify the username used to send mail to manage accounts (retrieve password, new registration...).
* domain_constraint : specify a domain name if you want to restrict user in the regristation process to use email inside this domain only (null otherwise).
* google_api_key: specify your Google API. Used to access to google map.