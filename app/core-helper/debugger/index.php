<?php
// Name: Code debugger panel
// Version: 0.3


c\mvc::$noBody=false;
c\mvc::$noHeader=false;
c\mvc::addComponent('bootstrap4');

$path='logs/debugger';

$log_files=c\filedata::filelist($path,'',function($filename){
	return basename($filename);
});
$current_file = $_GET['file'];
$full_name = $path . '/' . $current_file;

$offset = null;
if (isset($_GET['offset']))$offset = $_GET['offset'];
if ($_GET['file'] && file_exists($full_name)){
	$handler = c\filedata::getHandler($full_name, $offset);
	for ($a = 0; $a < 100; $a++){
		$contents[] = c\input::jsonDecode(c\filedata::readLastDataPart($full_name));
		if (!ftell($handler))break;
    }
}