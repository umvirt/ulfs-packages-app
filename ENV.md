# UmVirt LFS Packages

## Environment variables

### System variables

#### MAKEFLAGS

Number of threads which used to build packages with "make" command and other settings.

Default value: "`` -j`nproc` ``" (use all available CPU cores)

Safe value: "-j1"

#### NINJAJOBS

Number of threads which used to build packages with "ninja" command.

Default value: `` `nproc` `` (use all available CPU cores)

Safe value: 1

### Base variables

This variables are to define release, packages repositories and connection options.

#### UMVIRT_RELEASE

Current ULFS release.

For example: "0.2.3"

#### UMVIRT\_PACKAGES\_URL

Current ULFS Packages service (repository). Source for packages files and installation scripts.

For example: "https://umvirt.com/linux/packages/"

#### UMVIRT\_ASSISTANT\_URL

Current ULFS Assistant service (optional). Source for commands.

For example: "https://umvirt.com/linux/assistant/"

#### ULFS\_CONFIG\_FILE

Configuration file with additional variables

For example: "/etc/ulfs-packages/config"

### Additional variables

Aditional variables are loaded from **ULFS\_CONFIG\_FILE** by installation scripts.

This variables are to define packages build options.

#### ULFS\_PKG\_DOCUMENTATION

Build documentation.

Default value: "NO"

#### ULFS\_PKG\_STATIC

Build static binaries and libraries.

Default value: "NO"

#### ULFS\_PKG\_TEST

Run tests.

Default value: "NO"

#### ULFS\_PKG\_DATERESET

Reset packages files timestamps after unpack in order to improve installed files detection. 

Deprecated. Some build systems are checking a files' timestamps. Using this variable can broke packages installation.

Default value: "NO"

#### ULFS\_ICECC

Perform distributed compilation with ICECC.

Default value: "NO"

#### ULFS\_ICECC\_PATH

Path to ICECC binary.

Default value: "/usr/lib/icecream/bin"
