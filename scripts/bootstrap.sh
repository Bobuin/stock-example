#!/usr/bin/env bash
echo "..............................."
echo "StartUP - GO"
/etc/init.d/nginx restart
service php7.2-fpm restart
/etc/init.d/mysql restart
sudo service mysql restart
ntpdate pool.ntp.org
echo "StartUP - END"
echo "..............................."