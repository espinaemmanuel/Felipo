Felipo
======

Felipo is a web development framework for rapid development


In my opinion when you have done a project that you like and you wouldn’t like to let it die, forgotten in some server in Slovakia, you open source it. That is exactly what I did with Felipo (editor note: Felipo is the name of the author’s cat).

[https://github.com/espinaemmanuel/Felipo](https://github.com/espinaemmanuel/Felipo)

If you see some similarities with other frameworks you are right. Felipo is strongly inspired in Ruby on Rails but written in PHP. Anyone interested in extending it will be welcomed. Just send me a mail and if you want to contribute your changes back create a pull request in Github. It is licensed under Apache so there are little restrictions on what you can do with it.

I will very briefly summarize some of its characteristics. You will notice that some parts are in Spanish and some in English. That’s because it started as a project in Spanish, not as framework and with the time it evolved into an independent piece of software. The language for new additions will always be English.

And finally you’ll notice the lack of unit tests in this project. Well that is bad and I know it. Again, anyone can add them now that it is open source.


## Front controller architecture based on modules


The architecture is basically the same as many web frameworks and is based on the design [proposed](http://martinfowler.com/eaaCatalog/frontController.html) by Martin Fowler. The idea, essentially, is to separate the view from the controllers. All the request are redirected via mod_rewrite to the same php script (front_controller.php) that parses the request. This, based on the URI string, redirects the request to the right controller. This redirection is based on a convention in the url:

http://<base_domain>/module/controller/action?params

The system is organized in modules. For example one module can be the admin interface and another the front end. Each module corresponds to a directory in the apps directory. In the pageControllers directory inside each module you find the modules that are classes, and the actions are methods in those classes.


## Multiple configuration environments


You can define a configuration for production, development, testing, etc. One environment will be selected based on different rules, currently there is a default one and another that is chosen based on the HTTP request "host" header (it is assumed that the production environment will have the real domain as the value for this header, and dev will have “localhost”). Based on that, the right config file is loaded.


## Extensible via plugins


The initial idea was to make this system easy to extend. A plugin is a folder in the include directory. Among the plugins currently in the system it’s worth noticing the database plugin that implements the Active Record pattern and the REST plugin that adds controllers to expose the Active Record via a REST interface.

The plugins are loaded selectively according to the $config ['plugins'] values in the configuration file.


## Database connections via Active Record


Felipo implements a very lightweight version of active record. For example to save a person in a database:

```php
class Person extends ActiveRecord {}
$person = new Person();
$person->id = 123;
$person->name = “Emmanuel”;
$person->lastName = “Espina”;
$person->save();
```
This will execute in the database:

```sql
INSERT INTO Person (id, name, lastName) VALUES (123, 'Emmanuel', 'Espina');
```
As you can see it is very simple (in simple cases). To load the person you do:

```php    
$person = Person::loadById(123);
```

The active records go in the models directory in each module.


## Easy REST resources


Now that you have a Person represented as an active record you can expose it to the world with a ActiveRecordResource

```php    
class Person extends ActiveRecordResource {}
```

That goes to the resources directory. Currently for this to work you must have a corresponding active record in the models directory with the name Person_AR (I’ll fix this in the future)
Now if you want to get the person you create a rest controller inheriting from **RestControllerGenerico**

And send it a request like:

    
    http://<base_domain>/module/rest/index/Person/123


And you will get the person formatted as a JSON object.


## What else?


To keep this post short I didn’t include elements like Authenticators (there are LDAP and Mysql based authentication modules). Also the login and session management is already included as another plugin.

Finally there is a set of validators and html form generators that take a specification of a form (as a JSON object) and creates the html and then can create validators on run time to test if it passes simple validations.

You can investigate all of these features reading the code (and documenting it if you want :) )

The interesting thing about this framework is that it is small, and one of the main design decision was to make it fully modular by the use of plugins. Almost anything is a plugin even the database connectivity. This should keep the system simple enough for anyone to understand it and extend it relatively easy.


Requirements
------------

* Apache HTTP Server 2
* PHP 5

In Ubuntu you can install the required packages with

`sudo apt-get install apache2 php5`

Getting started
---------------

Clone the git repository

	git clone https://github.com/espinaemmanuel/Felipo.git

Create the logs directory

	mkdir Felipo/apache-logs

Create a site in the Apache Server

	cd /etc/apache2/sites-available/
	gedit felipo

Add the following configuration specifying the correct path for the directories

    <VirtualHost *:80>
            ServerName  felipo
    
            DocumentRoot /home/emmanuel/Felipo

            ErrorLog  /home/emmanuel/Felipo/apache-logs/error.log
            CustomLog /home/emmanuel/Felipo/apache-logs/access.log combined
    </VirtualHost>
    
Enable the site with the following commands

	sudo a2ensite felipo
	sudo service apache2 reload

To access the new site you will need to add "felipo" to the hosts file. For example

	sudo gedit /etc/hosts

And edit it so it looks like

	127.0.0.1	localhost
	127.0.0.1	felipo

You can now open a browser and go to http://felipo and see the example page
