<?php
//name: Cache

const CORE_SAMPLE_TIMER_KEY = 'core-sample-timer';
const CORE_SAMPLE_TIMER_DAY_END_KEY = 'core-sample-timer-day-end';

$cachedTimer = c\cache::get(CORE_SAMPLE_TIMER_KEY, function () {
    return time();
}, 60);
$cachedTimerUntilDayEnd = c\cache::get(CORE_SAMPLE_TIMER_DAY_END_KEY, function () {
    return time();
}, strtotime('tomorrow') - time());

$clearTimerForm = new c\form();
$clearTimerForm->addSubmitField('Clear timer');

$removeTimerForm = new c\form();
$removeTimerForm->addSubmitField('Remove caches');
