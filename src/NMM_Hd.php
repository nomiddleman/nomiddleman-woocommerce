<?php

class NMM_Hd {

	public static function buffer_ready_addresses($cryptoId, $mpk, $amount, $hdMode) {
		$hdRepo = new NMM_Hd_Repo($cryptoId, $mpk, $hdMode);
		$readyCount = $hdRepo->count_ready();		
		
		$neededAddresses = $amount - $readyCount;
		
		for ($i = 0; $i < $neededAddresses; $i++) {
			
			try {
				self::force_new_address($cryptoId, $mpk, $hdMode);
			}
			catch ( \Exception $e ) {
				NMM_Util::log(__FILE__, __LINE__, $e->getMessage());
			}
		}
	}

	public static function check_all_pending_addresses_for_payment($cryptoId, $mpk, $requiredConfirmations, $percentToVerify, $hdMode) {
		global $woocommerce;
		$hdRepo = new NMM_Hd_Repo($cryptoId, $mpk, $hdMode);

		$pendingRecords = $hdRepo->get_pending();

		foreach ($pendingRecords as $record) {

			try {
				$blockchainTotalReceived = self::get_total_received_for_address($cryptoId, $record['address'], $requiredConfirmations);
			}
			catch ( \Exception $e ) {
				// just go to next record if the endpoint is not responding			
				continue;
			}

			$recordTotalReceived = $record['total_received'];
			$newPaymentAmount = $blockchainTotalReceived - $recordTotalReceived;

			// if we received a new payment
			// TODO: This should be 1 / 10*max digits
			if ($newPaymentAmount > 0.0000001) {

				$address = $record['address'];				

				$orderAmount = $record['order_amount'];
				NMM_Util::log(__FILE__, __LINE__, 'Address ' . $address . ' received a new payment of ' . NMM_Cryptocurrencies::get_price_string($cryptoId, $newPaymentAmount) . ' ' . $cryptoId);
				// set total in database because we received a payment
				$hdRepo->set_total_received($address, $blockchainTotalReceived);
				
				$amountToVerify = ((float) $orderAmount) * $percentToVerify;
				$paymentAmountVerified = $blockchainTotalReceived >= $amountToVerify;
				
				
				// if new total is enough to process the order
				if ($paymentAmountVerified) {

					$orderId = $record['order_id'];
					$order = new WC_Order( $orderId );

					$orderNote = sprintf(
						'Order payment of %s %s verified at %s.',
						NMM_Cryptocurrencies::get_price_string($cryptoId, $blockchainTotalReceived),
						$cryptoId,
						date('Y-m-d H:i:s', time()));			
					
					//$order->update_status('wc-processing', $orderNote);					
					$order->payment_complete();
					$order->add_order_note($orderNote);
					
					$hdRepo->set_status($address, 'complete');
				}
				// we received payment but it was not enough to meet store admin's processing requirement
				else {
					$orderId = $record['order_id'];
					$order = new WC_Order( $orderId );
					
					// handle multiple underpayments, just add a new note
					if ($record['status'] === 'underpaid') {
						$orderNote = sprintf(
							'New payment was received but is still under order total. Received payment of %s %s.<br>Remaining payment required: %s<br>Wallet Address: %s',
							NMM_Cryptocurrencies::get_price_string($cryptoId, $newPaymentAmount),
							$cryptoId,
							NMM_Cryptocurrencies::get_price_string($cryptoId, ((float) $orderAmount) - $blockchainTotalReceived),
							$address);

						add_filter('woocommerce_email_subject_customer_note', 'NMM_change_partial_email_note_subject_line', 1, 2);
	    				add_filter('woocommerce_email_heading_customer_note', 'NMM_change_partial_email_heading', 1, 2);

						$order->add_order_note($orderNote, true);
					}
					// handle first underpayment, update status to pending payment (since we use on-hold for orders with no payment yet)
					else {						
						$orderNote = sprintf(
							'Payment of %s %s received at %s. This is under the amount required to process this order.<br>Remaining payment required: %s<br>Wallet Address: %s',
							NMM_Cryptocurrencies::get_price_string($cryptoId, $blockchainTotalReceived),
							$cryptoId,
							date('m/d/Y g:i a', time() + (60 * 60 * get_option('gmt_offset'))),
							NMM_Cryptocurrencies::get_price_string($cryptoId, $amountToVerify - $blockchainTotalReceived),
							$address);
						
						add_filter('woocommerce_email_subject_customer_note', 'NMM_change_partial_email_note_subject_line', 1, 2);
	    				add_filter('woocommerce_email_heading_customer_note', 'NMM_change_partial_email_heading', 1, 2);
						
						$order->add_order_note($orderNote, true);
						$hdRepo->set_status($address, 'underpaid');
					}
				}
			}
		}
	}

