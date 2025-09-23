<?php
/**
 * ULFS Packages Web-Application
 *
 * Application functions
 *
 * This file is contain functions that are application specific.
 */

/**
 * Convert value to HTML-code
 *
 * @param string $val A source value
 * @return string
 */
function val2html($val)
{
    return htmlspecialchars($val,ENT_QUOTES);
}


/**
 * Add slashes to script value
 *
 * @param string $v       A source value
 * @param string $release A release code
 * @return string
 */
function scriptslashes($v,$release="")
{
    //if release 0.1
    if($release != "0.1")
    {
        //add slashes
        $v=str_replace('\\','\\\\',$v);
    }
    //$ -> \$
    $v=str_replace('$','\$',$v);
    //\r\n\ -> \n
    $v=str_replace("\r\n","\n",$v);

    return $v;
}

/**
 * Configuration script selector for specific package
 *
 * @param array $v A package dataset row
 * @return string
 */
function configuration_script($v)
{
    //if configuration script is not defined
    if(!$v['configure'])
    {
        //if template not defined
        if(!$v['template'])
        {
            //use default value
            return "./configure --prefix=/usr";
        //if template is defined
        }else{
            //use value defined in template
            $v=$v['template_configure'];
            $v=str_replace("\r\n","\n",$v);
            return $v;
        }
    //if configuration script is defined
    }else{
        //use it
        $res=$v['configure'];
        $res=str_replace("\r\n","\n",$res);
        return $res;
    }
}

/**
 * Build script selector for specific package
 *
 * @param array $v A package dataset row
 * @return string
 */
function build_script($v)
{
    //if build script is not defined
    if(!$v['build'])
    {
        //if template not defined
        if(!$v['template'])
        {
            //use default value
            return "make";
        //if template is defined
        }else{
            //use value defined in template
            $v=$v['template_build'];
            $v=str_replace("\r\n","\n",$v);
            return $v;
        }
    //if build script is defined
    }else{
        //use it
        $res=$v['build'];
        $res=str_replace("\r\n","\n",$res);
        return $res;
    }
}

/**
 * Install script selector for specific package
 *
 * @param array $v A package dataset row
 * @return string
 */
function install_script($v)
{
    //if install script is not defined
    if(!$v['install'])
    {
        //if template not defined
        if(!$v['template'])
        {
            //use default value
            return "make install";
        //if template is defined
        }else{
            //use value defined in template
            $v=$v['template_install'];
            $v=str_replace("\r\n","\n",$v);
            return $v;
        }
    //if install script is defined
    }else{
        //use it
        $res=$v['install'];
        $res=str_replace("\r\n","\n",$res);
        return $res;
    }
}

/**
 * Unpack script formatter
 *
 * @param string $v A source value
 * @return string
 */
function unpack_script($v="")
{
    $v=str_replace("\r\n","\n",$v);
    return $v;
}

/**
 * Preparation script formatter
 * 
 * @param string $v A source value
 * @return string
 */
function preparation_script($v="")
{  
    $v=str_replace("\r\n","\n",$v);
    return $v;
}



/**
 * Archpackage configuration script selector
 *
 * @param string $v A source value
 * @return string
 */
function archconfiguration_script($v="")
{
    //if value is not defined
    if(!$v)
    {
        //use default value
        return "./configure --prefix=/usr";
    //if value is defined
    }else{
        //use it
        return $v;
    }
}

/**
 * Archpackage build script selector
 *
 * @param string $v A source value
 * @return string
 */
function archbuild_script($v="")
{
    //if value is not defined
    if(!$v)
    {
        //use default value
        return "make";
    //if value is defined
    }else{
        //use it
        return $v;
    }
}

/**
 * Archpackage install script selector
 *
 * @param string $v A source value
 * @return string
 */
function archinstall_script($v="")
{
    //if value is not defined
    if(!$v)
    {
        //use default value
        return "make install";
    //if value is defined
    }else{
        //use it
        //$ -> \$
        $v=str_replace('$','\$',$v);
        //\r\n\ -> \n
        $v=str_replace("\r\n","\n",$v);
        return $v;
    }
}

/**
 * Generate download URL for specific file
 *
 * @param string $release A release code
 * @param string $file    A file name
 * @return string
 */
function download_url($release,$file)
{
    global $config;
    return getpath($release,$file, $config['downloads_url']);
}

/**
* Generate absolute path for specific file
*
* @param string $release A release code
* @param string $file    A file name
* @return string
*/
function file_path($release,$file)
{
    global $config;
    return getpath($release,$file, $config['localpath']."/files/");
}

/**
 * Generate path for specific file and apped it to base path
 *
 * @param string $release   A release code
 * @param string $file      A file name
 * @param string $basepath  A base path
 * @return string
 */
