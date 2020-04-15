<?php
if (count(c\filedata::filelist('migrations')))die('migrations already started');
mkdir('migrations');
file_put_contents('migrations/'.date('Y-m-d-H-i-s').'-migration-start.php', file_get_contents(__DIR__.'/template.phtml'));
die('migrations created');