# UmVirt LFS Packages

## Packages storage

Packages storage is disk space used to store source packages files, addons files, patches files.

### Structure

#### Releases

All source packages files are stored localy with splitting by releases.

For example, at some directory you can create subfolders for releases that you want:

        /
                0.1
                0.2
                0.2.1
                0.2.2
                0.2.3

##### LFS packages files

Source packages files which was mentioned in LFS book are stored in "sources.tar" file in release directory.

##### Patches

Patches files are stored in "patches" directory inside specific relelease directory.

##### Packages

Packages files are stored in "packages" directory inside specific relelease directory.

In order to simpify search and impliment ability to skip optional packages for diskspace economy, source code packages are alocated in directories 

##### One digit / one character directories

One digit / one character directories are used to storing packages which file names are started from such digit or letter.

Files should stored inside this directories only if other directories are not suitable for them.

##### Xorg

"Xorg" directory is used to store packages files for Xorg graphical environment.

There are subdirectories:

- app
- font
- lib

"Xorg/app" directory is used to store files which mentioned in chapter "Xorg Applications" of BLFS book.

"Xorg/font" directory is used to store files which mentioned in chapter "Xorg Fonts" of BLFS book.

"Xorg/lib" directory is used to store files which mentioned in chapter "Xorg Libraries" of BLFS book.

##### Perl modules

"perl-modules" directory is used to store Perl modules files.

##### Python modules

"python-modules" directory is used to store Python modules files.

##### KDE

"KDE" directory is used to store packages files for KDE Desktop Environment.

There are subdirectories:

- apps
- kf
- plasma

"KDE/apps" directory is used to store files which mentioned in chapter "KDE Frameworks Based Applications" of BLFS book.

"KDE/kf" directory is used to store files which mentioned in chapter "KDE Frameworks" of BLFS book.

"KDE/lib" directory is used to store files which mentioned in chapter "KDE Plasma" of BLFS book.

KDE directory is optional.

##### LXQT

LXQT directory is used to store packages for LXQT Desktop Environment. 

Because LXQT packages is depending on KDE Desktop Environment KDE folder is needed.

LXQT directory is optional.

