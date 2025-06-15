<?php
/**
 * ULFS Packages Web-Application
 *
 * Package export controller
 *
 * Package export controller is used to dump package info in various formats
 */

//load application
include "inc/main.php";

//get release from $_REQUEST
$release=@addslashes($_REQUEST['release']);
//get package from $_REQUEST
$package=@addslashes($_REQUEST['package']);
//get format from $_REQUEST
$format=@addslashes($_REQUEST['format']);


$sql="select p.id, r.`release`, p.code, p.unpack, p.sourcefile, p.sourcedir, p.configure, p.build, p.install, p.description,p.localbuild,
p.template template_id, t.code template
from packages p
left join releases r on p.release=r.id
left join packages_templates t on t.id=p.template
where r.`release`=\"$release\" and p.code=\"$package\"";

//var_dump($sql);
$db->execute($sql);

$x=$db->dataset;

$pkgs=array();
foreach ($x as $k=>$v)
{
    $dependances=dependances($release, $v['code']);
    $patches=patches($release,$v['code']);
    $addons=addons($release,$v['code']);
    $nestings=nestings($release,$v['code']);
    $comments=comments($release,$v['code']);
    $archpackages=pkgarchpackages($release,$package);

    if($format=="json")
    {
        $arr=array
        (
            'code'=>$package,
            'description'=>base64_encode($v['description']),
            'release'=>$release,
            'sourcefile'=>$v['sourcefile'],
            'sourcedir'=>$v['sourcedir'],
            'unpack'=>base64_encode($v['unpack']),
            'configure'=>base64_encode($v['configure']),
            'build'=>base64_encode($v['build']),
            'install'=>base64_encode($v['install']),
            'dependances'=>$dependances,
            'patches'=>$patches,
            'addons'=>$addons,
            'nestings'=>$nestings,
            'comments'=>$comments,
        );

        if($release!="0.1")
        {
                $arr['localbuild']=$v['localbuild'];
        }

        if($v['template'])
        {
                $arr['template']=$v['template'];
        }

        //Architecture specific instructions
        if(count($archpackages))
        {
            $arr['archpackages']=array();

            foreach($archpackages as $archpackage)
            {
                $architem=array(
                'arch'=>$archpackage['arch'],
                'configure'=>base64_encode($archpackage['configure']),
                'build'=>base64_encode($archpackage['build']),
                'install'=>base64_encode($archpackage['install'])
                );
                $adependances=archpkgdependances($release,$archpackage['arch'],$v['code']);

                if(count($adependances))
                {
                    $architem['dependances']=array();
                    foreach ($adependances as $adep)
                    {
                        $architem['dependances'][]=array(
                        'code'=>$adep['code'],
                        'arch'=>$adep['arch'],
                        'weight'=>$adep['weight']
                        );
                    }
                }

                $arr['archpackages'][]=$architem;
            }
        }

        $result=json_encode($arr);
    }

    if($format=="xml")
    {
        //header("Content-type: text/xml");
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput=true;
        $root = $dom->createElement('package');
        $release_ = $dom->createElement('release',$release);
        $code = $dom->createElement('code',$package);
        $description = $dom->createElement('description',base64_encode($v['description']));

        $sourcefile = $dom->createElement('sourcefile',$v['sourcefile']);
        $sourcedir = $dom->createElement('sourcedir',$v['sourcedir']);
        $unpack = $dom->createElement('unpack',base64_encode($v['unpack']));
        $configure = $dom->createElement('configure',base64_encode($v['configure']));
        $build = $dom->createElement('build',base64_encode($v['build']));
        $install = $dom->createElement('install',base64_encode($v['install']));

        $root->appendChild($release_);
        $root->appendChild($code);
        $root->appendChild($description);
        $root->appendChild($sourcefile);
        $root->appendChild($sourcedir);
        $root->appendChild($unpack);
        $root->appendChild($configure);
        $root->appendChild($build);
        $root->appendChild($install);

        if($release!="0.1")
        {
            $localbuild = $dom->createElement('localbuild',$v['localbuild']);
            $root->appendChild($localbuild);
        }

        if($v['template'])
        {
            $localbuild = $dom->createElement('template',$v['template']);
            $root->appendChild($localbuild);
        }


        //Dependances
        //test: mc
        $dependances_element = $dom->createElement('dependances');
        foreach($dependances as $dep)
        {
            $dependance_element=$dom->createElement('dependance');
            $dependance_element->appendChild($dom->createElement('code',$dep['code']));
            $dependance_element->appendChild($dom->createElement('weight',$dep['weight']));
            $dependances_element->appendChild($dependance_element);
        }
        $root->appendChild($dependances_element);
        //Patches
        //test: glib
        $patches_element = $dom->createElement('patches');
        foreach($patches as $pat)
        {
            $patch_element=$dom->createElement('patch');
            $filename_element=$dom->createElement('filename',$pat['filename']);
            $patch_element->appendChild($filename_element);
            $mode_element=$dom->createElement('mode',$pat['mode']);
            $patch_element->appendChild($mode_element);
            $patches_element->appendChild($patch_element);
        }
        $root->appendChild($patches_element);
        //Addons
        //test: llvm
        $addons_element = $dom->createElement('addons');
        foreach($addons as $addon)
        {
            $addon_element=$dom->createElement('addon',$addon);
            $addons_element->appendChild($addon_element);
        }
        $root->appendChild($addons_element);
        //Nestings
        $nestings_element = $dom->createElement('nestings');
        foreach($nestings as $nesting)
        {
            $nesting_element=$dom->createElement('nesting',$nesting);
            $nestings_element->appendChild($nesting_element);
        }
        $root->appendChild($nestings_element);

        //Comments
        $comments_element = $dom->createElement('comments');
        foreach($comments as $comment)
        {
            $comment_element=$dom->createElement('comment',$comment);
            $comments_element->appendChild($comment_element);
        }
        $root->appendChild($comments_element);

        //Architecture specific instructions
        if(count($archpackages))
        {
            $archpackages_element = $dom->createElement('archpackages');
            foreach($archpackages as $archpackage)
            {
                //main container init
                $archpackage_element=$dom->createElement('archpackage');

                //arch
                $arch_element=$dom->createElement('arch',$archpackage['arch']);
                $archpackage_element->appendChild($arch_element);

                //configure script
                $arch_element=$dom->createElement('configure',base64_encode($archpackage['configure']));
                $archpackage_element->appendChild($arch_element);

                //build script
                $arch_element=$dom->createElement('build',base64_encode($archpackage['build']));
                $archpackage_element->appendChild($arch_element);

                //install script
                $arch_element=$dom->createElement('install',base64_encode($archpackage['install']));
                $archpackage_element->appendChild($arch_element);

                //arch dependances
                $adependances=archpkgdependances($release,$archpackage['arch'],$v['code']);
                //var_dump($adependances);exit;
                if(count($adependances))
                {
                    $arch_element=$dom->createElement('dependances');
                    foreach ($adependances as $adep)
                    {
                        $dep_element=$dom->createElement('dependance');
                        //code
                        $depcode_element=$dom->createElement('code',$adep['code']);
                        $dep_element->appendChild($depcode_element);
                        //arch
                        $depcode_element=$dom->createElement('arch',$adep['arch']);
                        $dep_element->appendChild($depcode_element);
                        //weight
                        $depcode_element=$dom->createElement('weight',$adep['weight']);
                        $dep_element->appendChild($depcode_element);

                        $arch_element->appendChild($dep_element);
                    }
                    $archpackage_element->appendChild($arch_element);
                }
                //main container finalize
                $archpackages_element->appendChild($archpackage_element);
            }

            $root->appendChild($archpackages_element);
        }
        $dom->appendChild($root);
        $result=$dom->saveXML();
    }
}


if($format=="xml")
{
    header("Content-type: text/xml");
    echo $result;
}

if($format=="json")
{
    header("Content-type: text/plain");
    echo $result;
}

