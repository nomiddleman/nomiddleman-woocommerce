<?php

class NMM_Settings {
	private $settings;
	
	public function __construct($settings) {
		$this->settings = $settings;
	}

	public function get_selected_cryptos() {		
		if (is_array($this->settings)) {
			if (array_key_exists('crypto_select', $this->settings)) {
				if (is_array($this->settings['crypto_select'])) {
					return $this->settings['crypto_select'];
				}			
			}
		}
		
		return [];
	}

	public function crypto_selected($cryptoId) {
		if (is_array($this->get_selected_cryptos())) {
			if (in_array($cryptoId, $this->get_selected_cryptos())) {
				return true;
			}
		}
		
		return false;
	}

	public function crypto_selected_and_valid($cryptoId) {
		$modeEnabled = $this->basic_enabled($cryptoId) || $this->autopay_enabled($cryptoId) || $this->hd_enabled($cryptoId);
		$cryptos = NMM_Cryptocurrencies::get();
		$validHd = $cryptos[$cryptoId]->has_hd();
		if ($this->hd_enabled($cryptoId) && !$validHd) {
			return false;
		}
		return $this->crypto_selected($cryptoId) && $modeEnabled;
	}

	public function get_valid_selected_cryptos() {
		$validSelectedCryptos = [];

		foreach (NMM_Cryptocurrencies::get_alpha() as $crypto) {
			if ($this->crypto_selected_and_valid($crypto->get_id())) {
				$validSelectedCryptos[] = $crypto;
			}
		}

		return $validSelectedCryptos;
	}

	public function basic_enabled($cryptoId) {
		return $this->_get_mode($cryptoId) === '0';
	}
	public function autopay_enabled($cryptoId) {
		return $this->_get_mode($cryptoId) === '1';
	}
	public function hd_enabled($cryptoId) {
		return $this->_get_mode($cryptoId) === '2';
	}
	public function get_addresses($cryptoId) {
		$addressesKey = $cryptoId . '_addresses';
		if (is_array($this->settings)) {
			if (array_key_exists($addressesKey, $this->settings)) {
				if (is_array($this->settings[$addressesKey])) {
					return $this->settings[$addressesKey];
				}
			}
		}
		
		return [];		
	}

	public function get_next_carousel_address($cryptoId) {
		$carousel = new NMM_Carousel($cryptoId);

		return $carousel->get_next_address();
	}

	public function get_mpk($cryptoId) {
		$mpkKey = $cryptoId . '_hd_mpk';
		if (is_array($this->settings)) {
			if (array_key_exists($mpkKey, $this->settings)) {
				return trim($this->settings[$mpkKey]);
			}
		}
		
		return '';		
	}

	public function get_hd_processing_percent($cryptoId) {
		if (!is_array($this->settings)) {
			return '0.995';
		}
		return $this->settings[$cryptoId . '_hd_percent_to_process'];
	}

	public function get_hd_required_confirmations($cryptoId) {
		if (!is_array($this->settings)) {
			return '2';
		}
		if (!array_key_exists($cryptoId . '_hd_required_confirmations', $this->settings)) {
			return '2';
		}
		return round($this->settings[$cryptoId . '_hd_required_confirmations']);
	}

	public function get_hd_cancellation_time($cryptoId) {
		if (!is_array($this->settings)) {
			return '24';
		}
		return $this->settings[$cryptoId . '_hd_order_cancellation_time_hr'];
	}

	public function get_autopay_processing_percent($cryptoId) {
		if (!is_array($this->settings)) {
			return '1.0';
		}
		return $this->settings[$cryptoId . '_autopayment_percent_to_process'];
	}

	public function get_autopay_required_confirmations($cryptoId) {
		if (!is_array($this->settings)) {
			return '2';
		}
		if (!array_key_exists($cryptoId . '_autopayment_required_confirmations', $this->settings)) {
			return '2';
		}
		return round($this->settings[$cryptoId . '_autopayment_required_confirmations']);
	}

	public function get_autopay_cancellation_time($cryptoId) {
		if (!is_array($this->settings)) {
			return '24';
		}
		return $this->settings[$cryptoId . '_autopayment_order_cancellation_time_hr'];
	}
	
	public function price_api_selected() {
		$priceApiKey = 'selected_price_apis';
		if (!is_array($this->settings)) {
			return false;
		}

		if (array_key_exists($priceApiKey, $this->settings)) {
			if (is_array($this->settings[$priceApiKey])) {
				if (count($this->settings[$priceApiKey]) > 0) {
					return true;
				}
			}
		}
		
		return false;
	}

	public function _get_mode($cryptoId) {
		$modeKey = $cryptoId . '_mode';
		if (!is_array($this->settings)) {
			return '';
		}
		if (array_key_exists($modeKey, $this->settings)) {
			return $this->settings[$modeKey];
		}
		
		return '';
	}

	public function add_consumed_tx($cryptoId, $address, $txHash) {
		$settingsKey = 'nmmpro_' . $cryptoId . '_transactions_consumed_for_' . $address;
		$consumedTxs = get_option($settingsKey, array());
		$consumedTxs[] = $txHash;
		update_option($settingsKey, $consumedTxs, false);
	}

	public function tx_already_consumed($cryptoId, $address, $txHash) {
		$settingsKey = 'nmmpro_' . $cryptoId . '_transactions_consumed_for_' . $address;
		$consumedTxs = get_option($settingsKey, array());
		
		if (in_array($txHash, $consumedTxs)) {
			return true;
		}

		return false;
	}
}

?>