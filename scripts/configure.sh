#!/usr/bin/env bash
echo "...................................................."
echo "...................................................."
echo "...................> Start StockExample Configure"
echo "...................................................."
echo "...................> Preparing StockExample Environment"
echo ".....> Install..PHP..7-2"
sed -i "s/PermitRootLogin without-password/PermitRootLogin yes/" /etc/ssh/sshd_config
sed -i "s/PasswordAuthentication no/PasswordAuthentication yes/" /etc/ssh/sshd_config
/etc/init.d/ssh restart
mkdir /root/.ssh
cat /tmp/source-file/key/id_rsa.pub >> /root/.ssh/authorized_keys
useradd -g root -G sudo -s /bin/bash -d /home/developer -m  developer
echo "developer:payb"|chpasswd
mkdir /var/log/php
apt-get update
apt-get install apt-transport-https lsb-release ca-certificates -y
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
apt-get update
apt-get install php7.2 php7.2-fpm php7.2-dev php7.2-intl php7.2-mbstring php7.2-sqlite php7.2-curl php7.2-mysqlnd php7.2-pgsql php7.2-xdebug php7.2-bcmath -y

pecl install timezonedb
echo "error_log = php_errors.log" >> /etc/php/7.2/fpm/php.ini
echo "extension=timezonedb.so" >> /etc/php/7.2/fpm/php.ini
echo "error_log = php_errors.log" >> /etc/php/7.2/cli/php.ini
echo "extension=timezonedb.so" >> /etc/php/7.2/cli/php.ini
echo "....> Install..NGINX..1-6"
apt-get remove apache2 -y
apt-get install nginx -y

echo "....> Install MySQL.."
echo 'deb http://repo.mysql.com/apt/debian/ jessie mysql-apt-config' >> /etc/apt/sources.list.d/mysql.list
echo 'deb http://repo.mysql.com/apt/debian/ jessie mysql-5.6' >> /etc/apt/sources.list.d/mysql.list
echo 'deb http://repo.mysql.com/apt/debian/ jessie mysql-tools' >> /etc/apt/sources.list.d/mysql.list
echo 'deb-src http://repo.mysql.com/apt/debian/ jessie mysql-5.6' >> /etc/apt/sources.list.d/mysql.list
apt-get update
export DEBIAN_FRONTEND=noninteractive

mysqlpassword="root"
debconf-set-selections <<< "mysql-server mysql-server/root_password password $mysqlpassword"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $mysqlpassword"
apt-get install -q -y  --force-yes mysql-community-server

echo 'bind-address = 0.0.0.0' >> /etc/mysql/mysql.conf.d/mysqld.cnf
echo 'skip-character-set-client-handshake' >> /etc/mysql/mysql.conf.d/mysqld.cnf
echo 'collation_server=utf8_unicode_ci' >> /etc/mysql/mysql.conf.d/mysqld.cnf
echo 'character_set_server=utf8' >> /etc/mysql/mysql.conf.d/mysqld.cnf
service mysql restart

if [ -f /var/lib/mysql/stockdb/db.opt ]
then
	echo "XXXXXXXXX: Database already exist"
else
    echo "XX - Execute query"
    echo "XX - Set rights to local"
    mysql -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD(''); FLUSH PRIVILEGES;"
    echo "XX - Set rights to %"
    mysql -e "CREATE USER 'root'@'%' IDENTIFIED BY '$mysqlpassword'; FLUSH PRIVILEGES;"
    echo "XX - Set grants"
    mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;"
    mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO 'vagrant'@'%' IDENTIFIED BY 'vagrant' WITH GRANT OPTION;"
    mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO 'vagrant'@'localhost' IDENTIFIED BY 'vagrant' WITH GRANT OPTION;"
    mysql -uroot -e "CREATE DATABASE stockdb;"
fi

service mysql restart

echo "...> Configure NGINX"
rm /etc/nginx/sites-enabled/default
mv /tmp/source-file/nginx/stock.local.conf /etc/nginx/sites-enabled/
mv /tmp/source-file/nginx/* /etc/nginx/


mkdir /etc/ssl/stock.com
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -nodes -subj "/C=UA/ST=Kiev/L=Kiev/O=Company/CN=stock.com.local/emailAddress=coolbob.coolbob@gmail.com" -keyout /etc/ssl/stock.com/stock.com.key -out /etc/ssl/stock.com/stock.com.csr

echo "...> Configure PHP-FPM"
#mv /etc/php5/fpm /etc/php5/fpm-Save
mv /tmp/source-file/php/fpm /etc/php/7.2/fpm
echo "...> Configure XDebug"
echo "; xdebug
zend_extension=xdebug.so
xdebug.remote_enable=1
xdebug.remote_connect_back=1
xdebug.remote_port=9000
xdebug.remote_host=192.168.10.20" > /etc/php/7.2/mods-available/xdebug.ini
#ln -s /etc/php5/mods-available/mysqlnd.ini /etc/php5/fpm/conf.d/10-mysqlnd.ini

echo "...> Restart NGINX & PHP & MySQL"
service nginx restart
service php7.2-fpm restart
service mysql restart

echo "....> Install programs"
apt-get install mc htop zip unzip mtr wget -y
mkdir /var/log/stock
chown www-data:www-data /var/log/stock
mv /tmp/source-file/mc.config /root/.config
cp -R /root/.config /home/vagrant/
chown vagrant:vagrant -R /home/vagrant/.config
cp -R /root/.config /home/developer/
chown developer -R /home/developer/.config
echo "....> Install composer"
cd /tmp/source-file
wget -q https://getcomposer.org/composer.phar 
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
apt-get install git -y


