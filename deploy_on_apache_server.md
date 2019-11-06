
- Install PHP and httpd (apache):
#> sudo dnf -y install httpd php php-cli php-php-gettext php-mbstring php-mcrypt php-mysqlnd php-pear php-curl php-gd php-xml php-bcmath php-zip


- Install phpMyAdmin:
#> sudo dnf -y install phpMyAdmin


- Enable and start httpd service:
#> sudo systemctl start httpd
#> sudo systemctl enable httpd
	[Output: Created symlink /etc/systemd/system/multi-user.target.wants/httpd.service → /usr/lib/systemd/system/httpd.service]

- Set User and Server permission:
#> sudo chown your-username:apache -R /path/to/repo/NeuralNetworkBuilder/website
#> sudo chmod -R 775 /path/to/repo/NeuralNetworkBuilder/website
#> sudo usermod apache --append --groups YOUR-USERNAME
#> sudo usermod YOUR-USERNAME --append --groups apache

- If "sites-available/" and "sites-enabled/" directories doesn't exists, than create it:
#> sudo mkdir /etc/httpd/sites-available
#> sudo mkdir /etc/httpd/sites-enabled


- Create a config file for laravel (you can find it in `utils/apache_conf/neuralnetworkbuilder.conf`): /etc/httpd/sites-available/neuralnetworkbuilder.conf >
"""
	Listen 8080
	ServerName neuralnetworkbuilder
	<VirtualHost *:8080>
	    ServerAdmin admin@example.com
	    ServerName neuralnetworkbuilder
	    DocumentRoot /path/to/repo/NeuralNetworkBuilder/website/public
	    
	    <Directory /path/to/repo/NeuralNetworkBuilder/website/public>
		    Options Indexes FollowSymLinks MultiViews
		    AllowOverride All
		    Require all granted
	    </Directory>
	     
	    LogLevel debug
	    ErrorLog /path/to/repo/NeuralNetworkBuilder/website/storage/logs/error.log
	    CustomLog /path/to/repo/NeuralNetworkBuilder/website/storage/logs/access.log combined
	</VirtualHost>
"""

- Add the virtual host 'neuralnetworkbuilder.dev':
#> echo "127.0.0.1 neuralnetworkbuilder" >> /etc/hosts 

Create a neuralnetworkbuilder.conf softlink from sites-available to sites-enabled:
#> ln -s /etc/httpd/sites-available/neuralnetworkbuilder.conf /etc/httpd/sites-enabled/neuralnetworkbuilder.conf


- Add the following line at the end of '/etc/httpd/conf/httpd.conf' file:
  "IncludeOptional sites-enabled/*.conf"


- Test if config is ok:
#> apachectl configtest


- Reload/Restart apache server (AS SUDO):
#> sudo systemctl reload httpd.service
#> sudo systemctl restart httpd.service


- If are you running on Fedora 29/30/31 (or other OS with SELinux):
- SELINUX WILL CAUSE A LOT OF PROBLEMS. DISABLE IT TEMPORARY:
	#> sudo set enforce 0


- Otherwise try running all this commands, but still you will get errors because php-fpm will try to write things for each sessions started (SELinux will block this attempts):
#> chcon -R -t httpd_sys_content_t path/to/repo/NeuralNetworkBuilder
#> sudo setsebool -P httpd_read_user_content 1
#> sudo setsebool -P httpd_can_network_connect 1
#> sudo setsebool -P httpd_graceful_shutdown 1
#> sudo setsebool -P httpd_can_network_relay 1
#> sudo setsebool -P nis_enabled 1
#> sudo setsebool -P httpd_execmem 1
#> sudo setsebool -P httpd_unified 1

#> sudo ausearch -c 'php-fpm' --raw | audit2allow -M my-phpfpm
#> sudo semodule -X 300 -i my-phpfpm.pp

#> sudo ausearch -c 'httpd' --raw | audit2allow -M my-httpd
#> sudo semodule -X 300 -i my-httpd.pp

#> sudo semanage fcontext -a -t httpd_sys_rw_content_t 'error.log'
#> sudo restorecon -v 'error.log'


