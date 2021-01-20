<?php
//name: Структура
//need_table: true

$table = new c\table();

$table->header = [
    'pk' => 'pk',
    'name' => 'Название',
    'typerange' => 'Длина',
    'type' => 'Тип',
    'default' => 'по умолчанию',
    'unsigned' => 'Беззнаковый',
    'notnull' => 'Не null',
    'zerofill' => 'zerofill',
    'autoincrement' => 'autoincrement',
    'comment' => 'комментарий',
];
$describe = c\dbwork::describeTable($tableName, '', $db);
foreach ($describe['data'] as $key => $val) {
    $describe['data'][$key]['name'] = $key;
}
foreach ($describe['primary_key'] as $name) {
    $describe['data'][$name]['pk'] = '🔑';
}
$table->data = $describe['data'];
