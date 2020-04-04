<?php
//name: Emulate other route example
//description: see random page from app folder

$urls = array(
    'samples/mvc',
    'samples/mvc/lifecycle-example',
    'samples/mvc/urls',
);
$random_url = $urls[mt_rand(0, count($urls) - 1)];

c\mvc::controllerRoute($random_url);
