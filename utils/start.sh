#!/bin/bash


cd /PATH/TO/PHPMYADMIN
gnome-terminal --tab --command="php -S localhost:7777"		#start PhpMyAdmin server

cd /PATH/TO/REPO/NeuralNetworkBuilder/nnb_website
gnome-terminal --tab --command="php artisan serve" 		#start PHP server

cd /PATH/TO/REPO/NeuralNetworkBuilder/nnb_website
gnome-terminal --tab --command="npm run watch"  		#start watch (js and css files)

#gnome-terminal --tab --command="sudo pkill supervisord"
gnome-terminal --tab --command="sudo supervisord -c /etc/supervisor/supervisord.conf"
gnome-terminal --tab --command="sudo supervisorctl"

cd /PATH/TO/REPO/NeuralNetworkBuilder/nnb_website
clear

