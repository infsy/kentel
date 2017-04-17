![](./Kentel-petit-H-RGB.jpg)

# Kentel

Web application to manage your employees and their assignments. Crafted initially for a professional services company.

## Requisites

To run this application, you need PHP 5.6.x, MySQL or MariaDB and a Web server like Apache.

## How to install it

Clone this GIT depot 

`git clone https://github.com/infsy/Kentel.git
`

In the Kentel directory, update composer.phar

`./composer.phar selfupdate
`


Then update the application itself :

`./composer.phar update
`

This will download all the plugins needed by the application and prepare the web directory. At the end of the update, it will ask you some parameters regarding your LDAP directory for authentication, you mail server... You'll need for the Geolocation function a Google API you can obtain [here](https://developers.google.com/maps/documentation/javascript/get-api-key?hl=Fr).


Don't forget to create your database with the parameters you filled in the parameters.yml then create tables :

`php app/console doctrine:schema:update --force
`

Then, you just to configure your Web server to load the Web directory and that's it !