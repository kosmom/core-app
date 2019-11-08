<?php
c\mvc::$noBody = true;
c\mvc::$noHeader = true;
c\mvc::layoutStartWith(__DIR__);
c\mvc::clearScriptsFull();
c\mvc::$search_css_js=false;
c\mvc::$search_config=false;
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
$result = ['code' => 0, 'message' => 'OK'];

function apiErrorHandler(int $errno)
{
    if ($errno < 8) return;
    c\error::log('logs/errors-' . date('Y-m-d') . '.log', date('d.m.Y H.i.s').': '.c\request::url());
    $result['message'] = 'runtime error';
    $result['code'] = 99;
    die(c\input::jsonEncode($result));
}

function apiExceptionHandler($exc, $code=0)
{
    c\error::log('logs/errors-' . date('Y-m-d') . '.log', date('d.m.Y H.i.s') .': '.c\request::url(). ': ' . $exc->getMessage() . print_r($exc->getTrace(), true));
    $result['message'] = $exc->getMessage();
    $result['code'] = $exc->getCode() ? $exc->getCode() : 99;
    die(c\input::jsonEncode($result));
}

set_error_handler('apiErrorHandler', E_ERROR);
set_exception_handler('apiExceptionHandler');