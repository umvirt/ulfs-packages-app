<?php

function configuration_script($v="",$release=""){
if(!$v){
return "./configure --prefix=/usr";
}else{
if($release != "0.1"){
$v=str_replace('\\','\\\\',$v);
}
//$ -> \$
$v=str_replace('$','\$',$v);
//\r\n\ -> \n
$v=str_replace("\r\n","\n",$v);


return $v;
}
}

function unpack_script($v=""){
$v=str_replace("\r\n","\n",$v);
return $v;
}


function build_script($v="",$release=""){
if(!$v){
return "make";
}else{
if($release!="0.1"){
$v=str_replace('\\','\\\\',$v);
}
//$ -> \$
$v=str_replace('$','\$',$v);
//\r\n\ -> \n
$v=str_replace("\r\n","\n",$v);

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
return $basepath."$release/packages/".$dir."/".$file;

}

function patch_url($release,$file){
global $config;
return getpatchpath ($release,$file,$config['downloads_url']);
}


function patch_path($release,$file){
global $config;
return getpatchpath ($release,$file,$config['localpath']."/files/");
}


function getpatchpath($release,$file,$basepath){
return $basepath."$release/patches/$file";
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

