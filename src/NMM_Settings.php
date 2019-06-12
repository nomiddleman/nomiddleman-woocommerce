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

	public function get_customer_gateway_message() {
		$paymentLabelKey = 'payment_label';
		if (is_array($this->settings)) {
			if (array_key_exists($paymentLabelKey, $this->settings)) {
				return $this->settings[$paymentLabelKey];
			}
		}

		return 'Pay with cryptocurrency';
	}

	public function get_customer_payment_message($crypto) {
		$paymentMessageKey = 'payment_message_html';		

		if (is_array($this->settings)) {
			if (array_key_exists($paymentMessageKey, $this->settings)) {
				return $this->settings[$paymentMessageKey];
			}
		}

		return 'Once you have paid, please check your email for payment confirmation.';
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

	public function get_hd_mode($cryptoId) {
		return apply_filters('nmm_hd_mode', '0', $cryptoId);
	}

	public function get_markup($cryptoId) {
		$markupKey = $cryptoId . '_markup';
		if (is_array($this->settings)) {
			if (array_key_exists($markupKey, $this->settings)) {
				return trim($this->settings[$markupKey]);
			}
		}

		return '0.0';
	}

	public function get_hd_processing_percent($cryptoId) {
		$hdPercentKey = $cryptoId . '_hd_percent_to_process';

		if (is_array($this->settings)) {
			if (array_key_exists($hdPercentKey, $this->settings)) {
				return $this->settings[$hdPercentKey];
			}
		}
		
		return '0.99';
	}

	public function get_hd_required_confirmations($cryptoId) {
		$hdConfirmationsKey = $cryptoId . '_hd_required_confirmations';
		
		if (is_array($this->settings)) {
			if (array_key_exists($hdConfirmationsKey, $this->settings)) {
				return round($this->settings[$hdConfirmationsKey]);
			}			
		}		

		return '2';		
	}

	public function get_hd_cancellation_time($cryptoId) {
		$hdCancellationKey = $cryptoId . '_hd_order_cancellation_time_hr';

		if (is_array($this->settings)) {
			if (array_key_exists($hdCancellationKey, $this->settings)) {
				return $this->settings[$hdCancellationKey];
			}			
		}

		return '24';		
	}

	public function get_autopay_processing_percent($cryptoId) {
		$autopayPercentKey = $cryptoId . '_autopayment_percent_to_process';

		if (is_array($this->settings)) {
			if (array_key_exists($autopayPercentKey, $this->settings)) {
				return $this->settings[$autopayPercentKey];
			}	
		}

		return '0.999';		
	}

	public function get_autopay_required_confirmations($cryptoId) {
		$autopayConfirmationsKey = $cryptoId . '_autopayment_required_confirmations';

		if (is_array($this->settings)) {
			if (array_key_exists($autopayConfirmationsKey, $this->settings)) {
				return round($this->settings[$autopayConfirmationsKey]);
			}
		}
		
		return '2';
	}

	public function get_autopay_cancellation_time($cryptoId) {
		$autopayCancellationKey = $cryptoId . '_autopayment_order_cancellation_time_hr';

		if (is_array($this->settings)) {
			if (array_key_exists($autopayCancellationKey, $this->settings)) {
				return $this->settings[$autopayCancellationKey];
			}			
		}

		return '24';
	}
	
	public function price_api_selected() {
		$priceApiKey = 'selected_price_apis';
		
		if (is_array($this->settings)) {
			if (array_key_exists($priceApiKey, $this->settings)) {
				if (is_array($this->settings[$priceApiKey])) {
					if (count($this->settings[$priceApiKey]) > 0) {
						return true;
					}
				}
			}
		}
		
		return false;
	}

	public function _get_mode($cryptoId) {
		$modeKey = $cryptoId . '_mode';

		if (is_array($this->settings)) {
			if (array_key_exists($modeKey, $this->settings)) {
				return $this->settings[$modeKey];
			}
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