	private static function get_total_received_for_address($cryptoId, $address, $requiredConfirmations) {
		if ($cryptoId === 'BTC') {
			return self::get_total_received_for_bitcoin_address($address, $requiredConfirmations);
		}
		if ($cryptoId === 'LTC') {
			return self::get_total_received_for_litecoin_address($address, $requiredConfirmations);
		}
		if ($cryptoId === 'QTUM') {
			return self::get_total_received_for_qtum_address($address);
		}
		if ($cryptoId === 'DASH') {
			return self::get_total_received_for_dash_address($address);
		}
		if ($cryptoId === 'DOGE') {
			return self::get_total_received_for_doge_address($address);
		}
		if ($cryptoId === 'XMY') {
			return self::get_total_received_for_xmy_address($address);
		}
		if ($cryptoId === 'BTX') {
			return self::get_total_received_for_bitcore_address($address, $requiredConfirmations);
		}
	}

	private static function get_total_received_for_bitcoin_address($address, $requiredConfirmations) {
		
		$primaryResult = NMM_Blockchain::get_blockchaininfo_total_received_for_btc_address($address, $requiredConfirmations);
		
		if ($primaryResult['result'] === 'success') {
			return $primaryResult['total_received'];
		}

		$secondaryResult = NMM_Blockchain::get_blockexplorer_total_received_for_btc_address($address);

		if ($secondaryResult['result'] === 'success') {			
			return $secondaryResult['total_received'];
		}

		$fallbackResult = NMM_Blockchain::get_chainso_total_received_for_btc_address($address);

		if ($fallbackResult['result'] === 'success') {
			return $fallbackResult['total_received'];
		}
		
		throw new \Exception("Unable to get BTC HD address information from external sources.");
	}

	private static function get_total_received_for_litecoin_address($address, $requiredConfirmations) {
		$primaryResult = NMM_Blockchain::get_blockcypher_total_received_for_ltc_address($address, $requiredConfirmations);

		if ($primaryResult['result'] === 'success') {
			return $primaryResult['total_received'];
		}

		$secondaryResult = NMM_Blockchain::get_chainso_total_received_for_ltc_address($address);

		if ($secondaryResult['result'] === 'success') {
			return $secondaryResult['total_received'];
		}

		throw new \Exception("Unable to get LTC HD address information from external sources.");
	}

	private static function get_total_received_for_qtum_address($address) {
		$result = NMM_Blockchain::get_qtuminfo_total_received_for_qtum_address($address);

		if ($result['result'] === 'success') {
			return $result['total_received'];
		}		

		throw new \Exception("Unable to get QTUM HD address information from external sources.");
	}

	private static function get_total_received_for_dash_address($address) {
		$result = NMM_Blockchain::get_dashblockexplorer_total_received_for_dash_address($address);

		if ($result['result'] === 'success') {
			return $result['total_received'];
		}		

		throw new \Exception("Unable to get DASH HD address information from external sources.");
	}

	private static function get_total_received_for_doge_address($address) {
		$result = NMM_Blockchain::get_chainso_total_received_for_doge_address($address);

		if ($result['result'] === 'success') {
			return $result['total_received'];
		}		

		throw new \Exception("Unable to get DOGE HD address information from external sources.");
	}

	private static function get_total_received_for_xmy_address($address) {
		$result = NMM_Blockchain::get_blockbook_total_received_for_xmy_address($address);

		if ($result['result'] === 'success') {
			return $result['total_received'];
		}		

		throw new \Exception("Unable to get XMY HD address information from external sources.");
	}
	
	private static function get_total_received_for_bitcore_address($address) {
		$result = NMM_Blockchain::get_insight_total_received_for_btx_address($address);

		if ($result['result'] === 'success') {
			return $result['total_received'];
		}		

		throw new \Exception("Unable to get XMY HD address information from external sources.");
	}

