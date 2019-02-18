<?php

include "inc/main.site.php";

echo "<h1>UmVirt LFS Package info</h1>";


$release=@addslashes($_REQUEST['release']);
$package=@addslashes($_REQUEST['package']);

$sql="select p.id, r.`release`, code, sourcefile, sourcedir, configure, build, install from packages p
left join releases r on p.release=r.id
where r.`release`=\"$release\" and p.code=\"$package\"";

//var_dump($sql);
$db->execute($sql);

$x=$db->dataset;

$pkgs=array();
foreach ($x as $k=>$v){

$s=$v['code'];

//$pkgs[]="<tr><td><a href=package.php?release=".$release."&package=".$v['code'].">".$s."</a></td><td>".$v['sourcefile']."</td></tr>";

echo "<h2>".$v['code']."</h2>";

$url=download_url($release, $v['sourcefile']);

$link="<a href=$url>$url</a>";
$linkmd5="<a href=$url.md5sum>$url.md5sum</a>";

echo "Codename: ".$v['code']."<br>";
echo "Source file: ".$v['sourcefile']."<br>";
echo "Source directory: ".$v['sourcedir']."<br>";
echo "Package URL: $link<br>";
echo "Package md5-checksum URL: $linkmd5<br>";

$dependances=dependances($release, $v['code']);
foreach($dependances as $dep){
$depends[]="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/$release/".$dep.">$dep</a>";
}


if(count($dependances)){
echo "Dependances: ".join($depends,", ").".<br>";
}else{
echo "Dependances: *** NO DEPENDANCES FOUND *** <br>";

}

$patches=patches($release,$v['code']);


foreach($patches as $pat){
$url=patch_url($release,$pat);
$pats[]="<a href=\"$url\">$pat</a>";
}


if(count($patches)){
echo "Patches: ".join($pats,", ").".<br>";
}else{
echo "Patches: *** NO PATCHES FOUND *** <br>";
}


echo "Configuration script: 
<br><pre>".configuration_script($v['configure'])."</pre><br>";
echo "Build script: 
<br><pre>".build_script($v['build'])."</pre><br>";
echo "Install script: 
<br><pre>".install_script($v['install'])."</pre><br>";



//echo "Available packages: <table>".join ($pkgs)."</table>";

}
include "inc/template.php";

