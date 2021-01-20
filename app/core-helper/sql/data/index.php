<?php
//name: Данные
//need_table: true

$describe = c\dbwork::describeTable($tableName, '', $db);
if (sizeof($describe['primary_key']) == 1) {
    $pk = $describe['primary_key'][0];
}

c\mvc::controllerPage(__DIR__, '_list');
