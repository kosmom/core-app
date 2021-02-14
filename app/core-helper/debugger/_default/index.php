<?php
$filterForm = new c\form();
$filterForm->formInline();
$filterForm->method('GET');
$filterForm->addField('file', array(
    'type' => 'hidden',
    'value' => $_GET['file']
));
$filterForm->addField('url', array(
    'label' => 'URL'
));
$filterForm->addSubmitField('Применить');

$filterForm->setData();
$filter = $filterForm->getData();

if ($_GET['file'] && file_exists($full_name)) {
    $handler = c\filedata::getHandler($full_name, $offset);
    for ($a = 0; $a < 100; $a++) {
        $offset = ftell($handler);
        $out = c\input::jsonDecode(c\filedata::readLastDataPart($full_name));
        if (!ftell($handler)) {
            break;
        }
        if ($filter['url']) {
            if (mb_strpos($out['server']['REQUEST_URI'], $filter['url']) === false) {
                $a--;
                continue;
            }
        }
        if ($out) {
            $contents[] = $out + ['offset' => $offset];
        }
    }
}
