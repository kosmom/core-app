<?php
switch (c\ajax::getAct()) {
    case 'waitSecAndCallAlert':
        sleep(1);
        c\error::addSuccess('message from backend');
        break;
    case 'callCustomCallback':
        c\ajax::addAction('some_callback', 'current timestamp: ' . time());
        break;
    case 'redirectToMain':
        c\error::redirect('/');
        break;
    case 'callWithClientData':
        c\ajax::addAction('message', array('message' => 'incoming from client vars in JSON: ' . c\input::jsonEncode(c\ajax::getVars())));
        break;
    case 'loadContent':
        die('Loaded from backend content. Current timestamp: ' . time());
}
