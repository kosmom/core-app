<?php
// update all migrations after current
$sql="select max(title) max_title, max(batch) max_batch from migration";
$migrations=c\db::ea1($sql);
$next_batch=$migrations['max_batch']+1;
$files= c\filedata::filelist('migrations','',function ($name){
	return basename($name);
});
if ($migrations){
	$files = array_filter($files,function($filename) use ($migrations){
		return $filename>$migrations['max_title'];
	});
}
$out=[];
foreach ($files as $file){
	$up=function(){};
	c\debug::timer();
	require 'migrations/'.$file;
	call_user_func($up);
	// add migration to db
	c\db::setData('migration',[
		'title'=> $file,
		'date_migrate'=>time(),
		'batch'=>$next_batch
	]);
	$counter++;
	$out[$file]= c\debug::timer();
}
if (!$counter)die('already update');

$arg=false;
if (c\request::isCmd()){
	$args=c\cli::argv();
	$arg=$args[0];
	if ($arg=='with-models'){
		require __DIR__.'/../../make-models/index.php';
	}
}

die('migration update successfully. '.$counter.' migrations updated: '. print_r($out,true));