	public static function cancel_expired_addresses($cryptoId, $mpk, $orderCancellationTimeSec, $hdMode) {
		global $woocommerce;
		$hdRepo = new NMM_Hd_Repo($cryptoId, $mpk, $hdMode);

		$assignedRecords = $hdRepo->get_assigned();

		foreach ($assignedRecords as $record) {
			
			$assignedAt = $record['assigned_at'];
			$totalReceived = $record['total_received'];
			$address = $record['address'];
			$orderId = $record['order_id'];

			$assignedFor = time() - $assignedAt;
			NMM_Util::log(__FILE__, __LINE__, 'address ' . $address . ' has been assigned for ' . $assignedFor . '... cancel time: ' . $orderCancellationTimeSec);
			if ($assignedFor > $orderCancellationTimeSec && $totalReceived == 0) {
				// since order was cancelled we can re-use the address, set status to ready
				$hdRepo->set_status($address, 'ready');
				$hdRepo->set_order_amount($address, 0.0);

				$order = new WC_Order($orderId);
				$orderNote = sprintf(
					'Your ' . $cryptoId . ' order was <strong>cancelled</strong> because you were unable to pay for %s hour(s). Please do not send any funds to the payment address.',
					round($orderCancellationTimeSec/3600, 1),
					$address);

				add_filter('woocommerce_email_subject_customer_note', 'NMM_change_cancelled_email_note_subject_line', 1, 2);
	    		add_filter('woocommerce_email_heading_customer_note', 'NMM_change_cancelled_email_heading', 1, 2);
				
				$order->update_status('wc-cancelled');				
				$order->add_order_note($orderNote, true);
				
				NMM_Util::log(__FILE__, __LINE__, 'Cancelled order: ' . $orderId . ' which was using address: ' . $address . 'due to non-payment.');
			}
		}
	}

	private static function is_dirty_address($cryptoId, $address) {
		if ($cryptoId === 'BTC') {
			return self::is_dirty_btc_address($address);
		}
		if ($cryptoId === 'LTC') {
			return self::is_dirty_ltc_address($address);
		}
		if ($cryptoId === 'QTUM') {
			return self::is_dirty_qtum_address($address);	
		}
		if ($cryptoId === 'DASH') {
			return self::is_dirty_dash_address($address);	
		}
		if ($cryptoId === 'DOGE') {
			return self::is_dirty_doge_address($address);	
		}
		if ($cryptoId === 'XMY') {
			return self::is_dirty_xmy_address($address);
		}
		if ($cryptoId === 'BTX') {
			return self::is_dirty_btx_address($address);
		}
	}

	private static function is_dirty_btc_address($address) {
		$primaryResult = NMM_Blockchain::get_blockchaininfo_total_received_for_btc_address($address, 0);

		if ($primaryResult['result'] === 'success') {
			// if we get a non zero balance from first source then address is dirty
			if ($primaryResult['total_received'] >= 0.00000001) {				
				return true;
			}
			else {
				$secondaryResult = NMM_Blockchain::get_blockexplorer_total_received_for_btc_address($address);

				// we have a primary resource saying address is clean and backup source failed, so return clean
				if ($secondaryResult['result'] === 'error') {					
					return false;
				}
				// backup source gave us data
				else {
					// primary source is clean but if we see a balance we return dirty
					if ($secondaryResult['total_received'] >= 0.00000001) {
						return true;
					}
					// both sources return clean
					else {
						return false;
					}
				}
			}
		}
		else {
			$secondaryResult = NMM_Blockchain::get_blockexplorer_total_received_for_btc_address($address);

			if ($secondaryResult['result'] === 'success') {
				return $secondaryResult['total_received'] >= 0.00000001;
			}
		}

		$fallbackResult = NMM_Blockchain::get_chainso_total_received_for_btc_address($address);
		if ($fallbackResult['result'] === 'success') {
				return $fallbackResult['total_received'] >= 0.00000001;
			}
		throw new \Exception("Unable to get BTC address total amount received to verify is address is unused.");
	}

	private static function is_dirty_ltc_address($address) {
		$primaryResult = NMM_Blockchain::get_chainso_total_received_for_ltc_address($address);		

		if ($primaryResult['result'] === 'success') {
			// if we get a non zero balance from first source then address is dirty
			if ($primaryResult['total_received'] >= 0.00000001) {				
				return true;
			}
			else {
				$secondaryResult = NMM_Blockchain::get_blockcypher_total_received_for_ltc_address($address, 0);

				// we have a primary resource saying address is clean and backup source failed, so return clean
				if ($secondaryResult['result'] === 'error') {					
					return false;
				}
				// backup source gave us data
				else {
					// primary source is clean but if we see a balance we return dirty
					if ($secondaryResult['total_received'] >= 0.00000001) {
						return true;
					}
					// both sources return clean
					else {
						return false;
					}
				}
			}
		}
		else {
			$secondaryResult = NMM_Blockchain::get_blockcypher_total_received_for_ltc_address($address, 0);

			if ($secondaryResult['result'] === 'success') {
				return $secondaryResult['total_received'] >= 0.00000001;
			}
		}
		
		throw new \Exception("Unable to get LTC address total amount received to verify is address is unused.");
	}

