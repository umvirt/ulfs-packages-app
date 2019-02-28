<?php

include "inc/main.php";

$release=@addslashes($_REQUEST['release']);
$package=@addslashes($_REQUEST['package']);
$format=@addslashes($_REQUEST['format']);


$sql="select p.id, r.`release`, code, sourcefile, sourcedir, configure, build, install from packages p
left join releases r on p.release=r.id
where r.`release`=\"$release\" and p.code=\"$package\"";

//var_dump($sql);
$db->execute($sql);

$x=$db->dataset;

$pkgs=array();
foreach ($x as $k=>$v){

$dependances=dependances($release, $v['code']);
$patches=patches($release,$v['code']);
$addons=addons($release,$v['code']);

if($format=="json"){
$arr=array(
'code'=>$package,
'release'=>$release,
'sourcefile'=>$v['sourcefile'],
'sourcedir'=>$v['sourcedir'],
'configure'=>base64_encode($v['configure']),
'build'=>base64_encode($v['build']),
'install'=>base64_encode($v['install']),
'dependances'=>$dependances,
'patches'=>$patches,
'addons'=>$addons,
);
$result=json_encode($arr);
}

if($format=="xml"){

//header("Content-type: text/xml");
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->createElement('package');
$release = $dom->createElement('release',$release);
$code = $dom->createElement('code',$package);

$sourcefile = $dom->createElement('sourcefile',$v['sourcefile']);
$sourcedir = $dom->createElement('sourcedir',$v['sourcedir']);
$configure = $dom->createElement('configure',base64_encode($v['configure']));
$build = $dom->createElement('build',base64_encode($v['build']));
$install = $dom->createElement('install',base64_encode($v['install']));


$root->appendChild($release);
$root->appendChild($code);
$root->appendChild($sourcefile);
$root->appendChild($sourcedir);
$root->appendChild($configure);
$root->appendChild($build);
$root->appendChild($install);

//Dependances
//test: mc
$dependances_element = $dom->createElement('dependances');
foreach($dependances as $dep){
	$dependance_element=$dom->createElement('dependance',$dep);
	$dependances_element->appendChild($dependance_element);
}
$root->appendChild($dependances_element);
//Patches
//test: glib
$patches_element = $dom->createElement('patches');
foreach($patches as $pat){
        $patch_element=$dom->createElement('patch',$pat);
        $patches_element->appendChild($patch_element);
}
$root->appendChild($patches_element);
//Addons
//test: llvm
$addons_element = $dom->createElement('addons');
foreach($addons as $addon){
        $addon_element=$dom->createElement('addon',$addon);
        $addons_element->appendChild($addon_element);
}
$root->appendChild($addons_element);

$dom->appendChild($root);
$result=$dom->saveXML();
}

}


if($format=="xml"){
header("Content-type: text/xml");
echo $result;
}

if($format=="json"){
header("Content-type: text/plain");
echo $result;
}

