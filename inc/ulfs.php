<?php

function scriptslashes($v,$release=""){
if($release != "0.1"){
$v=str_replace('\\','\\\\',$v);
}
//$ -> \$
$v=str_replace('$','\$',$v);
//\r\n\ -> \n
$v=str_replace("\r\n","\n",$v);

return $v;
}

function configuration_script($v=""){
if(!$v){
return "./configure --prefix=/usr";
}else{
return $v;
}
}

function unpack_script($v=""){
$v=str_replace("\r\n","\n",$v);
return $v;
}


function build_script($v=""){
if(!$v){
return "make";
}else{
return $v;
}


}

function install_script($v=""){
if(!$v){
return "make install";
}else{
//$ -> \$
$v=str_replace('$','\$',$v);
//\r\n\ -> \n
$v=str_replace("\r\n","\n",$v);
return $v;
}
}

function download_url($release,$file){
global $config;

return getpath($release,$file, $config['downloads_url']);
}

function file_path($release,$file){
global $config;
return getpath($release,$file, $config['localpath']."/files/");
}

function getpath($release,$file,$basepath){
global $config;

$x=file_exists($config['filespath']."/$release/packages/python-modules/$file");
//var_dump($x);
if($x){
return $basepath."$release/packages/python-modules/".$file;
}
$x=file_exists($config['filespath']."/$release/packages/perl-modules/$file");
//var_dump($x);
if($x){
return $basepath."$release/packages/perl-modules/".$file;
}
$x=file_exists($config['filespath']."/$release/packages/Xorg/$file");
//var_dump($x);
if($x){
return  $basepath."$release/packages/Xorg/".$file;
}
$x=file_exists($config['filespath']."/$release/packages/Xorg/lib/$file");
//var_dump($x);
if($x){
return  $basepath."$release/packages/Xorg/lib/".$file;
}

$x=file_exists($config['filespath']."/$release/packages/Xorg/app/$file");
//var_dump($x);
if($x){
return  $basepath."$release/packages/Xorg/app/".$file;
}

$x=file_exists($config['filespath']."/$release/packages/Xorg/font/$file");
//var_dump($x);
if($x){
return  $basepath."$release/packages/Xorg/font/".$file;
}

$x=file_exists($config['filespath']."/$release/packages/games/$file");
//var_dump($x);
if($x){
return  $basepath."$release/packages/games/".$file;
}

$dir=strtolower($file[0]); 
if(file_exists($config['filespath']."/$release/packages/".$dir."/".$file)){
return $basepath."$release/packages/".$dir."/".$file;
}

return "";
}

function patch_url($release,$file){
global $config;
return $config['downloads_url']."$release/patches/$file";
}


function patch_path($release,$file){
global $config;
if(file_exists($config['filespath']."/$release/patches/$file")){
return $config['filespath']."/$release/patches/$file";
}
return "";

}


function dependances($release,$package){
global $db;
$sql="select dp.code, dd.code dependance, d.weight from dependances d
inner join packages dp on d.package=dp.id
inner join packages dd on d.dependance=dd.id 
inner join `releases` r on r.id=dp.release 
where dp.code=\"$package\" and r.release=\"$release\"

order by d.weight, d.dependance
";
//var_dump($sql);
$db->execute($sql);
$deps=array();
$x=$db->dataset;
foreach($x as $k=>$v){
	$deps[]=array(
	"code"=>$v['dependance'],
	"weight"=>$v['weight']
	);
}
return $deps;

}

function dependanceOf($release,$package){
global $db;
$sql="select dp.code package, dd.code, d.weight from dependances d
inner join packages dp on d.package=dp.id
inner join packages dd on d.dependance=dd.id 
inner join `releases` r on r.id=dp.release 
where dd.code=\"$package\" and r.release=\"$release\"

order by d.weight, d.dependance
";
//var_dump($sql);
$db->execute($sql);
$deps=array();
$x=$db->dataset;
foreach($x as $k=>$v){
        $deps[]=array(
        "code"=>$v['package'],
        "weight"=>$v['weight']
        );
}
return $deps;

}


function patches($release,$package){

global $db;
$sql="select p.filename,p.mode from patches p
inner join packages pp on p.package=pp.id
inner join `releases` r on r.id=pp.release 
where pp.code=\"$package\" and r.release=\"$release\"";
//var_dump($sql);
$db->execute($sql);
$deps=array();
$x=$db->dataset;
foreach($x as $k=>$v){
        $deps[]=array("filename"=>$v['filename'],"mode"=>$v['mode']);
}
return $deps;

}

function addons($release,$package){

global $db;
$sql="select a.filename from addons a
inner join packages p on a.package=p.id
inner join `releases` r on r.id=p.release 
where p.code=\"$package\" and r.release=\"$release\"";
//var_dump($sql);
$db->execute($sql);
$deps=array();
$x=$db->dataset;
foreach($x as $k=>$v){
        $deps[]=$v['filename'];
}
return $deps;

}


function nestings($release,$package){
global $db;
$sql="select dp.code, dd.code child from nestings d
inner join packages dp on d.parent=dp.id
inner join packages dd on d.child=dd.id 
inner join `releases` r on r.id=dp.release 
where dp.code=\"$package\" and r.release=\"$release\"
";
//var_dump($sql);
$db->execute($sql);
$deps=array();
$x=$db->dataset;
foreach($x as $k=>$v){
        $deps[]=$v['child'];
}
return $deps;

}


function comments($release,$package){
global $db;
$sql="select p.code, c.text text from comments c
inner join packages p on c.package=p.id
inner join `releases` r on r.id=p.release 
where p.code=\"$package\" and r.release=\"$release\"
";
//var_dump($sql);
$db->execute($sql);
$deps=array();
$x=$db->dataset;
foreach($x as $k=>$v){
        $deps[]=base64_encode($v['text']);
}
return $deps;

}


function distributedBuildInit($localbuild=0){

$s= "";

if(!$localbuild){
$s.= "echo \"Initializing distributed build environment... \"\n";
$s.= "if [[ \"\$ULFS_ICECC\" == \"YES\" ]]\n";
$s.= "then\n";
$s.= "    export PATH=\"\$ULFS_ICECC_PATH:\$PATH\"\n";
$s.= "    echo \"ICECC\"\n";
$s.= "fi\n";
$s.= "\n";
}

$s.= "echo \"Environment debug...\"\n";
$s.= "echo \"PATH: \$PATH\"\n";
$s.= "echo \"MAKEFLAGS: \$MAKEFLAGS\"\n";
$s.= "echo \"NINJAJOBS: \$NINJAJOBS\"\n";
$s.= "env | grep ULFS\n";
$s.= "\n\n";

return $s;

}


function loadConfig(){
echo "echo \"checking config file\"\n";
echo "if [ -f \$ULFS_CONFIG_FILE ]\n";
echo "then\n";
echo "echo \"loading config file \$ULFS_CONFIG_FILE...\"\n";
echo ". \$ULFS_CONFIG_FILE\n";
echo "fi\n";
}
