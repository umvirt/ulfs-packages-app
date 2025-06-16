<?php
/**
 * ULFS Packages Web-Application
 *
 * Main startup file
 *
 * This file is loaded almost by all php-files on their startup
 */

//load base functions
include "functions/base.php";

//define base constants (uses only system functions to define)
DEFINE('APPDIR',dirname(dirname(__file__)).'/');
DEFINE('INCDIR',APPDIR.'/inc/');

//define additional constant (uses base functions to define)
DEFINE('APPCOMMIT',gitHead(APPDIR));

//load classes
include INCDIR."classes/DirParser.php";
include INCDIR."classes/DatabaseConnection.php";

//load application functions
include INCDIR."functions/ulfs.php";

//load configuration
include INCDIR."config.php";

//if debugging is needed
if(@$config['debug'])
{
    //override PHP settings
    ini_set('display_errors',1);
    ini_set('error_reporting',E_ALL);
}

//init database driver
$db=new DatabaseConnection($db_config);



