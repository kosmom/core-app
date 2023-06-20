<?php
c\core::$env='t';

error_reporting(E_ALL);
ini_set("display_error", true);
ini_set("error_reporting", E_ALL);

$clear_tables=function(){
	$sql = "show tables";
	foreach (c\db::ec($sql) as $table){
		$sql="truncate table ".$table;
		c\db::e($sql);
	}
};

c\core::$data['dump_file'] = 'logs/debugger/tests.log';

function testErrorHandler(int $errno){
	//global $die,$header, $_GLOBAL_EXCEPTION;
    //if ($errno < 8) return;
	die('runtime error '.$errno. debug_print_backtrace());
}

function testExceptionHandler($exc, $code=0){
	global $_GLOBAL_EXCEPTION;
    echo 'test failed '.$exc->getMessage().' in '.$exc->getLine();
	$_GLOBAL_EXCEPTION=['file'=>$exc->getFile(),'line'=>$exc->getLine(),'message'=>$exc->getMessage(),'trace'=>$exc->getTrace()];
}

set_error_handler('testErrorHandler', E_ERROR);
set_exception_handler('testExceptionHandler');