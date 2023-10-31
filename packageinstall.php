<?php

include "inc/main.site.php";
ob_end_clean();
$release=@addslashes($_REQUEST['release']);
$package=@addslashes($_REQUEST['package']);
$arch=@addslashes($_REQUEST['arch']);


$localinstall=false;
if(@$_REQUEST['type']=="local"){
$localinstall=true;
}

if($arch){
$sql="
select p.id, r.`release`, p.code, p.sourcefile, p.sourcedir, p.unpack, ap.configure, ap.build, ap.install
from packages p left join releases r on p.release=r.id 
left join packagesfiles_packages pf_p on pf_p.package=p.id 
left join packagesfiles pf on pf.id=pf_p.packagefile 
left join architectures_packages ap on ap.package=p.id 
left join architectures a on ap.architecture=a.id
where r.`release`=\"$release\" and p.code=\"$package\" and a.code=\"$arch\"";
}else{
$sql="select p.id, r.`release`, code, sourcefile, sourcedir, configure, unpack, build, install from packages p
left join releases r on p.release=r.id
where r.`release`=\"$release\" and p.code=\"$package\"";
}

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
$purl=patch_path($release,$pat['filename']);
}else{
$purl=patch_url($release,$pat['filename']);
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

$nestings=nestings($release, $v['code']);


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

echo "echo \"ULFS Package installation start\"\n";
echo "echo \"===============================\"\n";
echo "echo \"Package: $package\" \n";
echo "echo \"Release: $release\" \n";
echo "\n";


echo "#default values\n";
echo "ULFS_PKG_DOCUMENTATION=YES\n";
echo "ULFS_PKG_STATIC=NO\n";
echo "ULFS_CONFIG_FILE=/etc/ulfs-packages/config\n";


echo "echo \"checking config file\"\n";
echo "if [ -f \$ULFS_CONFIG_FILE ]\n";
echo "then\n";
echo "echo \"loading config file\"\n";
echo ". \$ULFS_CONFIG_FILE\n";
echo "fi\n";




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
echo "           cat $localpath/packages/$release/$dep.sh | bash\n";
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

if($v['unpack']){
echo "#Extracting source package with previously defined commands...\n";
echo unpack_script($v['unpack'])."\n";
}else{

if(preg_match("/zip$/",$v['sourcefile'])){
echo "#Extracting zip source package archive with default parameters...\n";
echo "unzip ".$v['sourcefile']." -d ".$v['sourcedir']."\n";

}else{
echo "#Extracting tar source package archive with default parameters...\n";
echo "tar -xf ".$v['sourcefile']."\n";
}
}

echo "#Checking package directory size after unpack...\n";
echo "du -s ".$v['sourcedir']." | awk 'NR==1 {print $1}' > ".$packagelogdir."unpack.size \n";




echo "#Going to source package directory...\n";
echo "cd ".$v['sourcedir']."\n";

if(count($patches)){
echo "#Applying patches...\n";
foreach ($patches as $pat){
if($pat['mode']==="0"){
echo "patch -Np0 -i ../".$pat['filename']."\n";
}else{
echo "patch -Np1 -i ../".$pat['filename']."\n";
}
}
}

}
echo "#Saving configuration timestamp\n";
echo "date +%s > ".$packagelogdir."configure.time\n";

echo "#Running configuration script...\n";
if($release=="0.1"){
echo configuration_script($v['configure'])."\n";
}else{
echo "cat > ulfs_configure.sh << EOIS\n";
echo configuration_script($v['configure'])."\n";
echo "EOIS\n";
echo "cat ulfs_configure.sh | bash | tee ".$packagelogdir."configure.log \n";
}

echo "#Saving build timestamp\n";
echo "date +%s > ".$packagelogdir."build.time\n";

echo "#Running build script...\n";
if($release=="0.1"){
echo build_script($v['build'])."\n";
}else{
echo "cat > ulfs_build.sh << EOIS\n";
echo build_script($v['build'])."\n";
echo "EOIS\n";
echo "cat ulfs_build.sh | bash | tee ".$packagelogdir."build.log \n";
}

echo "#Saving install timestamp\n";
echo "date +%s > ".$packagelogdir."install.time\n";

echo "#Changing all files creation time in source directory to find them\n";
echo "find /sources/".$v['sourcedir']." -exec touch -m {} +\n";

echo "#Running install script...\n";
echo "cat > ulfs_install.sh << EOIS\n";
echo install_script($v['install'])."\n";
echo "EOIS\n";

echo "USER=`whoami`\n";
echo "if [ \"\$USER\" == \"root\" ] ; then \n";
echo "cat ulfs_install.sh | bash | tee ".$packagelogdir."install.log \n";
echo "else\n";
echo "cat ulfs_install.sh | sudo bash | tee ".$packagelogdir."install.log \n";
echo "fi\n";


echo "#Saving finish timestamp\n";
echo "date +%s > ".$packagelogdir."finish.time\n";

echo "#Checking package directory size after unpack...\n";
echo "cd /sources \n";
echo "du -s ".$v['sourcedir']." | awk 'NR==1 {print $1}' > ".$packagelogdir."install.size \n";

echo "echo \"ULFS package installation completed.\"\n";

echo "#Producing files list\n";
echo "echo \"Looking for installed files...\"\n";
echo "rm ".$packagelogdir."files.txt\n";
echo "USER=`whoami`\n";
echo "if [ \"\$USER\" == \"root\" ] ; then \n";
echo "find /bin -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /sbin -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /usr -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /etc -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /opt -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /lib -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /lib64 -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /var -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";

echo "else\n";

echo "sudo find /bin -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /sbin -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /usr -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /etc -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /opt -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /lib -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /lib64 -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /var -type f -newer ".$packagelogdir."install.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";

echo "fi\n";

echo "#Marking package as installed...\n";
echo "mkdir -p $packagesdir\n";

echo "touch $packagesdir/".$v['code']."\n";
foreach($nestings as $nesting){
	echo "touch $packagesdir/".$nesting."\n";
}

echo "#Calculate delta size\n";
echo "a=`cat ".$packagelogdir."unpack.size`\n";
echo "b=`cat ".$packagelogdir."install.size`\n";
echo "c=\$((\$b-\$a))\n";
echo "echo \$c > ".$packagelogdir."delta.size \n";


echo "#Calculate prepare time\n";
echo "a=`cat ".$packagelogdir."start.time`\n";
echo "b=`cat ".$packagelogdir."configure.time`\n";
echo "dp=\$((\$b-\$a))\n";

echo "#Calculate download time\n";
echo "a=`cat ".$packagelogdir."download.time`\n";
echo "b=`cat ".$packagelogdir."unpack.time`\n";
echo "dd=\$((\$b-\$a))\n";

echo "#Calculate delta time\n";
echo "a=`cat ".$packagelogdir."configure.time`\n";
echo "b=`cat ".$packagelogdir."finish.time`\n";
echo "db=\$((\$b-\$a))\n";
echo "echo \$db > ".$packagelogdir."delta.time \n";



echo "#Report\n";
echo "echo \"\"\n";
echo "echo \"ULFS Package installation report\"\n";
echo "echo \"================================\"\n";
echo "echo \"Package: $package\" \n";
echo "echo \"Release: $release\" \n";
echo "echo \"Build size: \$c\" \n";
echo "echo \"Prepare time: \$dp sec.\" \n";
echo "echo \"Download time: \$dd sec.\" \n";
echo "echo \"Build time: \$db sec.\" \n";
echo "\n#End of script\n";

}

