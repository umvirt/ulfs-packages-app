<?php

include "inc/main.site.php";

echo "<h1>UmVirt LFS Package info</h1>";


$release=@addslashes($_REQUEST['release']);
$package=@addslashes($_REQUEST['package']);

$sql="select p.id, r.`release`, unpack, code, sourcefile, sourcedir, configure, build, install,
description,
pf.id packagefile, pf.size packagefile_size, md5_stored packagefile_md5 
from packages p
left join releases r on p.release=r.id
left join packagesfiles_packages pf_p on pf_p.package=p.id
left join packagesfiles pf on pf.id=pf_p.packagefile 
where r.`release`=\"$release\" and p.code=\"$package\"";

//var_dump($sql);
$db->execute($sql);

$x=$db->dataset;

$pkgs=array();
foreach ($x as $k=>$v){

$s=$v['code'];

//$pkgs[]="<tr><td><a href=package.php?release=".$release."&package=".$v['code'].">".$s."</a></td><td>".$v['sourcefile']."</td></tr>";

echo "<h2>".$v['code']."</h2>";

echo $v['description'];

echo "<h3>Package info</h3>";
if($v['sourcefile']){

$link=$v['sourcefile'];
$linkmd5=$v['sourcefile'].".md5sum";


$url=download_url($release, $v['sourcefile']);
if($url){
$link="<a href=$url>$url</a>";
}

$url=download_url($release, $v['sourcefile'].".md5sum");
if($url){
$linkmd5="<a href=$url.md5sum>$url</a>";
}

echo "Codename: ".$v['code']."<br>";
echo "Source file: ".$v['sourcefile']."<br>";
if($v['packagefile']){
echo "Source file size: ".$v['packagefile_size']."<br>";
echo "Source file MD5-checkum: ".($v['packagefile_md5'] ? $v['packagefile_md5'] : "none") ."<br>";
}
echo "Source directory: ".$v['sourcedir']."<br>";
echo "Package URL: $link<br>";
echo "Package md5-checksum URL: $linkmd5<br>";
}else{
echo "Codename: ".$v['code']."<br>";

}

$dependances=dependances($release, $v['code']);
foreach($dependances as $dep){
$depends[]="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/$release/".$dep['code'].">".$dep['code']."</a>";

}


if(count($dependances)){
echo "Dependances: ".strjoin($depends,", ").".<br>";
}else{
echo "Dependances: *** NO DEPENDANCES FOUND *** <br>";

}

$patches=patches($release,$v['code']);


foreach($patches as $pat){
$url=patch_url($release,$pat['filename']);
if(file_exists(patch_path($release,$pat['filename']))){
$pats[]="<a href=\"$url\">".$pat['filename']."</a>";
}else{
$pats[]=$pat['filename'];
}
}

if(count($patches)){
echo "Patches: ".strjoin($pats,", ").".<br>";
}else{
echo "Patches: *** NO PATCHES FOUND *** <br>";
}


$addons=addons($release,$v['code']);


foreach($addons as $addn){
$url=download_url($release,$addn);
if($url){
$addns[]="<a href=\"$url\">$addn</a>";
}else{
$addns[]="$addn";
}
}

$nestings = nestings($release,$v['code']);

foreach($nestings as $nesting){
//$url=download_url($release,$addn);
//$addns[]="<a href=\"$url\">$addn</a>";
$nestings_[]="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/$release/".$nesting.">".$nesting."</a>";
}







if(count($addons)){
echo "Addons: ".strjoin($addns,", ").".<br>";
}else{
echo "Addons: *** NO ADDONS FOUND *** <br>";
}

if(count($nestings)){
echo "Nestings: ".strjoin($nestings_,", ").".<br>";
}else{
echo "Nestings *** NO NESTINGS FOUND *** <br>";
}


if($v['unpack']){
echo "Unpack script: 
<br><pre>".configuration_script($v['unpack'])."</pre><br>";
}
echo "Configuration script: 
<br><pre>".configuration_script($v['configure'])."</pre><br>";
echo "Build script: 
<br><pre>".build_script($v['build'])."</pre><br>";
echo "Install script: 
<br><pre>".install_script($v['install'])."</pre><br>";




//echo "Available packages: <table>".strjoin ($pkgs)."</table>";

$id=$v['id'];
}


$sql="select a.code, ap.configure, ap.build, ap.install
from packages p left join releases r on p.release=r.id 
left join packagesfiles_packages pf_p on pf_p.package=p.id 
left join packagesfiles pf on pf.id=pf_p.packagefile 
inner join architectures_packages ap on ap.package=p.id 
left join architectures a on ap.architecture=a.id
where r.`release`=\"$release\" and p.code=\"$package\"";

$db->execute($sql);
$x=array();

$x=$db->dataset;
//var_dump($sql);
if(count($x)){
echo "<h3>Arch specific instructions</h3>";
foreach($x as $v){
echo "<h4>".$v["code"]."</h4>";
echo "Configuration script: 
<br><pre>".configuration_script($v['configure'])."</pre><br>";
echo "Build script: 
<br><pre>".build_script($v['build'])."</pre><br>";
echo "Install script: 
<br><pre>".install_script($v['install'])."</pre><br>";

}
}


$x=array();
$sql="select package, text comment from comments where package=$id";
$db->execute($sql);


$x=$db->dataset;
//var_dump($sql);
if(count($x)){
echo "<hr><p>Comments:<ol>";
foreach($x as $v){
echo "<li><pre>".$v["comment"]."</pre></li>";
}
echo "</ol></p>";

}


include "inc/template.php";

