<?php

include "inc/main.site.php";
ob_end_clean();
$release=@addslashes($_REQUEST['release']);
$package=@addslashes($_REQUEST['package']);
$arch=@addslashes($_REQUEST['arch']);


//extract arch from packagename
if(strpos($package,':')){
$matches=array();
//preg_match('/:([a-zA-z0-9.-]+)$/',$package,$matches);
//$arch=matches[1];
$matches=explode(":",$package);
//var_dump($matches);
if(count($matches)==2){
$package=$matches[0];
$arch=$matches[1];
}

}

$localinstall=false;
if(@$_REQUEST['type']=="local"){
$localinstall=true;
}

if($arch){
$sql="
select p.id, r.`release`, r.`commit` releasedbcommit,p.code, p.sourcefile, p.sourcedir, p.unpack, ap.configure, ap.build, ap.install
from packages p 
left join releases r on p.release=r.id 
left join architectures_packages ap on ap.package=p.id 
left join architectures a on ap.architecture=a.id
where r.`release`=\"$release\" and p.code=\"$package\" and a.code=\"$arch\"";
}else{
$sql="select p.id, r.`release`, r.`commit` releasedbcommit, p.code, p.sourcefile, p.sourcedir, p.configure, p.unpack, p.build, p.install,p.localbuild,
p.template template_id, t.code template, t.configure template_configure, t.build template_build, t.install template_install
from packages p
left join releases r on p.release=r.id
left join packages_templates t on t.id=p.template
where r.`release`=\"$release\" and p.code=\"$package\"";
}

//var_dump($sql);
$db->execute($sql);

$x=$db->dataset;

