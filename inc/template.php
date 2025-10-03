<?php
/**
 * ULFS Packages Web-Application
 *
 * Site template
 *
 * This file is used to render final web-page
 */

//get output buffer contents
$content=ob_get_contents();
//stop output buffering
ob_end_clean();
//render web-page
?>
<html>
<head><title>UmVirt LFS Packages</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
table.packages td.error {color:#900;}
table.packages td.ok {color:#060;}
</style>
</head>
<body>
<?php echo $content;?>
</body>

</html>
