#!/usr/bin/env php
<?php
include "../inc/main.php";

$sql="truncate packagesfiles_packages";
$db->execute($sql);

$sql="select id,`release`,filename from packagesfiles";
$db->execute($sql);
//var_dump($db->error);
$pfs=$db->dataset;

foreach ($pfs as $pf){
echo $pf['filename']."\n";


//Packages

$sqlp="select id from packages where sourcefile=\"".trim($pf['filename'])."\"";
//echo $sqlp;//exit;
$db->execute($sqlp);
//var_dump($db->error);
$x=$db->dataset;
//echo count($x); exit;

foreach ($x as $p){
//echo $p['id']."\n";
$sqlpf="insert into packagesfiles_packages (packagefile,package) values (".$pf['id'].",".$p['id'].")";
$db->execute($sqlpf);
}

//Addons

$sqlp="select package from addons where filename=\"".trim($pf['filename'])."\"";
//echo $sqlp;//exit;
$db->execute($sqlp);
//var_dump($db->error);
$x=$db->dataset;
//echo count($x); exit;

foreach ($x as $p){
//echo $p['id']."\n";
$sqlpf="insert into packagesfiles_packages (packagefile,package) values (".$pf['id'].",".$p['package'].")";
$db->execute($sqlpf);
}





}
