<?php

include "inc/main.site.php";
ob_end_clean();
$release=@addslashes($_REQUEST['release']);
$package=@addslashes($_REQUEST['package']);

$localinstall=false;
if(@$_REQUEST['type']=="local"){
$localinstall=true;
}

$sql="select p.id, r.`release`, code, sourcefile, sourcedir, configure, build, install from packages p
left join releases r on p.release=r.id
where r.`release`=\"$release\" and p.code=\"$package\"";

//var_dump($sql);
$db->execute($sql);

$x=$db->dataset;

$pkgs=array();
foreach ($x as $k=>$v){
$url=download_url($release, $v['sourcefile']);
$filepath=file_path($release, $v['sourcefile']);

$patches=patches($release,$v['code']);


foreach($patches as $pat){
if($localinstall){
$purl=patch_path($release,$pat);
}else{
$purl=patch_url($release,$pat);
}
$pats[]=$purl;
}

$addons=addons($release,$v['code']);


foreach($addons as $addn){
if($localinstall){
$aurl=file_path($release,$addn);
}else{
$aurl=download_url($release,$addn);
}
$addns[]=$aurl;
}



$dependances=dependances($release, $v['code']);

$packagesdir="/var/cache/ulfs-packages";
$packagelogdir="/var/log/ulfs-packages/".$package."/";
$installurl=$config['packages_url'];
$localpath=$config['localpath'];

$mode="Network mode.";
if($localinstall){
$mode="Local mode.";
}

header("Content-type: text/plain");
echo "#!/bin/bash\n";
echo "#===========================\n";
echo "# UMVIRT LINUX FROM SCRATCH \n";
echo "#===========================\n";
echo "# Compilation script.\n" ;
echo "# $mode \n";
echo "#===========================\n";
echo "# Release: $release\n";
echo "# Package: $package\n";
echo "#===========================\n\n";


echo "#Creating log directory\n";
echo "mkdir -p $packagelogdir\n";
echo "#Saving start timestamp\n";
echo "date +%s > ".$packagelogdir."start.time\n";
echo "#Going to source directory...\n";
echo "cd /sources\n";

//echo "checking installation...";

if(count($dependances)){
echo "#Checking dependances...\n";
foreach($dependances as $dep){
echo "      #Checking $dep...\n";
echo "      if [ ! -f $packagesdir/$dep ]; then\n";
echo "           echo \"Dependance \\\"$dep\\\" not found. Trying to install...\";\n";
if($localinstall){
echo "           cat $localpath/packages/$release/$dep.sh -O - | bash\n";
}else{
echo "           wget --no-check-certificate $installurl/$release/$dep/install -O - | bash\n";
}
echo "           if [ ! -f $packagesdir/$dep ]; then\n";
echo "	             echo \"Dependance \\\"$dep\\\" is not installed. Exiting...\"\n";
echo "               exit\n";
echo "           fi\n";
echo "      fi\n";


}
}

if($v['sourcefile']){
echo "#Saving downloading timestamp\n";
echo "date +%s > ".$packagelogdir."download.time\n";
if($localinstall){
echo "#Copying source package archive...\n";
echo "cp $filepath . \n";
}else{
echo "#Downloading source package archive...\n";
echo "wget --no-check-certificate -nc $url\n";
}
}

if(count($addons)){
foreach ($addns as $addn){
if($localinstall){
echo "#Copying add-ons...\n";
echo "cp $addn .\n";
}else{
echo "#Downloadning add-ons...\n";
echo "wget --no-check-certificate -nc $addn\n";
}
}
}



if(count($patches)){
foreach ($pats as $pat){
if($localinstall){
echo "#Copying patches...\n";
echo "cp $pat .\n";

}else{
echo "#Downloadning patches...\n";
echo "wget --no-check-certificate -nc $pat\n";
}

}
}

if($v['sourcefile']){
echo "#Saving extracting timestamp\n";
echo "date +%s > ".$packagelogdir."unpack.time\n";

if(preg_match("/zip$/",$v['sourcefile'])){
echo "#Extracting zip source package archive...\n";
echo "unzip ".$v['sourcefile']." -d ".$v['sourcedir']."\n";

}else{
echo "#Extracting tar source package archive...\n";
echo "tar -xf ".$v['sourcefile']."\n";
}

echo "#Going to source package directory...\n";
echo "cd ".$v['sourcedir']."\n";

if(count($patches)){
echo "#Applying patches...\n";
foreach ($patches as $pat){
echo "patch -Np1 -i ../$pat\n";
}
}

}
echo "#Saving configuration timestamp\n";
echo "date +%s > ".$packagelogdir."configure.time\n";

echo "#Running configuration script...\n";
echo configuration_script($v['configure'])."\n";

echo "#Saving build timestamp\n";
echo "date +%s > ".$packagelogdir."build.time\n";

echo "#Running build script...\n";
echo build_script($v['build'])."\n";

echo "#Saving install timestamp\n";
echo "date +%s > ".$packagelogdir."install.time\n";

echo "#Running install script...\n";
echo install_script($v['install'])."\n";

echo "#Marking package as installed...\n";
echo "mkdir -p $packagesdir\n";
echo "touch $packagesdir/".$v['code']."\n";

echo "#Saving finish timestamp\n";
echo "date +%s > ".$packagelogdir."finish.time\n";


/*
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
$depends[]="<a href=".$_SERVER['SCRIPT_URI']."?release=$release&package=$dep>$dep</a>";
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
*/

}



