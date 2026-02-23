# Sample LAMP stack WEB-server installation script
#
# This sample script can be used to deploy ULFS packages local repository
#
# Test environment
#
# Source image: ULFS 0.2.4 Mate
# RAM: 20G
# CPU cores:  6
# Shared disk: sources.img mount point /sources

#Packages installation

chimp install mariadb-server
chimp install php
chimp install openssh

#MySQL configuration

install -v -m755 -o mariadb -g mariadb -d /run/mariadb &&
mariadbd-safe --user=mariadb 2>&1 >/dev/null &

mariadb-admin -u root password

mariadb-admin -p shutdown

#Apache httpd configuration 

sed -i 's@php/includes"@&\ninclude_path = ".:/usr/lib/php"@' \
    /etc/php.ini


sed -i -e '/proxy_module/s/^#//'      \
       -e '/proxy_fcgi_module/s/^#//' \
       /etc/httpd/httpd.conf
echo \
'ProxyPassMatch ^/(.*\.php)$ fcgi://127.0.0.1:9000/srv/www/$1' >> \
/etc/httpd/httpd.conf

#create sample php script

cat > /srv/www/p.php << EOF
<?php
phpinfo();

#BLFS systemd services install

(
cd /usr/share/blfs-systemd-units/
make install-sshd
make install-httpd
make install-mariadb
make install-php-fpm
)

#reboot

#check results

#php script handling check

links -dump http://127.0.0.1/p.php

#mariadb server check

ss -atnp | grep 3306
