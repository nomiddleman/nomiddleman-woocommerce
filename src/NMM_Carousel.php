<?php

class NMM_Carousel {
	private $buffer;
	private $cryptoId;
	private $currentIndex;
	private $carouselSeats;

	public function __construct($cryptoId) {		
		$this->cryptoId = $cryptoId;

		$carouselRepo = new NMM_Carousel_Repo();
		$this->buffer = $carouselRepo->get_buffer($cryptoId);
		$this->currentIndex = $carouselRepo->get_index($cryptoId);
		$this->carouselSeats = 5;
	}

	public function get_next_address() {
		// Set the next address
		$nextAddress = $this->buffer[$this->currentIndex];

		// If we have an invalid address, increment the index and try the next one
		while (!NMM_Cryptocurrencies::is_valid_wallet_address($this->cryptoId, $nextAddress)) {
			
			$this->increment_current_index($this->cryptoId);
				
			$nextAddress = $this->buffer[$this->currentIndex];
		}

		// increment once after we have found a valid address so we start at the correct index next time
		$this->increment_current_index($this->cryptoId);

		$carouselRepo = new NMM_Carousel_Repo();

		// update the index in the database
		$carouselRepo->set_index($this->cryptoId, $this->currentIndex);

		return $nextAddress;
	}

	private function increment_current_index($cryptoId) {
		// TODO: make sure this is right... not doing any testing
		$reduxOptions = get_option(NMM_REDUX_ID);
		
		$seatCount = count($reduxOptions[$cryptoId . '_addresses']);
		
		// increment by one if we aren't at the last index
		if ($this->currentIndex >= 0 && $this->currentIndex < ($seatCount - 1)) {
			$this->currentIndex = $this->currentIndex + 1;
		}
		// if we are at the last index then start over
		elseif ($this->currentIndex == ($seatCount - 1)) {
			$this->currentIndex = 0;
		}
		else {			
			NMM_Util::log(__FILE__, __LINE__, 'Invalid current index! Something went wrong, please contact plug-in support.');
		}
	}
}

?>