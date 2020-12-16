<?php
$file='tmp/depmap_0.1.svg';
if(!file_exists($file)){
header("Status: 404");
exit();
}
echo(file_get_contents($file));
