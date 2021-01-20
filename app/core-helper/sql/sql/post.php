<?php
if (c\forms::isSubmit()) {
    c\forms::setData();
    $data = c\forms::getData();

    if ($data['sql']) {
        try {

            if (isset($_POST['execute']) or isset($_POST['export'])) {
                c\tables::$data = c\db::ea($data['sql'], [], $db);
                if (c\tables::$data[0]) {
                    c\tables::$header = c\datawork::keysToKeyVals(c\tables::$data[0]);
                }
                if (isset($_POST['export'])) {
                    c\tables::writesheetXlsx();
                    c\xlsx::generateXlsx();
                }
            } elseif (isset($_POST['explain'])) {
                c\tables::$data = c\db::explain($data['sql'], [], $db);
                c\tables::$header = c\datawork::valsToKeyVals([
                    'id',
                    'select_type',
                    'table',
                    'type',
                    'possible_keys',
                    'key',
                    'key_len',
                    'ref',
                    'rows',
                    'filtered',
                    'Extra'
                ]);
            }
        } catch (\Throwable $th) {
            c\error::addError($th->getMessage());
        }
    }
}
