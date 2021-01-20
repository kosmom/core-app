<?php
//name: SQL
//need_table: false

c\forms::addField('sql', ['render' => 'teatarea']);
c\forms::addSubmitField(['value' => 'execute'], 'execute');
c\forms::addSubmitField(['value' => 'explain'], 'explain');
c\forms::addSubmitField(['value' => 'export'], 'export');
c\forms::method('post');
