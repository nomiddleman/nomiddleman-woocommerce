<?php

class NMM_Transaction {
	private $amount;
	private $confirmations;
	private $timeStamp;
	private $hash;

	public function __construct($amount, $confirmations, $timeStamp, $hash) {
		$this->amount = $amount;
		$this->confirmations = $confirmations;
		$this->timeStamp = $timeStamp;
		$this->hash = $hash;
	}

	public function get_amount() {
		return $this->amount;
	}

	public function get_confirmations() {
		return $this->confirmations;
	}

	public function get_time_stamp() {
        $time = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $this->timeStamp);
	    if ($time) {
	        return $time->getTimestamp();
        }
		return $this->timeStamp;
	}

	public function get_hash() {
		return $this->hash;
	}
}

?>