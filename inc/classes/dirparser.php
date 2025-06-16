<?php
/**
 * ULFS Packages Web-Application
 *
 * Directory tree parser
 *
 * This class is used to get directory tree information
 */
class dirparser{

    public $dir;
    public $ignorefiles;
    public $size=0;

    /**
     * Check that file name is match to patterns
     * @param string $file     A filename
     * @param array  $patterns A PREG-patterns
     */
    function checkfilename($file, $patterns)
    {
        $chk=false;
        foreach($patterns as $pattern)
        {
            if(preg_match($pattern, $file))
            {
                $chk=true;
            }
        }
        return $chk;
    }


    /**
     * Parse a directory
     *
     * @param string $subdir       A direcory to parse
     * @param array  $files        A files array
     * @param array  $ignorefiles  A list of files to ignore
     * @param int    $size         A total size counter
     * @param bool   $md5          To check MD5 checksum or not
     * @param int    $filescnt     A files counter
     * @param bool   $debug        Debugging mode
     */
    function parsedirtree($subdir,&$files,$ignorefiles, &$size, $md5=false, $filescnt=0, $debug=false)
    {
        //define root direcory
        $dir=$this->dir."/".$subdir;

        //if root direcory is directory
        if (is_dir($dir))
        {
            //open direcory
            if ($dh = opendir($dir))
            {
                //proccess each file in direcory
                while (($file = readdir($dh)) !== false)
                {
                    //if file is valid
                    if(($file!=".") and ($file!="..")and !$this->checkfilename($file,$ignorefiles))
                    {
                        //absolute path to file
                        $filepath=$dir.'/'.$file;
                        //relative path to file from defined root directory
                        $filesubpath=$subdir.'/'.$file;
                        //if file is direcory
                        if(is_dir($filepath))
                        {
                            //parse it recursively
                            $this->parsedirtree($filesubpath,$files,$ignorefiles,$size,$md5,$filescnt,$debug);
                        //in other case
                        }else{
                            //if debugging mode enabled
                            if($debug)
                            {
                                //show debugging information
                                $process="";
                                if($filescnt)
                                {
                                    $proccess='('.(count($files)+1).'/'.$filescnt.')';
                                }

                                echo "Proccessing file $file $proccess... ";
                            }
                            //get file information
                            $fileinfo=array("name"=>$file, "path"=>$subdir, "mtime"=>filemtime($filepath), "size"=>filesize($filepath));
                            //increase total size counter
                            $size+=$fileinfo['size'];

                            //if to check MD5 checksum
                            if($md5)
                            {
                                //calculate checksum
                                $fileinfo['md5']=md5_file($filepath);
                                $md5sum="";
                                //if checsum file is exisis
                                if(file_exists($filepath.".md5sum"))
                                {
                                    //read data from such file
                                    $md5sum=file_get_contents($filepath.".md5sum");
                                    $md5sum=substr($md5sum,0,strpos($md5sum," "));
                                }
                                $fileinfo['md5_']=$md5sum;
                            }

                            //add file to files list
                            $files[]=$fileinfo;

                            //if debugging mode enabled
                            if($debug)
                            {
                                //print "Ok".
                                echo "Ok\n";
                            }

                        }
                    }
                }
            }
        }
    }


    /**
     * Get files list for current root directory
     *
     * @return array
     */
    function getFiles()
    {
        //first scan: get summary to calculate process position
        $size=0;
        $files=array();
        $this->parsedirtree('',$files,$this->ignorefiles,$size);

        $files_count=count($files);
        $files_size=$size;

        $size=0;
        $files=array();

        //second scan: get detailed information
        $this->parsedirtree('',$files,$this->ignorefiles,$size,true,$files_count,true);

        return $files;
    }

}


