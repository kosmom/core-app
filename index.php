<?php
session_start();

date_default_timezone_set('Europe/Moscow');

error_reporting(E_ERROR);

//uncomment if app use at core outside mode
//require("core-path/core.php");

if (file_exists('vendor/autoload.php'))require 'vendor/autoload.php';

error_reporting(E_ALL);

c\core::$charset=c\core::UTF8;
c\core::$lang='ru';
c\core::$env='d';
c\core::$version='1';



function coreErrorHandler($errno){
	if ($errno<8)return;
	print '<div class="error">Some errors. '.($_POST?'Do not close page to not loose data ':'').'Please wait several minutes and refresh page.</div>';
	c\error::log('logs/errors-'.date('Y-m-d').'.log',date('d.m.Y H.i.s').': '.print_r(debug_backtrace(),true));
}
function coreExceptionHandler($ex){
		print '<div class="error">Some errors. '.($_POST?'Do not close page to not loose data ':'').'Please wait several minutes and refresh page.</div>';
		c\error::log('logs/errors-'.date('Y-m-d').'.log',date('d.m.Y H:i:s').': '.$ex->getMessage().PHP_EOL.print_r(debug_backtrace(),true));
}

if (c\core::$env=='d'){
	//enable debug mode
	if (isset($_GET['debug']))$_SESSION['debug']=$_GET['debug'];
	if (isset($_SESSION['debug']))c\core::$debug=$_SESSION['debug'];
	
	// live edit mode. run "php ws.php" on local server
	if (c\core::$debug){
		c\core::$version=time(); // clear static cache
		c\mvc::addJsVar('core_debug_ws', 'ws://localhost:8889');
		c\mvc::addJsVar('core_debug_ws_latency', 1000); // if remote server saver
		c\mvc::addJs('js/core_ws.js');
		c\mvc::addJs('jquery');
	} 
}
if (!c\core::$bebug){
	//logging errors at prod mode
	set_error_handler('coreErrorHandler',E_ERROR);
	set_exception_handler('coreExceptionHandler');
}

c\mvc::$js_dict['core_ajax']=array('url'=>'/js/core_ajax.jsgz','requires'=>'jquery');

c\core::$data['include_dir']='models';

//c\core::$data['db']='';
//c\core::$data['mail']='';

c\mvc::init(__DIR__);

do include c\mvc::content(); while (c\mvc::check());
c\mvc::header();
require c\mvc::viewPage(false);
c\mvc::footer();
