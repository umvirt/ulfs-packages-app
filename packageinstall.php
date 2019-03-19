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
foreach($dependances as $dependance){
$dep=$dependance['code'];
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
echo "cp $filepath.md5sum . \n";
echo "cp $filepath . \n";

}else{
echo "#Downloading source package archive...\n";
echo "wget --no-check-certificate -nc $url.md5sum\n";
echo "wget --no-check-certificate -nc $url\n";
}


echo "#Checking source package file existance\n";
echo "if [ ! -f ".$v['sourcefile']." ]; then\n";
echo "echo \"Error: Can't find ".$v['sourcefile'].". Exiting!\"\n";
echo "exit\n";
echo "fi\n";


echo "#Checking source package file checksum\n";
echo "if [ -f ".$v['sourcefile'].".md5sum ]; then\n";
echo "    MD5=`LANG=C md5sum -c ".$v['sourcefile'].".md5sum | grep OK`\n";
echo "    if [ \"\$MD5\" == \"\" ] ; then\n";
echo "    echo \"Error: Checksum of ".$v['sourcefile']." is wrong. Exiting!\"\n";
echo "    exit\n";
echo "    fi\n";
echo "fi\n";




}

if(count($addons)){

if($localinstall){
echo "#Copying add-ons...\n";
}else{
echo "#Downloadning add-ons...\n";
}

foreach ($addns as $addn){
$addfile=basename($addn);

if($localinstall){
echo "#Copying add-on \"$addfile\"...\n";
echo "cp $addn.md5sum .\n";
echo "cp $addn .\n";
}else{
echo "#Downloadning add-on  \"$addfile\"...\n";
echo "wget --no-check-certificate -nc $addn.md5sum\n";
echo "wget --no-check-certificate -nc $addn\n";
}

echo "#Checking addon file existance\n";
echo "if [ ! -f $addfile ]; then\n";
echo "echo \"Error: Can't find $addfile. Exiting!\"\n";
echo "exit\n";
echo "fi\n";


echo "#Checking add-on file checksum\n";
echo "if [ -f $addfile.md5sum ]; then\n";
echo "    MD5=`LANG=C md5sum -c $addfile.md5sum | grep OK`\n";
echo "    if [ \"\$MD5\" == \"\" ] ; then\n";
echo "    echo \"Error: Checksum of $addfile is wrong. Exiting!\"\n";
echo "    exit\n";
echo "    fi\n";
echo "fi\n";

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

echo "if [ -f /sources/.cleanup ]; then\n";
echo "    #Saving cleanup timestamp\n";
echo "    date +%s > ".$packagelogdir."cleanup.time\n";

echo      "rm -rfv /sources/".$v['sourcedir']."/\n";
echo "fi\n";

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

echo "#Saving finish timestamp\n";
echo "date +%s > ".$packagelogdir."finish.time\n";

echo "#Producing files list\n";
echo "find / -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time | grep \"^/bin/\\|/usr/\\|^/etc/\\|^/opt/\" > ".$packagelogdir."files.txt\n";

echo "#Marking package as installed...\n";
echo "mkdir -p $packagesdir\n";
echo "touch $packagesdir/".$v['code']."\n";


echo "\n#End of script\n";
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



