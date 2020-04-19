<?php
// Name: Code debugger panel
// Version: 0.1


c\mvc::$noBody=false;
c\mvc::$noHeader=false;
c\mvc::addComponent('bootstrap4');

$path='logs/debugger';

$log_files=c\filedata::filelist($path,'',function($filename){
	return basename($filename);
});
if ($_GET['file']){
	$current_file=$_GET['file'];
	for ($a=0;$a<10;$a++){
		$contents[]= c\input::jsonDecode(c\filedata::readLastDataPart($path.'/'.$current_file));
	}
}