#!/usr/bin/env php
<?php

include "../inc/main.php";

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

function getAll(){
global $db;
$sql="select id package, code from packages p";

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
      'URL'   => '/linux/packages/0.1/'.$node['code'],
      'label' => $node['code'],
      'shape' => 'box'
    )
  );

$childs=getChilds($node);
foreach($childs as $cnode){
addEdge($gv,$node,$cnode);
}

}

//var_dump(getChilds());

require_once 'Image/GraphViz.php';

$roots=getAll();


$gv = new Image_GraphViz(true,array(),"ULFS Packages Dependencies Map");
//var_dump($gv);
/*
$gv->addEdge(array('wake up'        => 'visit bathroom'));
$gv->addEdge(array('visit bathroom' => 'make coffee'));
*/


//var_dump($roots);
foreach($roots as $node){
addNode($gv,$node);

/*
$gv->addNode(
    $node['package'],
    array(
      'URL'   => '/linux/packages/0.1/'.$node['code'],
      'label' => $node['code'],
      'shape' => 'box'
    )
  );
*/

}

//ob_end_clean();
//echo "xxx";
//var_dump(
$gv->image();
//);


