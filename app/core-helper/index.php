<?php
if (c\core::$env == 'p' && !c\request::isCmd()) {
    die('only in dev mode');
}

$coreHelperDir = __DIR__;

c\mvc::controllerPage(__DIR__);
