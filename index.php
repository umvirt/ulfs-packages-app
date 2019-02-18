<?php

include "inc/main.site.php";

echo "<h1>UmVirt LFS Packages</h1>";
//var_dump($_SERVER);

$release=@addslashes($_REQUEST['release']);

$sql="select id, `release` from releases";

$db->execute($sql);

$x=$db->dataset;

$releases=array();
foreach ($x as $k=>$v){

$s=$v['release'];
if($release==$v['release']){
$s="<b>".$v['release']."</b>";
}

$releases[]="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/".$v['release'].">".$s."</a>";
}


if(!$release){
echo "Select release: ".join ($releases,', ');

}else{
echo "Current releases: ".join ($releases,', ');

//----


$sql="select p.id, code, sourcefile from packages p
inner join releases r on r.id=p.`release`
where r.`release`=\"".$release."\"";
//echo $sql;
$db->execute($sql);

$x=$db->dataset;

$pkgs=array();
foreach ($x as $k=>$v){

$s=$v['code'];

$pkgs[]="<tr><td><a href=".dirname($_SERVER['SCRIPT_NAME'])."/$release/".$v['code'].">".$s."</a></td><td>".$v['sourcefile']."</td></tr>";
}

echo "<h2>Packages(".count($x).")</h2>";
echo "Available packages: <table>".join ($pkgs)."</table>";

}

include "inc/template.php";
