<?php
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);

DEFINE('APPDIR',dirname(dirname(__file__)).'/');
DEFINE('INCDIR',APPDIR.'/inc/');
//echo APPDIR;exit;
include INCDIR."config.php";
include INCDIR."db.php";
$db=new db_connection($db_config);

// Returns a file size limit in bytes based on the PHP upload_max_filesize
// and post_max_size
function file_upload_max_size() {
  static $max_size = -1;

  if ($max_size < 0) {
    // Start with post_max_size.
    $post_max_size = parse_size(ini_get('post_max_size'));
    if ($post_max_size > 0) {
      $max_size = $post_max_size;
    }

    // If upload_max_size is less, then reduce. Except if upload_max_size is
    // zero, which indicates no limit.
    $upload_max = parse_size(ini_get('upload_max_filesize'));
    if ($upload_max > 0 && $upload_max < $max_size) {
      $max_size = $upload_max;
    }
  }
  return $max_size;
}

function parse_size($size) {
  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
  if ($unit) {
    // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
  }
  else {
    return round($size);
  }
}

function configuration_script($v=""){
if(!$v){
return "./configure --prefix=/usr";
}else{
//\r\n\ -> \n
$v=str_replace("\r\n","\n",$v);

return $v;
}
}

function build_script($v=""){
if(!$v){
return "make";
}else{
//\r\n\ -> \n
$v=str_replace("\r\n","\n",$v);

return $v;
}
}

function install_script($v=""){
if(!$v){
return "make install";
}else{
//\r\n\ -> \n
$v=str_replace("\r\n","\n",$v);
return $v;
}
}

function download_url($release,$file){
$x=file_exists("../downloads/$release/packages/python-modules/$file");
//var_dump($x);
if($x){
return "http://umvirt.com/linux/downloads/$release/packages/python-modules/".$file;
}
$x=file_exists("../downloads/$release/packages/Xorg/$file");
//var_dump($x);
if($x){
return "http://umvirt.com/linux/downloads/$release/packages/Xorg/".$file;
}

$x=file_exists("../downloads/$release/packages/Xorg/lib/$file");
//var_dump($x);
if($x){
return "http://umvirt.com/linux/downloads/$release/packages/Xorg/lib/".$file;
}

$dir=strtolower($file[0]);
return "http://umvirt.com/linux/downloads/$release/packages/".$dir."/".$file;

}

function patch_url($release,$file){
return "http://umvirt.com/linux/downloads/$release/patches/$file";
}

function dependances($release,$package){
global $db;
$sql="select dp.code, dd.code dependance from dependances d
inner join packages dp on d.package=dp.id
inner join packages dd on d.dependance=dd.id 
inner join `releases` r on r.id=dp.release 
where dp.code=\"$package\" and r.release=\"$release\"";
//var_dump($sql);
$db->execute($sql);
$deps=array();
$x=$db->dataset;
foreach($x as $k=>$v){
	$deps[]=$v['dependance'];
}
return $deps;

}

function patches($release,$package){

global $db;
$sql="select p.filename from patches p
inner join packages pp on p.package=pp.id
inner join `releases` r on r.id=pp.release 
where pp.code=\"$package\" and r.release=\"$release\"";
//var_dump($sql);
$db->execute($sql);
$deps=array();
$x=$db->dataset;
foreach($x as $k=>$v){
        $deps[]=$v['filename'];
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

