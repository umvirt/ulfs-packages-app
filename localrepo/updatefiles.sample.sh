# This script is contain commands which needed to update installed release files and data.

#go to release files directory
cd /mnt/storage/data/0.2.4

#get new packages files list
wget --no-check-certificate https://umvirt.com/linux/packages/files/0.2.4/wget -O packages.update.txt

sort packages.txt > _packages.txt
sort packages.upate.txt > _packages.update.txt
comm -23 _packages.update.txt _packages.txt > _packages2download.txt

#get files
cd packages
wget --no-check-certificate -i ../_packages2download.txt -x --cut-dirs=4 -nH -c
cd ..

#generate checksums files list
cat _packages2download.txt | awk '{print $0.".md5sum"};' > _checksums.txt

#get packages checksums
cd packages
wget --no-check-certificate -i ../_checksums.txt -x --cut-dirs=4 -nH
cd ..

#get new patches list
wget --no-check-certificate https://umvirt.com/linux/packages/patches/0.2.4/wget -O patches.update.txt

sort patches.txt > _patches.txt
sort patches.update.txt > _patches.update.txt
comm -23 _patches.update.txt _patches.txt > _patches2download.txt

#get patches
cd patches
wget --no-check-certificate -i ../_patches2download.txt -c
cd ..

#update release database
cd /srv/www/linux/packages/tmp/0.2.4
git pull

cd /srv/www/linux/packages/bin/
./load_data --path=../tmp/0.2.4 --release=0.2.4 --format=xml

#scan files
./scan_files

#scan patches
./scan_patches

#update links to files
./updatelinks2files

#check files
./getfiles packages 0.2.4 | grep ERROR | grep -v \!
./getfiles addons 0.2.4 | grep ERROR | grep -v \!
./getfiles patches 0.2.4 | grep ERROR | grep -v \!

#if no files are broken 

#go to release files directory
cd /mnt/storage/data/0.2.4

#remove temporary files with prefix "_"
rm -v  _*

#rename packages.update.txt to packages.txt
mv packages.update.txt  packages.txt

#rename patches.update.txt to patches.txt
mv patches.update.txt  patches.txt
