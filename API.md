# UmVirt LFS Packages

## Application programming interface (API)

Packages web-application is provides API for interaction with various software.

### PHP-files

Main entry points in Packages web-application are php-files in root directory.

They can be reached directly or with rewrite engine.

Rules for rewrite engine is stored in ".htacces" file.

#### depmap.php

- render or load dependencies map (release is defined)
    - [depmap.php?release=0.1](depmap.php?release=0.1)

#### files.php

- files page (release is defined, act is not defined)
    - [files.php?release=0.1](files.php?release=0.1)
- wget files list  (release is defined and act is "wget")
    - [files.php?release=0.1&act=wget](files.php?release=0.1&act=wget)
    
#### index.php

- default page(no variables defined):
    - [index.php](index.php)
- releases list(only format is defined):
    - [index.php?format=text](index.php?format=text)
    - [index.php?format=json](index.php?format=json)
    - [index.php?format=xml](index.php?format=xml)
- packages page(only releases is defined):
    - [index.php?release=0.1](index.php?release=0.1)
- packages list(format and release is defined):
    - [index.php?format=text&release=0.1](index.php?format=text&release=0.1)
    - [index.php?format=json&release=0.1](index.php?format=json&release=0.1)
    - [index.php?format=xml&release=0.1](index.php?format=xml&release=0.1)
- packages descriptions page(format defined as "descriptions" and release is defined):
    - [index.php?format=descriptions&release=0.1](index.php?format=descriptions&release=0.1)

#### package.php

- package info page (package and release is defined)
    - [package.php?release=0.2.3&package=wine](package.php?release=0.2.3&package=wine)

#### packageexport.php

- package export document (format, release, and package is defined)
    - [packageexport.php?release=0.2.3&package=wine&format=xml](packageexport.php?release=0.2.3&package=wine&format=xml)
    - [packageexport.php?release=0.2.3&package=wine&format=json](packageexport.php?release=0.2.3&package=wine&format=json)

#### packageinstall.php

- package installation script
    - [packageinstall.php?release=0.2.3&package=wine](packageinstall.php?release=0.2.3&package=wine)
- archpackage installation script
    - [packageinstall.php?release=0.2.3&package=wine&arch=lib32_amd64](packageinstall.php?release=0.2.3&package=wine&arch=lib32_amd64)

#### patches.php

- files page (release is defined, act is not defined)
    - [patches.php?release=0.1](patches.php?release=0.1)
- wget files list  (release is defined and act is "wget")
    - [patches.php?release=0.1&act=wget](patches.php?release=0.1&act=wget)

