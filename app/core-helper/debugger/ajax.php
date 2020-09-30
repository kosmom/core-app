<?php
switch (c\ajax::getAction()) {
    case 'repeat_request':
        $offset = $_POST['offset'];
		$data = c\input::jsonDecode(c\filedata::readLastDataPart($full_name, $offset));
		// make request with same props
        if (!$data) {
            throw new Exception('no source data received');
        }
		$result = c\curl::getContent(c\request::url($data['server']), $data['input']);
        c\ajax::addAction('result', $result);
        break;
}
c\ajax::render();
