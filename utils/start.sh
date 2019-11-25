#!/bin/bash

cd /PATH/TO/REPO/NeuralNetworkBuilder/website
gnome-terminal --tab --command="npm run watch"  		#start watch (js and css files)

#gnome-terminal --tab --command="sudo pkill supervisord"
gnome-terminal --tab --command="sudo supervisord -c /etc/supervisor/supervisord.conf"
gnome-terminal --tab --command="sudo supervisorctl"

clear