function getpath($release,$file,$basepath)
{
    global $config;

    //search files in specialized directories

    $directories=array(
      'python-modules',
      'perl-modules',
      'Xorg',
      'Xorg/lib',
      'Xorg/app',
      'Xorg/font',
      'games',
      'kde',
      'kde/kf',
      'kde/plasma',
      'kde/apps',
      'lxqt',
      'db'
    );

    foreach($directories as $directory) {
        $x=file_exists($config['filespath']."/$release/packages/$directory/$file");
        //var_dump($x);
        if($x){
            return $basepath."$release/packages/$directory/".$file;
        }
    }

    //search files in alphabetical directories

    $dir=strtolower($file[0]);
    if(file_exists($config['filespath']."/$release/packages/".$dir."/".$file)){
    return $basepath."$release/packages/".$dir."/".$file;
    }

    //return empy string

    return "";
}

/**
 * Generate download URL for specific patch
 *
 * @param string $release A release code
 * @param string $file    A patch file name
 * @return string
 */
function patch_url($release,$file)
{
    global $config;
    return $config['downloads_url']."$release/patches/$file";
}

/**
* Generate absolute path for specific patch
*
* @param string $release A release code
* @param string $file    A patch file name
* @return string
*/
function patch_path($release,$file)
{
    global $config;
    if(file_exists($config['filespath']."/$release/patches/$file"))
    {
        return $config['filespath']."/$release/patches/$file";
    }
    return "";
}

/**
 * Generate dependencies list for specific package
 *
 * @param string $release A release code
 * @param string $package A package code
 * @return array
 */
function dependances($release,$package)
{
    global $db;
    $sql="select dp.code, dd.code dependance, d.weight from dependances d
    inner join packages dp on d.package=dp.id
    inner join packages dd on d.dependance=dd.id
    inner join `releases` r on r.id=dp.release
    where dp.code=\"$package\" and r.release=\"$release\"

    order by d.weight, d.dependance
    ";
    //var_dump($sql);
    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    foreach($x as $k=>$v)
    {
        $deps[]=array(
            "code"=>$v['dependance'],
            "weight"=>$v['weight']
        );
    }
    return $deps;
}

/**
 * Generate packages list which have specific package in dependencies
 *
 * @param string $release A release code
 * @param string $package A package code
 * @return array
 */
function dependanceOf($release,$package)
{
    global $db;
    $sql="select dp.code package, dd.code, d.weight from dependances d
    inner join packages dp on d.package=dp.id
    inner join packages dd on d.dependance=dd.id
    inner join `releases` r on r.id=dp.release
    where dd.code=\"$package\" and r.release=\"$release\"

    order by d.weight, d.dependance
    ";
    //var_dump($sql);
    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    foreach($x as $k=>$v)
    {
        $deps[]=array(
            "code"=>$v['package'],
            "weight"=>$v['weight']
        );
    }
    return $deps;
}

/**
 * Generate patches list for specific package
 *
 * @param string $release A release code
 * @param string $package A package code
 * @return array
 */
function patches($release,$package)
{

    global $db;
    $sql="select p.filename,p.mode from patches p
    inner join packages pp on p.package=pp.id
    inner join `releases` r on r.id=pp.release
    where pp.code=\"$package\" and r.release=\"$release\"";
    //var_dump($sql);
    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    foreach($x as $k=>$v)
    {
            $deps[]=array("filename"=>$v['filename'],"mode"=>$v['mode']);
    }
    return $deps;

}

/**
 * Generate add-ons list for specific package
 *
 * @param string $release A release code
 * @param string $package A package code
 * @return array
 */
function addons($release,$package)
{

    global $db;
    $sql="select a.filename from addons a
    inner join packages p on a.package=p.id
    inner join `releases` r on r.id=p.release
    where p.code=\"$package\" and r.release=\"$release\"";
    //var_dump($sql);
    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    foreach($x as $k=>$v)
    {
            $deps[]=$v['filename'];
    }
    return $deps;

}

/**
 * Generate nestings list for specific package
 *
 * @param string $release A release code
 * @param string $package A package code
 * @return array
 */
function nestings($release,$package)
{
    global $db;
    $sql="select dp.code, dd.code child from nestings d
    inner join packages dp on d.parent=dp.id
    inner join packages dd on d.child=dd.id
    inner join `releases` r on r.id=dp.release
    where dp.code=\"$package\" and r.release=\"$release\"
    ";
    //var_dump($sql);
    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    foreach($x as $k=>$v)
    {
            $deps[]=$v['child'];
    }
    return $deps;
}

/**
 * Generate comments list for specific package
 *
 * @param string $release A release code
 * @param string $package A package code
 * @return array
 */
function comments($release,$package)
{
    global $db;
    $sql="select p.code, c.text text from comments c
    inner join packages p on c.package=p.id
    inner join `releases` r on r.id=p.release
    where p.code=\"$package\" and r.release=\"$release\"
    ";
    //var_dump($sql);
    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    foreach($x as $k=>$v)
    {
            $deps[]=base64_encode($v['text']);
    }
    return $deps;
}

/**
 * Generate distributed build support code for shell script
 *
 * @param bool $localbuild Local build mode
 * @return string
 */
