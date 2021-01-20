<?php
if ($form->isSubmit()) {
    $form->setData();
    $data = $form->getData();

    c\db::setData($tableName, $data, true, $db);
    if (!c\error::count()) {
        c\error::addSuccess('save successfull')->redirect(c\input::getLinkParams());
    }
}
