<?php

$id = c\mvc::routeNext();

$form = new c\form();
$form->method('post');

$form->addFields(c\datawork::describeToForm($describe));
$form->addSubmitField('save');

$sql = 'SELECT * from ' . $tableName . ' where ' . $pk . '=:id';
$form->setDataForce(c\db::ea1($sql, ['id' => $id]));
