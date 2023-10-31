<?php
include "inc/main.php";

//variables

$styles=array(
    "dashed",
    "dotted",
    "solid");

$lsn=0;

$colors=array(
"red",
"black",
"gray",
"blue",
"green",
"brown",
"pink",
"yellow",
"purple",
"orange",
);
$cn=0;

//functions


function getChilds($parent=null){
global $db;
$sql="select id package, code from packages p
left join dependances d on d.package=p.id";

if($parent){
$sql.=" where dependance = ".$parent['package'];
//echo $sql;
//exit;
}else{
$sql.=" where dependance is null";
}

$db->execute($sql);
/*
$x=$db->dataset;
$res=array();
foreach($x as $v){
}
*/
return $db->dataset;

}

function getAll($release){
global $db;
$sql="select p.id package, p.code, r.`release` from packages p left join releases r on r.id=p.`release` where r.release=\"$release\"";

$db->execute($sql);

return $db->dataset;

}

function addEdge($gv,$parent,$child){
global $colors;
global $styles;
global $cn;
global $lsn;

if($cn>=count($colors)){
$cn=0;
$lsn++;
}

if($lsn>=count($styles)){
$lsn=0;
}

$gv->addEdge(
    array(
      $parent['package'] => $child['package']
    ),
    array(
      'color' => $colors[$cn],
      'style' => $styles[$lsn]
    )
 );

$cn++;

}


function addNode($gv,$node){

$gv->addNode(
    $node['package'],
    array(
      'URL'   => '/linux/packages/'.$node['release'].'/'.$node['code'],
      'label' => $node['code'],
      'shape' => 'box'
    )
  );

$childs=getChilds($node);
foreach($childs as $cnode){
addEdge($gv,$node,$cnode);
}

}

//Main

//get reease value
$release=addslashes($_REQUEST['release']);

//cache file
$file='tmp/depmap_'.($release).'.svg';

//if cache file not exists
if(!file_exists($file)){

//render it
require_once 'Image/GraphViz.php';

//get all roots (packages) for release
$roots=getAll($release);

//new graph
$gv = new Image_GraphViz(true,array(),"ULFS Packages Dependencies Map");


//add root nodes
foreach($roots as $node){
    addNode($gv,$node);
}

//start buffer
ob_start();
//render graph to buffer
$gv->image();
//save buffer
$content=ob_get_contents();
//stop buffer
ob_end_clean();

file_put_contents($file,$content);
//show graph
echo $content;
}
echo(file_get_contents($file));


