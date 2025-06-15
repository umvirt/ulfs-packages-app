<?php
/**
 * ULFS Packages Web-Application
 *
 * depmap controller
 *
 * depmap controller is used to render packages dependenies map using GraphViz library.
 */

//load application
include "inc/main.php";

//lines stroke styles
$styles=array(
    "dashed",
    "dotted",
    "solid"
);

//lines stroke number
$lsn=0;

//colors list
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

//color number
$cn=0;

/**
 * Get childs for package.
 *
 * If package is not defined get packages without parents (roots)
 *
 * @param int $parent A package id
 *
 * @return array
 */
function getChilds($parent=null)
{
    global $db;
    $sql="select id package, code from packages p
    left join dependances d on d.package=p.id";

    if($parent)
    {
        $sql.=" where dependance = ".$parent['package'];
    }else{
        $sql.=" where dependance is null";
    }

    $db->execute($sql);

    return $db->dataset;
}
/**
 * Get all packages for release
 *
 * @param string $release A release code
 *
 * @return array
 */
function getAll($release)
{
    global $db;
    $sql="select p.id package, p.code, r.`release` from packages p left join releases r on r.id=p.`release` where r.release=\"$release\"";
    $db->execute($sql);
    return $db->dataset;
}

/**
 * Add edge for line between parent package node and child package node.
 *
 * @param object $gv A graph object
 * @param object $parent A parent package node object
 * @param object $child A child package node object
 */
function addEdge($gv,$parent,$child)
{
    global $colors;
    global $styles;
    global $cn;
    global $lsn;

    if($cn>=count($colors))
    {
        $cn=0;
        $lsn++;
    }

    if($lsn>=count($styles))
    {
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

/**
 * Add package node to graph
 *
 * @param object $gv A graph object
 * @param array $node A package dataset item
 */
function addNode($gv,$node)
{
    //add specific node
    $gv->addNode(
        $node['package'],
        array(
        'URL'   => '/linux/packages/'.$node['release'].'/'.$node['code'],
        'label' => $node['code'],
        'shape' => 'box'
        )
    );

    //get chils for node
    $childs=getChilds($node);

    //each child
    foreach($childs as $cnode)
    {
        //add it to graph
        addEdge($gv,$node,$cnode);
    }
}

//Main

//get reease value
$release=addslashes($_REQUEST['release']);

//cache file
$file='tmp/depmap_'.($release).'.svg';

//if cache file not exists
if(!file_exists($file))
{
    //render it
    require_once 'Image/GraphViz.php';

    //get all roots (packages) for release
    $roots=getAll($release);

    //new graph
    $gv = new Image_GraphViz(true,array(),"ULFS Packages Dependencies Map");


    //add root nodes
    foreach($roots as $node)
    {
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
}else{
    echo(file_get_contents($file));
}


