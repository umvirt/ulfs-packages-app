<?php
/**
 * ULFS Packages Web-Application
 *
 * Default controller
 *
 * Default controller is used to render:
 *  - default page
 *  - releses list
 *  - packages list for specific package
 */

//load application
include "inc/main.site.php";

//get release from $_REQUEST
$release=@addslashes($_REQUEST['release']);

//Show title
echo "<h1>UmVirt LFS Packages</h1>";

//If release not defined
if(!$release)
{
    //render index page
    echo "<h2>About</h2>";
    echo "<p>Every GNU/Linux distro is provide software packages to install additional applications. <a href=\"//umvirt.com/linux/\">UmVirt LFS</a> is not exception.</p>";
    echo "<p>Main purpose of \"UmVirt LFS Packages\" service is package installing assistance. Linux from scratch is not typical distro where binary source packages offered to user. LFS offers source packages without compilation automation. User have to download, unpack, configure, build and install packages manualy.";
    echo "\"UmVirt LFS Packages\" service is help users to install packages and all it dependaces like in other distros.";
}

//get format from $_REQUEST
$format=@addslashes($_REQUEST['format']);

//get releases links list
$sql="select id, `release` from releases";
$db->execute($sql);
$x=$db->dataset;
$releases=array();
foreach ($x as $k=>$v)
{
    $s=$v['release'];
    if($release==$v['release'])
    {
        $s="<b>".$v['release']."</b>";
    }
    $releases[]="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/".$v['release'].">".$s."</a>";
}

