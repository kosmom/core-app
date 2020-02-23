<?php
//name: Samples

if (c\core::$env==='p')die('not work in production mode');

if (!session_id())session_start();

c\mvc::layoutParentRemove();

c\mvc::addCss('https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');