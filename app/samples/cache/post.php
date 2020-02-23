<?php
if ($clearTimerForm->isSubmit()){
	c\cache::clear(CORE_SAMPLE_TIMER_KEY);
	c\cache::clear(CORE_SAMPLE_TIMER_DAY_END_KEY);
	c\error::addSuccess('Times successfully cleared')->redirect();
}

if ($removeTimerForm->isSubmit()){
	c\cache::deleteMultiple(c\cache::getList('core-sample-'));
	c\error::addSuccess('Caches successfully removed')->redirect();
}