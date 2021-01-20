<?php

$table = new c\table();

$table->header = c\datawork::describeToTableHeader($describe);

if ($pk) {
    $table->header['_opts'] = [
        'name' => '',
        'fill' => function ($row) use ($pk) {
            return '<a href="' . c\mvc::getUrl(__DIR__) . 'edit/' . $row[$pk] . c\input::getLinkParams() . '">✏️</a>';
        }
    ];
}

$sql = "SELECT * from " . $tableName . ($table->sort() ? (" order by " . $table->sort() . ' ' . $table->order()) : '');
$table->data = c\db::query($sql);
