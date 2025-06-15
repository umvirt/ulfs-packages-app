<?php
/**
 * ULFS Packages Web-Application
 *
 * HTML controller
 *
 * HTML controller is used to render HTML pages from "html" directory.
 */

//load application
include "inc/main.site.php";

echo "<h1>UmVirt LFS Packages</h1>";
$pages=array(
    "api","howitworks","howtofork"
);

$page=@$_REQUEST['page'];
if(in_array($page,$pages))
{
    echo file_get_contents('html/'.$page);
}


//render page with template
include "inc/template.php";
