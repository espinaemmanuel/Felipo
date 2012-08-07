Felipo
======

Felipo is a web development framework for rapid development

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
