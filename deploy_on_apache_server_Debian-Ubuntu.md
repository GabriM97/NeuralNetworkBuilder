# Deploy APACHE SERVER on Debian/Ubuntu systems. <br>

**IMPORTANT NOTES:** <br>
- If are you running an Operating Systems with SELinux (or others security modules): <br>
**SELINUX WILL CAUSE A LOT OF PROBLEMS. DISABLE IT!** <br>
_(the following command temporary disable SELinux, it will be up again at next restart)_ <br>
#> `sudo setenforce 0` <br>

------------------------------------------ <br>

- Install PHP and apache: <br>
#> `sudo apt install apache2 php libapache2-mod-php php-mysql php-mbstring php-gettext` <br>


- Install phpMyAdmin (during installation it will ask if you want to use "Apache2" or "Lighttpd", select "Apache2"): <br>
#> `sudo apt install phpmyadmin` <br>
#> `sudo phpenmod mbstring` <br>


- Enable and start apache2 service: <br>
#> `sudo systemctl start apache2` <br>
#> `sudo systemctl enable apache2` <br>


- Set User and Server permission:<br>
#> `sudo chown YOUR-USERNAME:www-data -R /PATH/TO/REPO/NeuralNetworkBuilder/website` <br>
#> `sudo chmod -R 775 /PATH/TO/REPO/NeuralNetworkBuilder/website` <br>
#> `sudo usermod www-data --append --groups YOUR-USERNAME` <br>
#> `sudo usermod YOUR-USERNAME --append --groups www-data` <br>


- If `sites-available/` and `sites-enabled/` directories doesn't exists, than create it:  <br>
#> `sudo mkdir /etc/apache2/sites-available` <br>
#> `sudo mkdir /etc/apache/sites-enabled` <br>


- Create a config file for laravel:  <br>
Into `utils/apache_conf/` folder you will find a confing file named `neuralnetworkbuilder.conf`. <br>
Copy the config file and Paste it into `/etc/apache2/sites-available/`. <br>
Edit now the config file with your properly data.<br>


- **OPTIONAL CHANGES.** Add the virtual host `neuralnetworkbuilder`: <br>
#> `echo "127.0.0.1 neuralnetworkbuilder" >> /etc/hosts` <br>


- Create a `neuralnetworkbuilder.conf` softlink from `sites-available/` to `sites-enabled/` directory: <br>
#> `sudo a2ensite neuralnetworkbuilder.conf` <br>


- Disable default config (remove softlink from `sites-enabled`): <br>
#> `sudo a2dissite 000-default.conf` <br>


- Add the following line at the end of `/etc/apache2/apache2.conf` file: <br>
#> sudo echo "IncludeOptional sites-enabled/*.conf" >> /etc/apache2/apache2.conf <br>


- Enable "rewrite" module: <br>
#> `sudo a2enmod rewrite` <br>


- Add the server port to `/etc/apache2/ports.conf`: <br>
#> `sudo echo "Listen 8080" >> /etc/apache2/ports.conf` <br>


- Test if apache config it's ok: <br>
#> `apachectl configtest` <br>
If the command output shows "`Syntax OK`", then you setting up everythings okay. <br>


- Reload/Restart apache server: <br>
#> `sudo systemctl reload apache2.service` <br>
#> `sudo systemctl restart apache2.service` <br>
