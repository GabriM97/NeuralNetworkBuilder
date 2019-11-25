# Deploy APACHE SERVER on Fedora/CentOS systems. <br>

**IMPORTANT NOTES:** <br>
- If are you running an Operating Systems with SELinux (or others security modules): <br>
**SELINUX WILL CAUSE A LOT OF PROBLEMS. DISABLE IT!** <br>
_(the following command temporary disable SELinux, it will be up again at next restart)_ <br>
#> `sudo setenforce 0` <br>

------------------------------------------ <br>

- Install PHP and httpd (apache): <br>
#> `sudo dnf -y install httpd php php-cli php-php-gettext php-mbstring php-mcrypt php-mysqlnd php-pear php-curl php-gd php-xml php-bcmath php-zip` <br>


- Install phpMyAdmin: <br>
#> `sudo dnf -y install phpMyAdmin` <br>


- Enable and start httpd service: <br>
#> `sudo systemctl start httpd` <br>
#> `sudo systemctl enable httpd` <br>


- Set User and Server permission:<br>
#> `sudo chown YOUR-USERNAME:apache -R /path/to/repo/NeuralNetworkBuilder/website` <br>
#> `sudo chmod -R 775 /path/to/repo/NeuralNetworkBuilder/website` <br>
#> `sudo usermod apache --append --groups YOUR-USERNAME` <br>
#> `sudo usermod YOUR-USERNAME --append --groups apache` <br>


- If `sites-available/` and `sites-enabled/` directories doesn't exists, than create it:  <br>
#> `sudo mkdir /etc/httpd/sites-available` <br>
#> `sudo mkdir /etc/httpd/sites-enabled` <br>


- Create a config file for laravel:  <br>
Into `utils/apache_conf/` folder you will find a confing file named `neuralnetworkbuilder.conf`. <br>
Copy the config file and Paste it into `/etc/httpd/sites-available/`. <br>
Edit now the config file with your properly data. <br>


- **OPTIONAL CHANGES.** Add the virtual host `neuralnetworkbuilder`: <br>
#> `echo "127.0.0.1 neuralnetworkbuilder" >> /etc/hosts` <br>


- Create a `neuralnetworkbuilder.conf` softlink from `sites-available/` to `sites-enabled/` directory: <br>
#> `ln -s /etc/httpd/sites-available/neuralnetworkbuilder.conf /etc/httpd/sites-enabled/neuralnetworkbuilder.conf` <br>

- Add the following line at the end of `/etc/httpd/conf/httpd.conf` file: <br>
  ``` IncludeOptional sites-enabled/*.conf ``` <br>


- Test if apache config it's ok: <br>
#> `apachectl configtest` <br>
If the command output shows "`Syntax OK`", then you setting up everythings okay. <br>

- Reload/Restart apache server: <br>
#> `sudo systemctl reload httpd.service` <br>
#> `sudo systemctl restart httpd.service` <br>
