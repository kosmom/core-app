<?php
if (c\core::$env=='d'){
	//enable debug mode
	if (isset($_GET['debug']))$_SESSION['debug']=$_GET['debug'];
	if (isset($_GET['ws']))$_SESSION['ws']=$_GET['ws'];
	if (isset($_SESSION['debug']))c\core::$debug=$_SESSION['debug'];
	
	// live edit mode. run "php index.php app\core-helper\ws" on local server
	if (c\core::$debug && $_SESSION['ws']){
		c\core::$version=time(); // clear static cache
		c\mvc::addJsVar('core_debug_ws', 'ws://localhost:8889');
		c\mvc::addJsVar('core_debug_ws_latency', 1000); // if remote server saver
		c\mvc::addJs('js/core_ws.js');
		c\mvc::addJs('jquery');
	} 
}
if (!c\core::$debug){
	//logging errors at prod mode
	set_error_handler('coreErrorHandler',E_ERROR);
	set_exception_handler('coreExceptionHandler');
}