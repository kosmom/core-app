<?php

if ($_GET['file'] && file_exists($full_name)) {
    $handler = c\filedata::getHandler($full_name, $offset);
    $offset = ftell($handler);
    $out = c\input::jsonDecode(c\filedata::readLastDataPart($full_name));
    if ($out) {
        $content = $out + ['offset' => $offset];
    }
}
