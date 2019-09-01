#!/usr/bin/env php
<?php

/*include "../inc/main.php";
$sql="select id, `release` from releases";
$db->execute($sql);

foreach($db->dataset as $v){
echo "Processing release \"".$v['release']."\"... ";
}
*/
//exit;


class dirparser{

var $dir;
var $ignorefiles;
var $size=0;

function checkfilename($file, $patterns){
	$chk=false;
	foreach($patterns as $pattern){
		if(preg_match($pattern, $file)){
			$chk=true;
		}

	}

	return $chk;
}



function parsedirtree($subdir,&$files,$ignorefiles, &$size, $md5=false, $filescnt=0, $debug=false){
$dir=$this->dir."/".$subdir;
//echo $dir;exit;
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
		if(($file!=".") and ($file!="..")and !$this->checkfilename($file,$ignorefiles)){
			$filepath=$dir.'/'.$file;
			$filesubpath=$subdir.'/'.$file;
			if(is_dir($filepath)){
				$this->parsedirtree($filesubpath,$files,$ignorefiles,$size,$md5,$filescnt,$debug);
			}else{

				if($debug){
				$process="";
				if($filescnt){
					$proccess='('.(count($files)+1).'/'.$filescnt.')';
				}
				
				echo "Proccessing file $file $proccess... ";

				}

				$fileinfo=array("name"=>$file, "path"=>$subdir, "mtime"=>filemtime($filepath), "size"=>filesize($filepath));
				$size+=$fileinfo['size'];
				
				if($md5){
					 $fileinfo['md5']=md5_file($filepath);
$md5sum="";
if(file_exists($filepath.".md5sum")){
$md5sum=file_get_contents($filepath.".md5sum");
$md5sum=substr($md5sum,0,strpos($md5sum," "));
}
$fileinfo['md5_']=$md5sum;
				}

				$files[]=$fileinfo;

			if($debug){
				echo "Ok\n";
			}

			}
		}
	}
    }
}
}





function getFiles(){

$size=0;
$files=array();

$this->parsedirtree('',$files,$this->ignorefiles,$size);

//foreach ($files as $file){
//echo $file['name'].'---'.$file['path']."\n";
//}
//exit;
$files_count=count($files);
$files_size=$size;

$size=0;

$files=array();
$this->parsedirtree('',$files,$this->ignorefiles,$size,true,$files_count,true);

//foreach ($files as $file){
//echo $file['name'].'---'.$file['path'].'----'.$file['md5']."\n";
//}

return $files;

}

};

include "../inc/main.php";
$sql="select id, `release` from releases";
$db->execute($sql);
$x=$db->dataset;

$sql="truncate packagesfiles";
//echo $sql;
$db->execute($sql);

foreach($x as $v){
echo "Processing release \"".$v['release']."\"... ";
$dp=new dirparser();

$dp->dir="/mnt/raw/LFS/".$v['release']."/packages";
$dp->ignorefiles=array('/.md5sum$/');
$files=$dp->getFiles();

echo count($files);

foreach($files as $file){
$sql="insert into packagesfiles (`release`, filename,path, size, mtime, md5_current, md5_stored) values (
".$v['id'].",\"".$file['name']."\",\"".$file['path']."\", ".$file['size'].", ".$file['mtime'].", \"".$file['md5']."\",\"".$file['md5_']."\")";
//var_dump($sql); 
$db->execute($sql);
//var_dump($db->error);
//exit;
}


}




