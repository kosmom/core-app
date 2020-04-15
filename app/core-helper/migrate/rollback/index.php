<?php
// rollback 1 migration
$step=null;

if ($step===null){
	// downgrade last batch
	$sql="select max(batch) from migration";
	$max_batch=c\db::ea11($sql);
	$sql="select title from migration where batch=:batch order by id desc";
	$datas=c\db::ec($sql,['batch'=>$max_batch]);
}else{
	$sql="select title from migration order by id desc limit 0,".$step;
	$datas=c\db::ec($sql);
}
$out=[];
foreach ($datas as $file){
	if (!file_exists('migrations/'.$file)){
		echo 'migration '.$file.' not exitst for downgrade'.c\cli::EOL;
		continue;
	}
	c\debug::timer();
	$down=function(){};
	require 'migrations/'.$file;
	call_user_func($down);
	$sql="delete from migration where title=:title";
	c\db::e($sql,['title'=>$file]);
	$counter++;
	$out[$file]= c\debug::timer();
}

if (!$counter)die('nothing downgrade');
die('migration downgrade successfully. '.$counter.' migrations: '. print_r($out,true));