function distributedBuildInit($localbuild=0)
{
    $s= "";

    if(!$localbuild)
    {
        $s.= "echo \"Initializing distributed build environment... \"\n";
        $s.= "if [[ \"\$ULFS_ICECC\" == \"YES\" ]]\n";
        $s.= "then\n";
        $s.= "    export PATH=\"\$ULFS_ICECC_PATH:\$PATH\"\n";
        $s.= "    echo \"ICECC\"\n";
        $s.= "fi\n";
        $s.= "\n";
    }

    $s.= "echo \"Environment debug...\"\n";
    $s.= "echo \"PATH: \$PATH\"\n";
    $s.= "echo \"MAKEFLAGS: \$MAKEFLAGS\"\n";
    $s.= "echo \"NINJAJOBS: \$NINJAJOBS\"\n";
    $s.= "env | grep ULFS\n";
    $s.= "\n\n";

    return $s;
}

/**
 * Generate configuration load code for shell script
 *
 * @return string
 */
function loadConfig()
{
    echo "echo \"loading environment settings(profile)\"\n";
    echo ". /etc/profile\n";
    echo "echo \"checking config file\"\n";
    echo "if [ -f \$ULFS_CONFIG_FILE ]\n";
    echo "then\n";
    echo "echo \"loading config file \$ULFS_CONFIG_FILE...\"\n";
    echo ". \$ULFS_CONFIG_FILE\n";
    echo "fi\n";
}

/**
 * Generate dependencies list for archpackage
 *
 * @param string $release    A release code
 * @param string $arch       An architecture code
 * @param string $package    A package code
 * @param string $dependency A dependency code
 * @return array
 */
function archpkgdependances($release,$arch,$package,$dependance="")
{
    global $db;
    //$db=$Yaps->Ulfs->db;
    $d="";
    if($dependance)
    {
        $d=" and dd.code=\"$dependance\"";
    }
    $sql="
    select dp.code, dd.code dependance, ad.weight, da.code arch, dd.id from architectures_dependances ad
    inner join architectures_packages adp on ad.package=adp.id
    inner join packages dp on dp.id=adp.package
    inner join architectures_packages `add` on ad.dependance=`add`.id
    inner join `releases` r on r.id=dp.release
    inner join packages dd on dd.id=`add`.package
    inner join architectures pa on pa.id=adp.architecture
    inner join architectures da on da.id=`add`.architecture
    where pa.code=\"$arch\" and dp.code=\"$package\" and r.release=\"$release\" $d
    order by ad.weight, ad.dependance
    ";

    //var_dump($db);
    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    //$x=array();
    foreach($x as $k=>$v)
    {
        $deps[]=array(
            "code"=>$v['dependance'],
            "weight"=>$v['weight'],
            "arch"=>$v['arch'],
            "id"=>$v['id']
        );
    }
    //var_dump($deps);

    return $deps;

}

/**
 * Generate packages templates list for specific release
 *
 * @param string $release A release code
 * @return array
 */
function templates($release)
{
    global $db;
    $sql="select t.id, r.id `release_id`, r.release `release`, t.code, t.description, t.configure, t.build, t.install from packages_templates t
    left join releases r on r.id=t.release
    where r.release=\"".addslashes($release)."\"";

    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    return $x;
}


/**
 * Generate architectures list
 *
 * @return array
 */
function architectures()
{
    global $db;
    $sql="select id,code,description from architectures";

    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    return $x;
}

/**
 * Get architecture id for specific architecture code
 *
 * @param string $code    An architecture code
 * @return integer
 */
function getArchId($code)
{
    global $db;
    $sql="select id from architectures where code=\"$code\"";

    $db->execute($sql);
    $deps=array();
    $x=$db->dataset;
    foreach($x as $k=>$v)
    {
        return $v['id'];
    }
}


/**
 * Get architecture packages for specific package
 *
 * @param string $release A release code
 * @param string $package A package code
 * @return array
 */
function pkgarchpackages($release,$package)
{
    global $db;

    $sql="select a.code, ap.configure, ap.build, ap.install
    from packages p left join releases r on p.release=r.id
    inner join architectures_packages ap on ap.package=p.id
    left join architectures a on ap.architecture=a.id
    where r.`release`=\"$release\" and p.code=\"$package\"";


    $db->execute($sql);
    $res=array();

    $x=$db->dataset;

    foreach($x as $k=>$v)
    {
            $res[]=array(
                "arch"=>$v['code'],
                "configure"=>$v['configure'],
                "build"=>$v['build'],
                "install"=>$v['install']
            );
    }

    return $res;

}

/**
 * Get template id for specific template
 *
 * @param string $code    A template code
 * @param string $release A release code
 * @return int
 */
function getTemplateID($code="",$release)
{
    global $db;
    //$res="";

    $sql="select t.id from packages_templates t
    left join releases r on t.`release`=r.id
    where r.`release`=\"".addslashes($release)."\" and t.code=\"".addslashes($code)."\"";
    $db->execute($sql);

    foreach($db->dataset as $row)
    {
        //$res=
        //echo $row['id'];
        return $row['id'];
    }
}

