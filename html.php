<?php

include "inc/main.site.php";

echo "<h1>UmVirt LFS Packages</h1>";
$pages=array(
"api","howitworks","howtofork"
);
$page=@$_REQUEST['page'];
if(in_array($page,$pages)){
echo file_get_contents('html/'.$page);
}

include "inc/template.php";
