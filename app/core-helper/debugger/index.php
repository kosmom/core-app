<?php
// Name: Code debugger panel
// Version: 0.2


c\mvc::$noBody=false;
c\mvc::$noHeader=false;
c\mvc::addComponent('bootstrap4');

$path='logs/debugger';

$log_files=c\filedata::filelist($path,'',function($filename){
	return basename($filename);
});
$current_file=$_GET['file'];
if ($_GET['file'] && file_exists($path.'/'.$current_file)){
	for ($a=0;$a<100;$a++){
		$contents[]= c\input::jsonDecode(c\filedata::readLastDataPart($path.'/'.$current_file));
	}
}