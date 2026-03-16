# ULFS Packages files mirroring
#
# This script is download files for 0.2.4 release
#
# After files downloading a links to them in database should be updated.
#
# See updatelinks2files.sample.sh

#format disk where files will be placed
#mkfs.ext4 /dev/sdc

#create mountpoint
mkdir /mnt/storage

#mount disk
mount /dev/sdc /mnt/storage

#go to disk
cd /mnt/storage

#create directory for local repo
mkdir data

#go to that directory
cd data

#create release directory
mkdir 0.2.4

#go to this directory
cd 0.2.4

#download LFS sources
wget --no-check-certificate http://umvirt.com/linux/downloads/0.2.4/src.tar

#download LFS sources checksum
wget --no-check-certificate http://umvirt.com/linux/downloads/0.2.4/src.tar.md5sum

#create directories
mkdir packages
mkdir patches

#get packages list
wget --no-check-certificate https://umvirt.com/linux/packages/files/0.2.4/wget -O packages.txt

#generate packages checksum files list
cat packages.txt | awk '{print $0.".md5sum"};' > checksums.txt

#get patches list
wget --no-check-certificate https://umvirt.com/linux/packages/patches/0.2.4/wget -O patches.txt


#get packages
cd packages
wget --no-check-certificate -i ../packages.txt -x --cut-dirs=4 -nH -c
cd ..


#get packages checksums
cd packages
wget --no-check-certificate -i ../checksums.txt -x --cut-dirs=4 -nH
cd ..

#get patches
cd patches
wget --no-check-certificate -i ../patches.txt -c
cd ..


