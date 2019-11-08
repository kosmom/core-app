<?php
c\mvc::$noBody=false;
c\mvc::$noHeader=false;
c\mvc::addCss('bootstrap4');
$basedir='app/api/';

$plugin_dir=null;
$source_dir=$_GET['dir'];
if (empty($source_dir))$source_dir='';
$src_dir=$source_dir;
if (is_dir($basedir.$src_dir)){
	$dir = opendir($basedir.$src_dir);
	$out=[];
	while(false !== ( $file = readdir($dir)) ) {
		if ($file=='.' or $file=='..')continue;
		if (!is_dir($basedir.$src_dir . '/' . $file) )				continue;
		$out[$file]=['path'=>$src_dir . '/' . $file];
		if (file_exists($basedir.$src_dir . '/' . $file.'/index.php')){
			$out[$file]['atime']= fileatime ($basedir.$src_dir . '/' . $file.'/index.php');
		}
		// check of subdirs
		$dir2 = opendir($basedir.$src_dir . '/' . $file);
		while(false !== ( $file2 = readdir($dir2)) ) {
			if ($file2=='.' or $file2=='..')continue;
			if (is_file($basedir.$src_dir . '/' . $file.'/'.$file2))continue;
			$out[$file]['has_subdir']= true;
			break;
		}
	}
	closedir($dir);
}

if (!$out){
	// empty dir. show dir upper
	$src_dir=substr($source_dir,0, strrpos($source_dir, '/'));
	if (is_dir($basedir.$src_dir)){
		$dir = opendir($basedir.$src_dir);
		$out=[];
		while(false !== ( $file = readdir($dir)) ) {
			if ($file=='.' or $file=='..')continue;
			if (!is_dir($basedir.$src_dir . '/' . $file) )continue;
			$out[$file]=['path'=>$src_dir . '/' . $file];
			if (file_exists($basedir.$src_dir . '/' . $file.'/index.php')){
				$out[$file]['atime']= fileatime ($basedir.$src_dir . '/' . $file.'/index.php');
			}
			// check of subdirs
			$dir2 = opendir($basedir.$src_dir . '/' . $file);
			while(false !== ( $file2 = readdir($dir2)) ) {
				if ($file2=='.' or $file2=='..')continue;
				if (is_file($basedir.$src_dir . '/' . $file.'/'.$file2))continue;
				$out[$file]['has_subdir']= true;
				break;
			}
		}
		closedir($dir);
	}
}
if ($_GET['sort']=='date_change'){
	uasort($out,function($a,$b){
		return ($a['atime']>$b['atime'])?-1:1;
	});
}else{
	ksort($out);
}

// find out folder content
$drafts=[];
$contents=[];
$exceptions=[];
$filenames=[
	'config'=>'Конфигурация',
	'middleware'=>'Мидлвар',
	'index'=>'Логический файл',
];
foreach ($filenames as $filename=>$filetitle){
	if (!file_exists($basedir.$source_dir.'/'.$filename.'.php'))continue;
	$tokens=token_get_all(file_get_contents($basedir.$source_dir.'/'.$filename.'.php'));
	foreach ($tokens as $key=> $token){
		if ($token[0]==377 or $token[0]==378){
			// comment
			$contents[$filename][]=trim($token[1]);
		}elseif ($token[0]==319 && $token[1]=='Exception'){
			$exceptions[$filename][]=trim($tokens[$key+2][1]);
		}else{
			$drafts[$filename][]=$token;
		}
	}
	if ($exceptions[$filename])$exceptions[$filename]=array_unique($exceptions[$filename]);
}
$drafts=[];

$curdir='';
foreach (explode('/',$src_dir) as $dir){
	$curdir=trim($curdir.'/'.$dir,'/');
	if (file_exists($basedir.$curdir .'/doc.php')){
		$plugin_dir= $basedir.$curdir;
	}
}	
if ($plugin_dir){
	include $plugin_dir.'/doc.php';
}