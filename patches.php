<?php

include "inc/main.site.php";

$release=@addslashes($_REQUEST['release']);

switch (@$_REQUEST['act']){

case "wget":

$sql="select id, `release` from releases";
$db->execute($sql);
$x=$db->dataset;

foreach ($x as $k=>$v){

$s=$v['release'];
if($release==$v['release']){
$releaseid=$v['id'];
}

}

header('Content-type: text/plain');

$sql="SELECT id,path,filename FROM `packagespatchesfiles`  where `release` = $releaseid order by path,filename";
$db->execute($sql);

$x=$db->dataset;

foreach ($x as $k=>$v){
echo  $config['downloads_url'].$release.'/patches'.$v['path'].'/'.$v['filename']."\n";
}

exit;
break;

default:

echo "<h1>UmVirt LFS Packages</h1>";

$sql="select id, `release` from releases";

$db->execute($sql);

$x=$db->dataset;

$releases=array();


foreach ($x as $k=>$v){

$s=$v['release'];
if($release==$v['release']){
$s="<b>".$v['release']."</b>";
$releaseid=$v['id'];
}

$releases[]="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/files/".$v['release'].">".$s."</a>";
}

echo "Current releases: ".strjoin ($releases,', ');



echo "<p>[  <a href=/linux/packages/".$release."/>Packages</a> | <a href=/linux/packages/files/".$release."/>Files</a> ]</p>";
echo "<h2>Patches</h2>";


$sql="SELECT `release`,path,sum(size) size, count(id) cnt FROM `packagespatchesfiles`  where `release` = $releaseid group by `release`,path";

$db->execute($sql);

$x=$db->dataset;

$s="";
$size=0;
$cnt=0;

foreach ($x as $k=>$v){

//$s.='<li><b>'.$v['path'].'</b> - '.round($v['size']/1024/1024).'MB in '.$v['cnt'].' files';
$size+=$v['size'];
$cnt+=$v['cnt'];

}

//echo "<ul>".$s."</ul>";

echo "Total size: ".round($size/1024/1024).'MB'."<br>";
echo "Total files: ".$cnt."<br>";

echo "<p> Download all files with: <a href=/linux/packages/patches/".$release."/wget>WGET</a> </p>";

}




include "inc/template.php";
