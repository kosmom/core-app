<?php
//name: Debug

c\core::$debug = 1;

c\debug::consoleLog('standart debug message');
c\debug::consoleLog('warning debug message', c\error::WARNING);
c\debug::consoleLog('error debug message', c\error::ERROR);
c\debug::consoleLog('success debug message', c\error::SUCCESS);

c\debug::trace('debug with pointer ' . __LINE__ . 'th string');

$arrayVar = array(
	'key1' => 'item1',
	'key2' => array(
		'subitem' => array(
			'key1 key2' => 'item12'
		)
	)
);
c\debug::dir($arrayVar);

$table = [
	['row1' => 'cell11', 'row2' => 'cell12', 'row3' => 'cell13'],
	['row1' => 'cell21', 'row2' => 'cell22', 'row3' => 'cell23'],
	['row1' => 'cell31', 'row2' => 'cell32', 'row3' => 'cell33'],
];
c\debug::table($table);

$result[] = 'test2';