	private static function is_dirty_qtum_address($address) {
		return self::get_total_received_for_qtum_address($address) >= 0.00000001;
	}
	
	private static function is_dirty_dash_address($address) {
		return self::get_total_received_for_dash_address($address) >= 0.00000001;
	}

	private static function is_dirty_doge_address($address) {
		return self::get_total_received_for_doge_address($address) >= 0.00000001;
	}

	private static function is_dirty_xmy_address($address) {
		return self::get_total_received_for_xmy_address($address) >= 0.00000001;
	}
	
	private static function is_dirty_btx_address($address) {
		return self::get_total_received_for_bitcore_address($address) >= 0.00000001;
	}
	
	public static function force_new_address($cryptoId, $mpk, $hdMode) {
		
		$hdRepo = new NMM_Hd_Repo($cryptoId, $mpk, $hdMode);

		$startIndex = $hdRepo->get_next_index();	

		$address = self::create_hd_address($cryptoId, $mpk, $startIndex, $hdMode);

		try {
			while (self::is_dirty_address($cryptoId, $address)) {
				
				$hdRepo->insert($address, $startIndex, 'dirty');
				$startIndex = $startIndex + 1;
				$address = self::create_hd_address($cryptoId, $mpk, $startIndex, $hdMode);
				set_time_limit(30);
			}
		}
		catch ( \Exception $e ) {
			NMM_Util::log(__FILE__, __LINE__, 'Could not create new addresses: ' . $e->getMessage());
			throw new \Exception($e);
		}

		$hdRepo->insert($address, $startIndex, 'ready');
	}

	public static function create_hd_address($cryptoId, $mpk, $index, $hdMode) {
		
		try {
			if (!NMM_Util::p_enabled()) {
				if (self::is_valid_xpub($mpk)) {
					return HdHelper::mpk_to_bc_address($cryptoId, $mpk, $index, 2, false);
				}
			}
			else {
				if (self::is_valid_mpk($cryptoId, $mpk)) {
					return apply_filters('nmm_get_hd_address', $cryptoId, $mpk, $index, $hdMode);
				}
			}
		}
		catch (\Exception $e) {
			throw new \Exception('Invalid MPK for ' . $cryptoId . '. ' . $e->getTraceAsString());
		}
	}

	public static function is_valid_xpub($mpk) {
		$mpkStart = substr($mpk, 0, 5);
		$validMpk = strlen($mpk) == 111 && $mpkStart === 'xpub6';
		return $validMpk;
	}

	public static function is_valid_ypub($mpk) {
		$mpkStart = substr($mpk, 0, 5);
		$validMpk = strlen($mpk) == 111 && $mpkStart === 'ypub6';
		return $validMpk;
	}

	public static function is_valid_zpub($mpk) {
		$mpkStart = substr($mpk, 0, 5);
		$validMpk = strlen($mpk) == 111 && $mpkStart === 'zpub6';
		return $validMpk;
	}

	public static function is_valid_mpk($cryptoId, $mpk) {
		if ($cryptoId == 'BTC') {
			return self::is_valid_xpub($mpk) || self::is_valid_ypub($mpk) || self::is_valid_zpub($mpk);
		}
		if ($cryptoId === 'LTC') {
			return self::is_valid_xpub($mpk) || self::is_valid_ypub($mpk) || self::is_valid_zpub($mpk);
		}
		if ($cryptoId === 'QTUM') {
			return self::is_valid_xpub($mpk) || self::is_valid_ypub($mpk);
		}
		if ($cryptoId === 'DASH') {
			return self::is_valid_xpub($mpk);
		}
		if ($cryptoId === 'DOGE') {
			return self::is_valid_xpub($mpk);
		}
		if ($cryptoId === 'XMY') {
			return self::is_valid_xpub($mpk);	
		}	
		if ($cryptoId === 'BTX') {
			return self::is_valid_xpub($mpk);	
		}
	}
}

?>