<?php
//name: Hidden route example

c\mvc::controllerPage(__DIR__);

if ($_GET['a'] == '1') {
    c\mvc::controllerPageForce(__DIR__, '_hidden-route');
}
