# UmVirt LFS Packages

## Coding related information

### Standards

[PHP Framework Interoperability Group](https://www.php-fig.org/) (PHP-FIG) is issue various [PHP Standards Recommendations](https://www.php-fig.org/psr/) (PSRs) which accepted by many PHP developers.

#### Coding styles

Standardized formatting reduces the cognitive friction when reading code from other authors.

Currently Packages web-application PHP-code is not well formatted but we strive to make it formatted according to [PSR-1](https://www.php-fig.org/psr/psr-1) and [PSR-12](https://www.php-fig.org/psr/psr-12) standards.

#### Code documentation

PHPDoc is used to generate API docunentation.

PHPDoc standard is defined in [PSR-5](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md)

#### Text documentation

Markdown is a simple markup language for formatting text documentation. It supported by GitHub and GitLab services.

### Software

#### KDevelop

On early stage, when Packages web-application was very unstable only console text editors has been used.

Currently it is possible to build comfortable desktop environment and use various graphical Integrated Development Environments (IDEs).

KDE desktop environment is offer a [KDevelop IDE](https://kdevelop.org/) among other KDE applications. 

KDevelop have very heavy advantages in opposition to other IDEs:

- KDevelop is free and open with GPL2 license
- KDevelop is writen in C++ and QT. Nor binaries nor long bootstap is needed.
- No downloads. KDevelop is can be built and used off-line.
- KDevelop is can be built inside ULFS.

#### SSHFS

It's possible to use shared folders on remote machine with various protocols. 
Shared folders installation and configuration is can take a time.

As alternative, SSH protocol can be used to mount shared folders with [SSHFS](https://github.com/libfuse/sshfs). 
No installation and configuration is needed. 
In most cases SSH protocol is already supported on remote machine.

#### GIT

We use [GIT](https://git-scm.com/) as Version Control System (VCS).

#### Doxygen

Doxygen is can be used to generate documentation. It support PHPDoc and Markdown. 