//if release not defined
if(!$release)
{
    //if format is text
    if($format=="text")
    {
        //just print releases list
        $result=array();
        ob_end_clean();
        header("Content-type: text/plain");

        foreach ($x as $k=>$v)
        {
            echo $v['release']."\n";
        }

        exit;
    }

    //if format is json
    if($format=="json")
    {
        //list releases in JSON-document
        $result=array();
        ob_end_clean();
        foreach ($x as $k=>$v)
        {
            $result['releases'][]=$v['release'];
        }
        header("Content-type: text/plain");
        echo json_encode($result);
        exit;
    }

    //if format is xml
    if($format=="xml")
    {
        //list releases in XML-document
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput=true;
        $root = $dom->createElement('ulfspackages');
        $releases_element = $dom->createElement('releases');
        $result=array();
        ob_end_clean();
        foreach ($x as $k=>$v)
        {
            $release_element = $dom->createElement('release', $v['release']);
            $releases_element->appendChild($release_element);
        }
        $root->appendChild($releases_element);
        $dom->appendChild($root);
        header("Content-type: text/xml");
        echo $dom->saveXML();
        exit;
    }

    //resume render default HTML page if format is not defined

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
//if release is defined
}else{
    //print releases list
    echo "Current releases: ".strjoin ($releases,', ');

    //get packages list dataset for specific release
    $sql="select p.id, p.code, sourcefile, c.comments, p.description, p.configure, p.build, p.install,
    p.template template_id, t.code template, t.configure template_configure, t.build template_build, t.install template_install,
    pf.filename pf_filename, pf.md5_current, pf.md5_stored
    from packages p
    inner join releases r on r.id=p.`release`
    left join packages_templates t on t.id=p.template
    left join (select count(id) comments, package from comments group by package) as c on c.package=p.id
    left join packagesfiles pf on pf.filename=p.sourcefile and pf.`release`=p.`release`
    where r.`release`=\"".$release."\"
    order by p.id asc";
    //echo $sql;
    $db->execute($sql);
    //var_dump($db->errors);
    $x=$db->dataset;

    //if format is json
    if($format=="json")
    {
        //list packages in JSON-document
        $result=array();
        $result['release']=$release;
        $archs=architectures();
        $result['architectures']=array();
        foreach ($archs as $arch)
        {
            $result['architectures'][]=array('code'=>$arch['code'],'description'=>base64_encode($arch['description']));
        }


        $templates=templates($release);

        if(count($templates))
        {
            $result['templates']=array();

            foreach ($templates as $template)
            {
                $result['templates'][]=array(
                    'code'=>$template['code'],
                    'description'=>base64_encode($template['description']),
                    'configure'=>base64_encode($template['configure']),
                    'build'=>base64_encode($template['build']),
                    'install'=>base64_encode($template['install'])
                );
            }

        }

        ob_end_clean();
        foreach ($x as $k=>$v)
        {
            $result['packages'][]=$v['code'];
        }
        header("Content-type: text/plain");
        echo json_encode($result);
        exit;
    }

    //if format is text
    if($format=="text")
    {
        //just print packages list
        $result=array();
        $result['release']=$release;
        ob_end_clean();
        header("Content-type: text/plain");
        foreach ($x as $k=>$v)
        {
            echo $v['code']."\n";
        }
        exit;
    }

    //if format is json
    if($format=="xml")
    {
        //list packages in XML-document
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput=true;
        $root = $dom->createElement('packages');

        $release_element = $dom->createElement('release',$release);
        $root->appendChild($release_element);

        $archs=architectures();
        $archs_element=$dom->createElement('architectures');
        //$root->appendChild($archs_element);
        foreach ($archs as $arch)
        {
            $arch_element=$dom->createElement('architecture');
            $arch_element->appendChild($dom->createElement('code',$arch['code']));
            $arch_element->appendChild($dom->createElement('description',base64_encode($arch['description'])));
            $archs_element->appendChild($arch_element);
        }

        $root->appendChild($archs_element);

        //templates

        $templates=templates($release);
        if(count($templates))
        {
            $templates_element=$dom->createElement('templates');
            //$root->appendChild($archs_element);
            foreach ($templates as $template)
            {
                $template_element=$dom->createElement('template');
                $template_element->appendChild($dom->createElement('code',$template['code']));
                $template_element->appendChild($dom->createElement('description',base64_encode($template['description'])));
                $template_element->appendChild($dom->createElement('configure',base64_encode($template['configure'])));
                $template_element->appendChild($dom->createElement('build',base64_encode($template['build'])));
                $template_element->appendChild($dom->createElement('install',base64_encode($template['install'])));
                $templates_element->appendChild($template_element);
            }

            $root->appendChild($templates_element);
        }


        $result=array();
        ob_end_clean();
        $packages_element = $dom->createElement('packages');
        foreach ($x as $k=>$v)
        {
            $package_element = $dom->createElement('package', $v['code']);
            $packages_element->appendChild($package_element);
        }
        $root->appendChild($packages_element);
        $dom->appendChild($root);
        header("Content-type: text/xml");
        echo $dom->saveXML();
        exit;
    }

    //resume render packages list HTML page if format is not defined

    //packages items array
    $pkgs=array();

    //proccess each record in dataset
    foreach ($x as $k=>$v)
    {

        $s="<a href=".dirname($_SERVER['SCRIPT_NAME'])."/$release/".$v['code'].">".$v['code']."</a>";

        //if package have comments
        if($v['comments'])
        {
            //mark it
            $s.="<sup>*</sup>";
        }

        //if format is descriptions
        if($format=="descriptions")
        {
            //add item "package and description"
            $pkgs[]="<li><b>$s</b> <br>".$v['description']."</li>";
        }else{

            //set default package template value is "Default"
            $tmpl="Default";

            //if template is defined
            if($v['template'])
            {
                //use it as template name
                $tmpl=$v['template'];
            }

            //assume that configuration script in package is empty
            $mod_c=".";

            //if configuration script in package is defined
            if($v['configure'])
            {
                //mark that
                $mod_c="C";
            }

            //assume that build script in package is empty
            $mod_b=".";

            //if build script in package is defined
            if($v['build'])
            {
                //mark that
                $mod_b="B";
            }

            //assume that build script in package is empty
            $mod_i=".";

            //if build script in package is defined
            if($v['install'])
            {
                //mark that
                $mod_i="I";
            }

            $tmpl=$tmpl.':'.$mod_c.$mod_b.$mod_i;

            $fileclass='';

            //if filesystem validation is enabled
            if(@$config['fs_validation'])
            {
                //if file is not found
                if(!$v['pf_filename'])
                {
                    $fileclass='error';
                //if file is found
                }else{
                    //if MD5-checksum is provided in .md5sum file
                    if($v['md5_stored'])
                    {
                        //if current checksum value is wrong
                        if($v['md5_current']!=$v['md5_stored'])
                        {
                           $fileclass='error';
                        //if current checksum value is valid
                        }else{
			    $fileclass='ok';
                        }
                    }
                }
            }
            //add item "package table item"
            $pkgs[]="<tr><td>".$v['id']."</td><td>".$s."</td><td class=$fileclass>".$v['sourcefile']."</td><td>$tmpl</td></tr>";
        }

    }

    echo "<p>[ <a href=/linux/packages/files/".$release."/>Files</a> | <a href=/linux/packages/patches/".$release."/>Patches</a> | <a href=/linux/packages/depmap/".$release."/>Dependencies Map</a> ]</p>";
    echo "<h2>Packages(".count($x).")</h2>";

    //if format is descriptions
    if($format=="descriptions")
    {
        //print package items as list
        echo "Available packages: <ul>".strjoin ($pkgs)."</ul>";
    //if format is empty or have other value
    }else{
        //print package items as table
        echo "Available packages: <table class=packages>".strjoin ($pkgs)."</table>";
    }

}

//render page with template
include "inc/template.php";
