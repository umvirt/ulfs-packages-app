<?php
include "classes/dirparser.php";

if(@$config['debug']){
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);
}


function gitHead($path){
$ref=trim(file_get_contents("$path/.git/HEAD"));
$file=str_replace('ref: ','',$ref);
//echo $file;
return trim(file_get_contents("$path/.git/$file"));
}


DEFINE('APPDIR',dirname(dirname(__file__)).'/');
DEFINE('INCDIR',APPDIR.'/inc/');
DEFINE('APPCOMMIT',gitHead(APPDIR));
//var_dump(APPCOMMIT);exit;
//echo APPDIR;exit;
include INCDIR."config.php";
include INCDIR."db.php";
include INCDIR."ulfs.php";

$db=new db_connection($db_config);



//version safe join function
function strjoin($array, $delimeter=""){
if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
return join($delimeter, $array);
}else{
return join($array, $delimeter);
}
}





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


