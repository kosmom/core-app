<?php
//name: SQL panel

c\mvc::addCss('bootstrap4');

$db = null;

c\core::$data['db_exception'] = true;

$connections = c\db::getAllConnectSchemes();
if (sizeof($connections) == 1) {
    foreach ($connections as $key => $value) {
        $db = $key;
    }
} else {

    if (isset($_GET['db'])) {
        $db = $_GET['db'];
    }
    if (!isset($connections[$db])) {
        $db = null;
    }
}

if ($db) {
    $sql = "show tables";
    $tableList = c\db::ec($sql);
    $tableName = null;
    if ($_GET['table']) {
        $tableName = $_GET['table'];
        c\mvc::controllerPage(__DIR__, 'data');
    }
}
