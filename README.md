# UmVirt LFS Packages

## About

UmVirt LFS Packages (ULFS Packages) is LAMP-stack based package management Web-service for UmVirt Linux From Scratch (ULFS) distribution. 

## License

ULFS Packages service is licensed under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

Source packages license information can be found on source packages files, sites and repositories.

## Structure

### Packages database

Main component is database. It used to store packages metadata (metainformation).

ULFS Packages is uses MySQL/MariaDB servers for storing packages database.

[More info](DATABASE.md)

### Packages storage

Packages storage is disk space used to store source packages files, addons files, patches files.

[More info](STORAGE.md)

### Packages app

Packages app is web-application (web-site) which render web-pages and bash scripts for download, unpack, configure, build, install source code packages.

Packages app is written on PHP. PHP is can be built simply by GCC inside BLFS no bootstraping is needed.

PHP is powerful technology to build web-applications it used in famous projects:

* [FreePBX](https://github.com/FreePBX/core) 
* [ZoneMinder](https://github.com/ZoneMinder/zoneminder)
* [NextCloud](https://github.com/nextcloud/server) 

### Local environment

After building Linux From Scratch environment have to be modified by LFScustomizer in order to interact with Packages app with Assistant script.

### Assistant script

Assistant script is BASH-script called "chimp" which installed on local environment (usualy in /bin directory) and interact with Packages app over network via HTTP requests.

Instead of manual downloading, unpacking and typing configure, build and install commands. User is running command:

        chimp install %package%

It's more simple and faster isn't it?

## How it works

If user uses ULFS "0.2.3" and wants to install package "mc", he can run command as root user:

        chimp install mc

Then Assistant script is sending request to Packages app on address:

        https://umvirt.com/linux/packages/0.2.3/mc/install

Then Packages app is receive request from Assistant script and generate BASH-script to install mc package by interacting with Packages database.
This BASH-script is returned as response to Assistant script.

Then Assistant script is passes received BASH-script to BASH interpreter and magic start to happens:

- all dependencies of package will be checked. If they not installed their own installation and installation of their dependencies is started recursively.
- source package is downoladed with checksum file, addons and patches.
- if patches defined, they are will be applied.
- configuration instructions are will be saved in ulfs_configure script and executed with logging in configure.log
- build instructions are will be saved in ulfs_build script and executed with logging in build.log
- install instructions are will be saved in ulfs_install script and executed with logging in install.log
- after installation new files are will be detected and their paths will be stored in files.txt







