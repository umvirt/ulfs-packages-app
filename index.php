<?php

include "inc/main.site.php";


$release=@addslashes($_REQUEST['release']);

echo "<h1>UmVirt LFS Packages</h1>";
//var_dump($_SERVER);

if(!$release){
echo "<h2>About</h2>";
echo "<p>Every GNU/Linux distro is provide software packages to install additional applications. <a href=\"//umvirt.com/linux/\">UmVirt LFS</a> is not exception.</p>";
echo "<p>Main purpose of \"UmVirt LFS Packages\" service is package installing assistance. Linux from scratch is not typical distro where binary source packages offered to user. LFS offers source packages without compilation automation. User have to download, unpack, configure, build and install packages manualy.";
echo "\"UmVirt LFS Packages\" service is help users to install packages and all it dependaces like in other distros.";
}

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

if($format=="text"){
$result=array();
ob_end_clean();
header("Content-type: text/plain");

foreach ($x as $k=>$v){
echo $v['release']."\n";
}

exit;
}


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
$dom->formatOutput=true;
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
echo "<h2>Packages list</h2>";
echo "Please select release to get packages list: ".strjoin ($releases,', ');


echo "<h2>How to install package?</h2>";
echo "<p>To download, unpack, compile, build and install \"Midnight Commander\" package with all dependances just type:</p>
<p><tt>wget --no-check-cerificate ".$config['packages_url']."0.1/mc/install -O - | bash</tt>
</p>";
echo "<p><b>Tip:</b> <i>To simplify this string you can use <a href=\"//umvirt.com/linux/assistant\">UmVirt LFS Assistant</a>!</i></p>";


echo "<h2>How to remove package?</h2>";
echo "<p>No way. You can't remove packages. Imagine Android smartphone, router or other device with Linux firmware. You cant remove installed packages directly, only firmware entirely.</p>";
echo "<p><b>Warning</b>: <i>Manual deletion of files can cause errors and system damage!</i></p>";


echo "<h2>See also</h2>";
echo "<ol>
<li><a href=\"howitworks.html\">How it works?</a>
<li><a href=\"howtofork.html\">How to fork?</a>
<li><a href=\"api.html\">Application Programming interface (API)</a>
</ol>";

}else{
echo "Current releases: ".strjoin ($releases,', ');

//----

$sql="select p.id, code, sourcefile, c.comments, p.description from packages p
inner join releases r on r.id=p.`release`
left join (select count(id) comments, package from comments group by package) as c on c.package=p.id 
where r.`release`=\"".$release."\"
order by p.id asc

";
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

if($format=="text"){
$result=array();
$result['release']=$release;
ob_end_clean();
header("Content-type: text/plain");
foreach ($x as $k=>$v){
echo $v['code']."\n";
}
exit;
}



if($format=="xml"){
$dom = new DOMDocument('1.0', 'utf-8');
$dom->formatOutput=true;
$root = $dom->createElement('packages');

$release_element = $dom->createElement('release',$release);
$root->appendChild($release_element);

$archs=architectures();
$archs_element=$dom->createElement('architectures');
//$root->appendChild($archs_element);
foreach ($archs as $arch){
$arch_element=$dom->createElement('architecture');
$arch_element->appendChild($dom->createElement('code',$arch['code']));
$arch_element->appendChild($dom->createElement('code',base64_encode($arch['description'])));
$archs_element->appendChild($arch_element);
}

$root->appendChild($archs_element);

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

$s="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/$release/".$v['code'].">".$v['code']."</a>";

if($v['comments']){
$s.="<sup>*</sup>";
}

if($format=="descriptions"){
$pkgs[]="<li><b>$s</b> <br>".$v['description']."</li>";
}else{
$pkgs[]="<tr><td>".$v['id']."</td><td>".$s."</td><td>".$v['sourcefile']."</td></tr>";
}

}

echo "<p>[ <a href=/linux/packages/depmap/".$release."/>Dependencies Map</a> ]</p>";
echo "<h2>Packages(".count($x).")</h2>";

if($format=="descriptions"){
echo "Available packages: <ul>".strjoin ($pkgs)."</ul>";
}else{
echo "Available packages: <table>".strjoin ($pkgs)."</table>";
}

}

include "inc/template.php";
