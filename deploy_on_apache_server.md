**IMPORTANT NOTE:** <br>
- If are you running on Operating Systems with SELinux (or others security modules): <br>
**SELINUX WILL CAUSE A LOT OF PROBLEMS. DISABLE IT!** <br>
_(the following command temporary disable SELinux, it will be up again at next restart)_ <br>
#> `sudo set enforce 0` <br>

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


[ OPTIONAL ] <br>
- Add the virtual host `neuralnetworkbuilder`: <br>
#> `echo "127.0.0.1 neuralnetworkbuilder" >> /etc/hosts` <br>
<br>
[ OPTIONAL ] <br>


- Create a `neuralnetworkbuilder.conf` softlink from `sites-available/` to `sites-enabled/` directory: <br>
#> `ln -s /etc/httpd/sites-available/neuralnetworkbuilder.conf /etc/httpd/sites-enabled/neuralnetworkbuilder.conf` <br>


- Add the following line at the end of `/etc/httpd/conf/httpd.conf` file: <br>
  ``` IncludeOptional sites-enabled/*.conf ``` <br>


- Test if apache config it's ok: <br>
#> `apachectl configtest` <br>


- Reload/Restart apache server (AS SUDO): <br>
#> `sudo systemctl reload httpd.service` <br>
#> `sudo systemctl restart httpd.service` <br>


[OPTIONAL] <br>
- Otherwise try running all this commands, but you will get errors still because _php-fpm_ will try to write things for each started user sessions (SELinux will block this attempts) into the application: <br>
#> `chcon -R -t httpd_sys_content_t path/to/repo/NeuralNetworkBuilder` <br>
#> `sudo setsebool -P httpd_read_user_content 1` <br>
#> `sudo setsebool -P httpd_can_network_connect 1` <br>
#> `sudo setsebool -P httpd_graceful_shutdown 1` <br>
#> `sudo setsebool -P httpd_can_network_relay 1` <br>
#> `sudo setsebool -P nis_enabled 1` <br>
#> `sudo setsebool -P httpd_execmem 1` <br>
#> `sudo setsebool -P httpd_unified 1` <br>
#> `sudo ausearch -c 'php-fpm' --raw | audit2allow -M my-phpfpm` <br>
#> `sudo semodule -X 300 -i my-phpfpm.pp` <br>
#> `sudo ausearch -c 'httpd' --raw | audit2allow -M my-httpd` <br>
#> `sudo semodule -X 300 -i my-httpd.pp` <br>
#> `sudo semanage fcontext -a -t httpd_sys_rw_content_t 'error.log'` <br>
#> `sudo restorecon -v 'error.log'` <br>
<br>
[OPTIONAL] <br>