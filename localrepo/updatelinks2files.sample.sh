# Database links updater
#
# This script is update links to files in database.
# Also this script shows broken links.
#
# Don't try to install anything if broken links take place.
# One packages can pull other packages as dependencies.

#go to bin directory
cd /srv/www/linux/packages/bin

#scan packages files
./scan_files

#scan patches
./scan_patches

#update links to files in database
./updatelinks2files

#check files
./getfiles packages 0.2.4 | grep ERROR | grep -v \!
./getfiles addons 0.2.4 | grep ERROR | grep -v \!
./getfiles patches 0.2.4 | grep ERROR | grep -v \!

#no records should appear
