# UmVirt LFS Packages

## Packages database

Main component is database. It used to store packages metadata (metainformation).

ULFS Packages is uses MySQL/MariaDB servers for storing packages database.

## Objects

Database is store information about: 

- releases 
- packages
- packages templates
- addons
- patches
- nestings
- comments
- architectures
- architectures packages

### Release

Release is ULFS version. Multiple versions is neened to store various set of packages.

#### Release code

In ULFS release code contain three parts divided by points:

* First digit - Major release. Mandatory. Only total code rewriting is can increase this value. Currently ULFS Packages in development stage and 0 should be used as value.
* Second digit - Packages list rewrite. Mandatory. This value should be increased when no data is used from previous release and all packages is created, not updated. 
* Update digit - Packages list update. Optional. This value should be increased on each update. If this part is omitted that mean 0 value.

#### Release history

Release 0.1 was initial. This version is prove that ULFS Packages can be used. 
It used as source to build next release (0.2) and updates to it (0.2.1, 0.2.2, 0.2.3).

Release 0.2 is improved version of 0.1. Totaly rewritten packages list.

Release 0.2.1 is updated version of 0.2. 
This version is based on 0.2 and use data defined on it. 
Using data from previous release is allows to speed up development significantly.

Other 0.2.x releases is just updates of previous releases 0.2.(x-1).

### Package

Package is object that representing binary package, result of building one or multiple source code packages.

Threre are two types of packages:

* Usual package - Package that contain link to source code package
* Virtual package - Package that don't contain link to source code package

Packages have few properties:

* Code
* Description
* Source file
* Source directory
* Unpack script
* Configure script
* Build script
* Install script
* Local build

Packages have few links to other objects:

* Release
* Package template

Also package can have a few lists:

* Dependencies
* Patches
* Add-ons
* Nestings
* Comments

#### Code

Code is code name for binary package. 

Usualy this field value is part of source package file name.

Many packages can use one source file. 

For example, packages "mariadb-server" and "mariadb-client" is use one source file.
Package "mariadb-server" is supply MariaDB server and client. 
Package "mariadb-client" is supply only MariaDB client.

#### Description

Description is short text field describes what package is.

Usualy this field value is recived from BLFS book, source package file, package site or package source repository.

#### Source file

Source file is source code package archive file name. ULFS Packages app is find this file and it's checksum file in storage automaticaly.

#### Source directory

Source directory is directory in source file package archive where source files are located.

#### Unpack script

Bash script which used to extract Source file. 

This field is optional and can be omitted to use default value:

        tar -xf %sourcefile%

#### Configure script

Bash script which used to prepare source code package for building and installation.

This field is optional and can be omitted to use default value:

        ./configure --prefix=/usr

In 0.1 release this script is executed directly.

In other releases this script is executed after saving in "ulfs_configure.sh" script.

#### Build script

Bash script which used to build source code package.

This field is optional and can be omitted to use default value:

        make

In 0.1 release this script is executed directly.

In other releases this script is executed after saving in "ulfs_build.sh" script.

#### Install script

Bash script which used to install source code package.

This field is optional and can be omitted to use default value:

        make install

In 0.1 release this script is executed directly.

In other releases this script is executed after saving in "ulfs_install.sh" script.

#### Local build

Local build field is used to forcing local build when distributed build is available.

#### Release

This is link to specific package release. Each release can have own package with same name like in other releases.

#### Package template

This is link to specific package template.

#### Dependencies list

All packages can have dependencies. 
Dependency is packages that should be installed before installing package.
 
If package don't have dependencies that means that it's dependencies is not defined by maintainer or installed on LFS stage.

Usual package is installs all dependencies and own source package.

Virtual package is installs only dependencies.

Samples of virtual packages: X, Xlibs, Xapps, Xfonts, LXDE, MATE, XFCE.

#### Patches list

Each package can have patches which will be automatically applied after unpack and before running configure script.

ULFS Packages app is find all patches files in storage automatically.

#### Add-ons list

Each package can have additional files (add-ons) which can be used by configure/build/install scripts.

ULFS Packages app is find all add-ons files in storage automatically.

#### Nestings list

Some packages can install other packages durning own installation. Those packages are should be mentioned in nestings list.

For example, as mentioned before, package "mariadb-server" is supply MariaDB server and client. 
Package "mariadb-client" is supply only MariaDB client.
Therefore package "mariadb-client" is nesting for "mariadb-server" package.

#### Comments list

It's possible for maintainer to leave messages linked to specific packages. 

This messages is called comments and can be placed in Comments list

### Packages templates

At the begining definition of configure/build/install scripts for each package was simple task.
Nowadays it's look extremely boring especially when many packages have same configure/build/install instructions.

For example, in KF6 and Plasma so many packages and they have almost same configure/build/install instructions.

Package templates are override default values for configure/build/install sripts and allows to add packages more faster.

Using Package templates is also allows to edit configure/build/install scripts in one place. 
It's better and faster than edit each separate package.

### Architectures

Architecture is code of specific platform. 

Currently only 'lib32\_amd64' is can be used. Packages for this platform can be installed on amd64(qemu64,x86\_64) 64-bit machines to supply x86 32-bit support.

### Architectures packages 

Architectures packages (archpackages) is packages derivatives which used to build and install binaries for specific architecture.

Archpackages have few properties:

* Configure script
* Build script
* Install script

Packages have few links to other objects:

* Release
* Package

Also archpackage can have a list of dependencies.

### Commands

Commands is standalone commands to run from ULFS environment with assistant.

For example, to build kernel user can run:

      chimp kernel_build

to install kernel and modules user can run:

      chimp kernel_install

## Tables

### architectures

This table contain list of architectures. Architectures are global, not release specific.

### architectures_packages

This table contain list of architectures packages. Architectures packages are release and package specific.

### architectures_dependances

This table is contain  links list 1:M between architecture packages.

### releases

This table is contain releases list.

### packages

This table is contain packages list. Packages are release specific.

### patches

This table is contain patches list. Patches are package specific.

### addons

This table is contain add-ons list. Add-ons are package specific.

### dependances

This table is contain links list 1:M between packages.

### commands

This table is contain commands list. Commands are release specific.

### comments

This table is contain comments list. Comments are package specific.

### nestings

This table is contain links list 1:M between packages.

### packagespatchesfiles

This table is contain packages patches files list. This table is kind a packages and addons file storage cache. This table is filled by "scan_files" script.

### packagesfiles

This table is contain packages files list. This table is kind a packages and addons file storage cache. This table is filled by "scan_files" script.

### packagesfiles_packages

This table is contain links list 1:M between packages and packagesfiles.

### packages_templates

This table is contain packages templates list. Packages are release specific.

## Relational schema

![Relational schema from phpMyAdmin](db.png)

