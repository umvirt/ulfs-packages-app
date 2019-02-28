<?php

include "inc/main.site.php";

echo "<h1>UmVirt LFS Packages</h1>";
//var_dump($_SERVER);

$release=@addslashes($_REQUEST['release']);


$format=@addslashes($_REQUEST['format']);

$sql="select id, `release` from releases";

$db->execute($sql);

$x=$db->dataset;

$releases=array();




foreach ($x as $k=>$v){

$s=$v['release'];
if($release==$v['release']){
$s="<b>".$v['release']."</b>";
}

$releases[]="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/".$v['release'].">".$s."</a>";
}


if(!$release){

if($format=="json"){
$result=array();
ob_end_clean();
foreach ($x as $k=>$v){
$result['releases'][]=$v['release'];
}
header("Content-type: text/plain");
echo json_encode($result);
exit;
}

if($format=="xml"){
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->createElement('ulfspackages');
$releases_element = $dom->createElement('releases');
$result=array();
ob_end_clean();
foreach ($x as $k=>$v){
$release_element = $dom->createElement('release', $v['release']);
$releases_element->appendChild($release_element);
}
$root->appendChild($releases_element);
$dom->appendChild($root);
header("Content-type: text/xml");
echo $dom->saveXML();
exit;
}

echo "Select release: ".join ($releases,', ');

}else{
echo "Current releases: ".join ($releases,', ');

//----


$sql="select p.id, code, sourcefile from packages p
inner join releases r on r.id=p.`release`
where r.`release`=\"".$release."\"";
//echo $sql;
$db->execute($sql);

$x=$db->dataset;

if($format=="json"){
$result=array();
$result['release']=$release;
ob_end_clean();
foreach ($x as $k=>$v){
$result['packages'][]=$v['code'];
}
header("Content-type: text/plain");
echo json_encode($result);
exit;
}

if($format=="xml"){
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->createElement('packages');
$release_element = $dom->createElement('release',$release);
$root->appendChild($release_element);
$result=array();
ob_end_clean();
$packages_element = $dom->createElement('packages');
foreach ($x as $k=>$v){
$package_element = $dom->createElement('package', $v['code']);
$packages_element->appendChild($package_element);
}
$root->appendChild($packages_element);
$dom->appendChild($root);
header("Content-type: text/xml");
echo $dom->saveXML();
exit;
}


$pkgs=array();
foreach ($x as $k=>$v){

$s=$v['code'];

$pkgs[]="<tr><td><a href=".dirname($_SERVER['SCRIPT_NAME'])."/$release/".$v['code'].">".$s."</a></td><td>".$v['sourcefile']."</td></tr>";
}

echo "<h2>Packages(".count($x).")</h2>";
echo "Available packages: <table>".join ($pkgs)."</table>";

}

include "inc/template.php";
