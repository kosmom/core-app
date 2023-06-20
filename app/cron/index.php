<?php
c\mvc::$noBody = true;
c\mvc::$noHeader = true;
c\mvc::layoutStartWith(__DIR__);
c\mvc::clearScriptsFull();
c\mvc::$search_css_js = false;
c\mvc::$search_config = false;

c\core::$data['dump_file'] = 'logs/debugger/cron.log';