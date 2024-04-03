<?php
//var_dump(c\cli::args());
if (c\request::isCmd()){
	$args=c\cli::argv();
	$title=$args[0];
}
if (!$title){
	$title=c\mvc::routeNext();
}

if (!$title)die(c\cli::COLOR_RED.'not set migration name'.c\cli::COLOR_RESET.PHP_EOL);
//$table='validate';

$result=file_put_contents('migrations/'.date('Y-m-d-H-i-s').'-'.$title.'.php', file_get_contents(__DIR__.'/template.phtml'));
if ($result){
    die('migration successfully created'.PHP_EOL);
}else{
    die(c\cli::COLOR_RED.'migration create error'.c\cli::COLOR_RESET.PHP_EOL);
}