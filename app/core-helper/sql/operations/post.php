<?php
if (c\forms::isSubmit()) {
    c\forms::setData();
    $data = c\forms::getData();

    if ($data['tablename']) {
        try {
            $sql = "rename table " . c\db::wrapper($tableName, $db) . ' to ' . c\db::wrapper($data['tablename'], $db);
            c\db::e($sql, [], $db);
            c\error::addSuccess('rename successfull')->redirect(c\input::getLinkParams(['table' => $data['tablename']]));
        } catch (\Throwable $th) {
            c\error::addError($th->getMessage());
        }
    }
}
