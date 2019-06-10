<?php

class NMM_Validation {
	public static function validate_redux_options($newValues, $oldValues) {
		$oldSettings = new NMM_Settings($oldValues);
		$newSettings = new NMM_Settings($newValues);		

		$selectedCryptosChanged = $oldSettings->get_selected_cryptos() !== $newSettings->get_selected_cryptos();

		$atLeastOneInvalidCrypto = false;
		$errorMessages = [];

		foreach (NMM_Cryptocurrencies::get() as $crypto) {
			$invalidCryptoSettings = false;
			$cryptoId = $crypto->get_id();
			$cryptoName = $crypto->get_name();
			if ($newSettings->basic_enabled($cryptoId) || $newSettings->autopay_enabled($cryptoId)) {
				$carouselAddresses = [];
				$hasValidWalletAddress = false;
				$addresses = $newSettings->get_addresses($cryptoId);
				
				foreach ($addresses as $ind => $address) {
					if (NMM_Cryptocurrencies::is_valid_wallet_address($cryptoId, $address)) {
                        $carouselAddresses[] = trim($address);
                        $hasValidWalletAddress = true;
                    }                    
				}
				if (! $hasValidWalletAddress) {
					$invalidCryptoSettings = true;
					$atLeastOneInvalidCrypto = true;
					$errorMessages[] = $cryptoName . ' has no valid wallet addresses. Disabling ' . $cryptoName . '.';
				}
				else {
					$carouselRepo = new NMM_Carousel_Repo();
					$carouselRepo->set_buffer($cryptoId, $carouselAddresses);
				}
			}
			else if ($newSettings->hd_enabled($cryptoId)) {
				$mpk = $newSettings->get_mpk($cryptoId);

				if (NMM_Util::p_enabled()) {
					if (!NMM_Hd::is_valid_mpk($cryptoId, $mpk)) {						
						$invalidCryptoSettings = true;
						$atLeastOneInvalidCrypto = true;
						$errorMessages[] = $cryptoName . ' has an invalid HD MPK. Disabling ' . $cryptoName . '.';					
					}
				}
				else {
					if (NMM_Hd::is_valid_ypub($mpk) || NMM_Hd::is_valid_zpub($mpk)) {
						$invalidCryptoSettings = true;
						$atLeastOneInvalidCrypto = true;
						if (NMM_Hd::is_valid_mpk($cryptoId, $mpk)) {
							$errorMessages[] = 'Please use an xpub MPK. Disabling ' . $cryptoName . '.';                    
						}
						else {
							$errorMessages[] = $cryptoName . ' has an invalid HD MPK. Disabling ' . $cryptoName . '. If you have a wallet that supports this MPK, please contact us at <a href="mailto:support@nomiddlemancrypto.io">support@nomiddlemancrypto.io</a>.';
						}
					}
					else {
						if (!NMM_Hd::is_valid_xpub($mpk)) {
							$invalidCryptoSettings = true;
							$atLeastOneInvalidCrypto = true;
							$errorMessages[] = $cryptoName . ' has an invalid HD MPK. Disabling ' . $cryptoName . '.';
						}
					}
				}
			}

			// standard validation not determined by what mode is selected
			// strip out invalid data from settings
			$invalidAddressKeys = [];
			foreach ($newSettings->get_addresses($cryptoId) as $k => $address) {
				if (!NMM_Cryptocurrencies::is_valid_wallet_address($cryptoId, $address)) {
					if ($address !== '') {
						$invalidAddressKeys[] = $k;
						$errorMessages[] = $cryptoName . ' has invalid address: ' . $address;
					}
					else {
						$invalidAddressKeys[] = $k;					
                    }
                }
			}
			foreach ($invalidAddressKeys as $k) {
				if ($k > 0) {
					unset($newValues[$cryptoId . '_addresses'][$k]);
				}
				else {
					$newValues[$cryptoId . '_addresses'][$k] = '';
				}
			}

			if (NMM_Util::p_enabled()) {
				if (!NMM_Hd::is_valid_mpk($cryptoId, $newSettings->get_mpk($cryptoId))) {
					unset($newValues[$cryptoId . '_hd_mpk']);
				}
			}
			else {
				if (!NMM_Hd::is_valid_xpub($newSettings->get_mpk($cryptoId))) {
					unset($newValues[$cryptoId . '_hd_mpk']);
				}
			}

			if ($invalidCryptoSettings) {
				$newValues[$cryptoId . '_mode'] = null;
			}
		} // foreach

		$reduxInstance = ReduxFrameworkInstances::get_instance(NMM_REDUX_ID);

		$noPriceApiSelected = false;

		if (!$newSettings->price_api_selected()) {
			$noPriceApiSelected = true;
			// 4/10/2019 - This is always executed in Redux v3, commenting out so user doesnt see false error
			//$errorMessages[] = 'At least one pricing API must be selected. Falling back to CryptoCompare.';
			$newValues['selected_price_apis'] = ['0'];
		}

		$reduxInstance->set_options($newValues);

		if ($atLeastOneInvalidCrypto || $selectedCryptosChanged || $noPriceApiSelected) {
        
	        foreach ($errorMessages as $msg) {
	            NMM_add_flash_notice($msg);
	        }
	        // hard reload page
	        echo json_encode( array( 'status' => 'success', 'action' => 'reload' ) );
	        die ();
	    }

	    return $newValues;
	}
}

?>