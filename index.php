<?php

include "inc/main.site.php";

echo "<h1>ULFS Packages</h1>";


$release=@$_REQUEST['release'];

$sql="select id, `release` from releases";

$db->execute($sql);

$x=$db->dataset;

$releases=array();
foreach ($x as $k=>$v){

$s=$v['release'];
if($release==$v['release']){
$s="<b>".$v['release']."</b>";
}

$releases[]="<a href=?release=".$v['release'].">".$s."</a>";
}

echo "Current releases: ".join ($releases,', ');

//----


$sql="select id, code, sourcefile from packages";

$db->execute($sql);

$x=$db->dataset;

$pkgs=array();
foreach ($x as $k=>$v){

$s=$v['code'];

$pkgs[]="<tr><td><a href=package.php?release=".$release."&package=".$v['code'].">".$s."</a></td><td>".$v['sourcefile']."</td></tr>";
}

echo "<h2>Packages(".count($x).")</h2>";
echo "Available packages: <table>".join ($pkgs)."</table>";

