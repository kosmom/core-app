<?php
/**
 * Search all config constructs as generate confix file
 */

/*
c\config()->module1='1';
//echo c\config()->$module1;

// Модуль 2 модуля 1
c\config()->module1()->module2='12';
c\config()->module1()->module2='12';
c\config()->module1()->module1=11;
echo c\config()->module1;
echo c\config()->module1()->module2;

$token=token_get_all(file_get_contents(__FILE__));
print_r($token);
*/

foreach (c\filedata::filelist('app') as $filename=>$t){
$token=token_get_all(file_get_contents($filename));
foreach ($token as $key=>$value){
	//if (is_string($value))continue;
	if ($value[1]=='config' && $value[0]==319 && $token[$key-1][1]=='\\' && $token[$key-2][1]=='c'){
		if ( $token[$key+1]=='(' && $token[$key+2]==')' && $token[$key+3][1]=='->' && $token[$key+4][0]==319){
			$comment=$token[$key-3][0]==377?trim(substr($token[$key-3][1],2)):true;
			$config1=$token[$key+4][1];
			if ($token[$key+5]=='(' && $token[$key+6]==')' && $token[$key+7][1]=='->' && $token[$key+8][0]==319){
				$configs1[$config1]=!isset($configs1[$config1])?$configs1[$config1]:$comment;
				$config2=$token[$key+8][1];
				$configs2[$config1][$config2]=(isset($configs2[$config1][$config2]) && is_string($configs2[$config1][$config2]))?$configs2[$config1][$config2]:$comment;
			}else{
				$configs1[$config1]=(isset($configs1[$config1]) && is_string($configs1[$config1]))?$configs1[$config1]:$comment;
			}
		}
	}
}
}
print_r($configs1);
print_r($configs2);

// make hint config files
$file= c\core::$data['include_dir'] . '/hint/config.php';
$begin = true;
$end = true;
$source_content = '';
$custom = '

';
if (file_exists($file)) {
	$source_content = file_get_contents($file);
	$begin = strpos($source_content, '/* BEGIN CUSTOM CODE */');
	$end = strpos($source_content, '/* END CUSTOM CODE */');

	$custom = (substr($source_content, $begin + strlen('/* BEGIN CUSTOM CODE */' . PHP_EOL),
	$end - $begin - 2 - strlen('/* END CUSTOM CODE */' . PHP_EOL)));
}

if ($begin && $end) {
        // generate hint model
        $content = '<?php
namespace c;

class config{

/* BEGIN CUSTOM CODE */' . PHP_EOL . $custom . '/* END CUSTOM CODE */' . PHP_EOL;
        foreach ($configs1 as $config_name => $config_comment) {
            if (is_string($config_comment)){
			$content .= '	/**
	 * ' . $config_comment . '
	*/

';
			}
	$content.='	var $' . $config_name . ';

';
        }
		foreach ($configs2 as $config1_name =>$config1_value){
	$content.='	function ' . $config1_name . '(){
		return new config_'.$config1_name.'();
	}

';
		}
$content.='}';

// make subclasses
	foreach ($configs2 as $config1_name=>$config1_value){
		$content.='

class config_'.$config1_name.'{
';
		foreach ($config1_value as $config2_name=>$config2_comment){
			if (is_string($config2_comment)){
			$content .= '	/**
	 * ' . $config2_comment . '
	 */
';
			}
			$content.='	var $' . $config2_name . ';

';
		}
		$content.='
}';
	}

	if ($content != $source_content) file_put_contents($file, $content);
	echo "Config hint created";
}