<?php
//var_dump(c\cli::args());
if (c\request::isCmd()){
	$args=c\cli::argv();
	$title=$args[0];
}
if (!$title){
	$title=c\mvc::routeNext();
}

if (!$title)die('not set migration name');
//$table='validate';

file_put_contents('migrations/'.date('Y-m-d-H-i-s').'-'.$title.'.php', file_get_contents(__DIR__.'/template.phtml'));
die('migration successfully created');