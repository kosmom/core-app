<?php
//name: Code debugger panel
//version: 0.5


c\mvc::$noBody = false;
c\mvc::$noHeader = false;
c\mvc::addComponent('bootstrap4');


$log_files = c\filedata::filelist($path, '', function ($filename) {
    return basename($filename);
});

$offset = null;
if (isset($_GET['offset'])) {
    $offset = $_GET['offset'];
}

$debugger__DIR__ = __DIR__;

c\mvc::controllerPage(__DIR__);

$group_counter = 0;