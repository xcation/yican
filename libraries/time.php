<?php
class Time {
	public function add_hour($openTime, &$closeTime) {
		if (strtotime($openTime) > strtotime($closeTime))
			$closeTime = date('H:i:s', strtotime('+1 hour', $closeTime));
	}
}


?>