$pkgs=array();
foreach ($x as $k=>$v){
$url="";
$filepath="";
if($v['sourcefile']){
$url=download_url($release, $v['sourcefile']);
$filepath=file_path($release, $v['sourcefile']);
}
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

//Use packagename from database instead from user 
$package_=$package;
$package=$v['code'];

//If arch defined
if($arch){
	//Append arch code to package name to separate arch specific packages
	$package=$package.":".$arch;
}

if($arch){
$dependances1=dependances($release, $v['code']);
$dependances2=archpkgdependances($release,$arch,$v['code']);
$dependances=array_merge($dependances1,$dependances2);

}else{
$dependances=dependances($release, $v['code']);
}



//var_dump($dependances);


//exit;


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
if($arch){
echo "# Architecture: $arch\n";
}
echo "#===========================\n";
echo "# DB commit: ".$v['releasedbcommit']."\n";
echo "# APP commit: ".APPCOMMIT."\n";
echo "#===========================\n\n";

echo "echo \"ULFS Package installation start\"\n";
echo "echo \"===============================\"\n";
echo "echo \"Package: $package\" \n";
echo "echo \"Release: $release\" \n";
if($arch){
echo "echo \"Architecture: $arch\" \n";
}

echo "\n";


/*
echo "#default values\n";
echo "ULFS_PKG_DOCUMENTATION=YES\n";
echo "ULFS_PKG_STATIC=NO\n";
echo "ULFS_CONFIG_FILE=/etc/ulfs-packages/config\n";
*/

//load config
loadConfig();



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
if($dependance['arch']){
$dep=$dependance['code'].':'.$dependance['arch'];
}else{
$dep=$dependance['code'];
}


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

if($v['sourcefile'] or (!$v['sourcefile'] and $v['sourcedir']=="kernel")){

if($v['sourcefile']){

//echo "if [ -f /sources/.cleanup ]; then\n";
echo "    #Saving cleanup timestamp\n";
echo "    date +%s > ".$packagelogdir."cleanup.time\n";

echo      "rm -rfv /sources/".$v['sourcedir']."/\n";
//echo "fi\n";

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

}



if(!$v['sourcefile'] and $v['sourcedir']=="kernel"){

switch ($v['sourcedir']){
case "kernel":

	echo "#Going to Linux kernel source directory...\n";
	echo "cd /usr/src/linux-`uname -r`/\n";

break;

}

}else{

echo "#Going to source package directory...\n";
echo "cd ".$v['sourcedir']."\n";

}

if(count($patches)){
echo "#Applying patches...\n";
foreach ($patches as $pat){
$plevel="1";

if($pat['mode']){
$plevel=$pat['mode'];
}

echo "patch -Np".$pat['mode']." -i ../".$pat['filename']."\n";

}
}

}
echo "#Saving configuration timestamp\n";
echo "date +%s > ".$packagelogdir."configure.time\n";

if($v['sourcefile'] or (!$v['sourcefile'] and $v['sourcedir']=="kernel")){


echo "#Sleep 1 second\n";
echo "sleep 1\n";


if($release!="0.1"){

echo "if [[ \"\$ULFS_PKG_DATERESET\" == \"YES\" ]]\n";
echo "then\n";

echo "#Changing all files creation time (except build configuration files) in source directory to find them after installation\n";

//default
$dateresetskip=Array(
"*/configure*","*/Makefile*","*.make","*.m4","*.am","*.mk"
);
//nettle
$dateresetskip[]="*.stamp";
//grub
$dateresetskip[]="*gentpl.py";

$skip="";
foreach($dateresetskip as $dss){
$skip.="\! -path \"$dss\" ";
}

echo "find /sources/".$v['sourcedir']." $skip -exec touch -m {} +\n";


echo "fi\n";
}

echo "#Running configuration script...\n";
$configure="";
if($release!="0.1"){
$configure.=scriptslashes(loadConfig(),$release);
$configure.=scriptslashes(distributedBuildInit($v['localbuild']),$release);
}
$configure.=scriptslashes(configuration_script($v),$release);

if($release=="0.1"){
echo $configure."\n";
}else{
echo "cat > ulfs_configure.sh << EOIS\n";
echo $configure."\n";
echo "EOIS\n";
echo "cat ulfs_configure.sh | bash 2>&1 | tee ".$packagelogdir."configure.log \n";
}

}
echo "#Saving build timestamp\n";
echo "date +%s > ".$packagelogdir."build.time\n";

if($v['sourcefile'] or (!$v['sourcefile'] and $v['sourcedir']=="kernel")){

echo "#Running build script...\n";
$build="";
if($release!="0.1"){
$build.=scriptslashes(loadConfig(),$release);
$build.=scriptslashes(distributedBuildInit($v['localbuild']),$release);
}
$build.=scriptslashes(build_script($v),$release);

if($release=="0.1"){
echo $build."\n";
}else{
echo "cat > ulfs_build.sh << EOIS\n";
echo $build."\n";
echo "EOIS\n";
//echo "cat ulfs_build.sh | bash 2>".$packagelogdir."build.err | tee ".$packagelogdir."build.log \n";

echo "cat ulfs_build.sh | bash 2>&1 | tee ".$packagelogdir."build.log \n";
}

}

echo "#Saving install timestamp\n";
echo "date +%s > ".$packagelogdir."install.time\n";


echo "#Running install script...\n";

if($v['sourcefile']  or (!$v['sourcefile'] and $v['sourcedir']=="kernel")){

echo "cat > ulfs_install.sh << EOIS\n";
echo scriptslashes(install_script($v),$release)."\n";
echo "EOIS\n";

//update linker directories
echo "echo \"/sbin/ldconfig\" >> ulfs_install.sh\n";

echo "USER=`whoami`\n";
echo "if [ \"\$USER\" == \"root\" ] ; then \n";
echo "cat ulfs_install.sh | bash 2>&1 | tee ".$packagelogdir."install.log \n";
echo "else\n";
echo "cat ulfs_install.sh | sudo bash 2>&1 | tee ".$packagelogdir."install.log \n";
echo "fi\n";

} else {

echo install_script($v)."\n";

}


echo "#Saving finish timestamp\n";
echo "date +%s > ".$packagelogdir."finish.time\n";


if($v['sourcefile']){

echo "#Checking package directory size after unpack...\n";
echo "cd /sources \n";
echo "du -s ".$v['sourcedir']." | awk 'NR==1 {print $1}' > ".$packagelogdir."install.size \n";

echo "echo \"ULFS package installation completed.\"\n";

}

if($v['sourcefile']  or (!$v['sourcefile'] and $v['sourcedir']=="kernel")){

echo "#Producing files list\n";
echo "echo \"Looking for installed files...\"\n";
echo "if [  -f ".$packagelogdir."files.txt ]; then\n";
echo "rm ".$packagelogdir."files.txt\n";
echo "fi\n";


$skipdir=str_replace('//','/',"$packagelogdir/*");
echo "USER=`whoami`\n";
echo "if [ \"\$USER\" == \"root\" ] ; then \n";
echo "find /bin -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /sbin -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /usr -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /etc -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time \! -path /etc/ld.so.cache >> ".$packagelogdir."files.txt\n";
echo "find /opt -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /lib -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /lib64 -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "find /var -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time \! -path \"$skipdir\" \! -path /var/cache/ldconfig/aux-cache >> ".$packagelogdir."files.txt\n";

echo "else\n";

echo "sudo find /bin -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /sbin -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /usr -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /etc -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time \! -path /etc/ld.so.cache >> ".$packagelogdir."files.txt\n";
echo "sudo find /opt -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /lib -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /lib64 -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time >> ".$packagelogdir."files.txt\n";
echo "sudo find /var -type f -newer ".$packagelogdir."configure.time \! -newer ".$packagelogdir."finish.time \!  -path \"$skipdir\"  \! -path /var/cache/ldconfig/aux-cache >> ".$packagelogdir."files.txt\n";

echo "fi\n";

}

echo "#Marking package as installed...\n";
echo "mkdir -p $packagesdir\n";

echo "USER=`whoami`\n";
echo "if [ \"\$USER\" == \"root\" ] ; then \n";


echo "touch $packagesdir/".$package."\n";
foreach($nestings as $nesting){
	echo "touch $packagesdir/".$package."\n";
}

echo "else\n";

echo "sudo touch $packagesdir/".$package."\n";
foreach($nestings as $nesting){
        echo "sudo touch $packagesdir/".$package."\n";
}

echo "fi\n";


if($v['sourcefile']){

echo "#Calculate delta size\n";
echo "a=`cat ".$packagelogdir."unpack.size`\n";
echo "b=`cat ".$packagelogdir."install.size`\n";
echo "c=\$((\$b-\$a))\n";
echo "echo \$c > ".$packagelogdir."delta.size \n";

}

echo "#Calculate prepare time\n";
echo "a=`cat ".$packagelogdir."start.time`\n";
echo "b=`cat ".$packagelogdir."configure.time`\n";
echo "dp=\$((\$b-\$a))\n";


if($v['sourcefile']){

echo "#Calculate download time\n";
echo "a=`cat ".$packagelogdir."download.time`\n";
echo "b=`cat ".$packagelogdir."unpack.time`\n";
echo "dd=\$((\$b-\$a))\n";

}

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

if($v['sourcefile']){

echo "echo \"Build size: \$c\" \n";

}
echo "echo \"Prepare time: \$dp sec.\" \n";

if($v['sourcefile']){

echo "echo \"Download time: \$dd sec.\" \n";

}

echo "echo \"Build time: \$db sec.\" \n";
echo "\n#End of script\n";

}

