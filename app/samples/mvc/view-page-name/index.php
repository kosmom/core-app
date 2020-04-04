<?php
//name: View page example

$time = time();

if ($_GET['template'] && strlen($_GET['template']) == 1) {
    c\mvc::setViewPageName(__DIR__, 'template_' . $_GET['template']);
}
