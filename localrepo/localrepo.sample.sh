# Localhost ULFS Packages repository sample database deployment script
#
# Aimed to be executed in ULFS after LAMP stack installation and configuration according to BLFS 12.4.
#
# See lampserver.sample.sh

#edit httpd default config
sed -e 's|#LoadModule rewrite_module|LoadModule rewrite_module|' -i /etc/httpd/httpd.conf
sed -e 's|AllowOverride None|AllowOverride All|' -i /etc/httpd/httpd.conf
systemctl restart httpd

#create database and user
echo "create user ulfs@localhost identified by 'secret';" | mariadb
echo "grant all on ulfs.* to ulfs@localhost;" | mariadb
echo "create database ulfs; " | mariadb

#go to website root
cd /srv/www

#create linux directory
mkdir linux

#go to linux directory
cd linux

#create symlic link to directory with packages files
ln -s /mnt/storage/data/ downloads

#clone ulfs-packages-app
git clone https://gitlab.com/umvirt/ulfs-packages-app packages

#go to packages directory
cd packages

#create config file
cp inc/config.php.sample inc/config.php

#create database structure
cat sql/database.sql | mariadb ulfs

#create tmp directory
mkdir tmp

#go to tmp directory
cd tmp

#clone release
git clone  https://gitlab.com/umvirt/ulfs-packages-database -b 0.2.4 0.2.4

#go to bin directory
cd ../bin

#load release data
./load_data --path=../tmp/0.2.4 --release=0.2.4 --format=xml

#test
links -dump http://127.0.0.1/linux/packages/0.2.4

# 1. list of packages should appear
# 2. packages files and add-ons files should be downloadable
# 3. bash script should be generated properly

#clone ulfs-packages-assistant
git clone https://gitlab.com/umvirt/ulfs-packages-assistant assistant

#go to packages directory
cd assistant

#create config file
cp inc/config.php.sample inc/config.php
