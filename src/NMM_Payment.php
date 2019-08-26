<?php

class NMM_Payment {

	public static function check_all_addresses_for_matching_payment($transactionLifetime) {		
		$paymentRepo = new NMM_Payment_Repo();

		// get a unique list of unpaid "payments" to crypto addresses
		$addressesToCheck = $paymentRepo->get_distinct_unpaid_addresses();

		$cryptos = NMM_Cryptocurrencies::get();

		foreach ($addressesToCheck as $record) {
			$address = $record['address'];

			$cryptoId = $record['cryptocurrency'];
			$crypto = $cryptos[$cryptoId];

			self::check_address_transactions_for_matching_payments($crypto, $address, $transactionLifetime);
		}
	}

	private static function check_address_transactions_for_matching_payments($crypto, $address, $transactionLifetime) {
		global $woocommerce;
		$paymentRepo = new NMM_Payment_Repo();
		$nmmSettings = new NMM_Settings(get_option(NMM_REDUX_ID));
		
		$cryptoId = $crypto->get_id();

		NMM_Util::log(__FILE__, __LINE__, '===========================================================================');
		NMM_Util::log(__FILE__, __LINE__, 'Starting payment verification for: ' . $cryptoId . ' - ' . $address);
		
		try {
			$transactions = self::get_address_transactions($cryptoId, $address);
		}
		catch (\Exception $e) {
			NMM_Util::log(__FILE__, __LINE__, 'Unable to get transactions for ' . $cryptoId);
			return;
		}
		
		NMM_Util::log(__FILE__, __LINE__, 'Transcations found for ' . $cryptoId . ' - ' . $address . ': ' . print_r($transactions, true));	


		foreach ($transactions as $transaction) {
			$txHash = $transaction->get_hash();
			$transactionAmount = $transaction->get_amount();

			$requiredConfirmations = $nmmSettings->get_autopay_required_confirmations($cryptoId);
			$txConfirmations = $transaction->get_confirmations();

			NMM_Util::log(__FILE__, __LINE__, '---confirmations: ' . $txConfirmations . ' Required: ' . $requiredConfirmations);
			if ($txConfirmations < $requiredConfirmations) {
				continue;
			}

			$txTimeStamp = $transaction->get_time_stamp();
			$timeSinceTx = time() - $txTimeStamp;

			NMM_Util::log(__FILE__, __LINE__, '---time since transaction: ' . $timeSinceTx . ' TX Lifetime: ' . $transactionLifetime);
			if ($timeSinceTx > $transactionLifetime) {
				continue;
			}

			if ($nmmSettings->tx_already_consumed($cryptoId, $address, $txHash)) {
				NMM_Util::log(__FILE__, __LINE__, '---Collision occurred for old transaction, skipping....');
				continue;
			}

			$paymentRecords = $paymentRepo->get_unpaid_for_address($cryptoId, $address);

			$matchingPaymentRecords = [];

			foreach ($paymentRecords as $record) {
				$paymentAmount = $record['order_amount'];
				$paymentAmountSmallestUnit = $paymentAmount * (10**$crypto->get_round_precision());				
				
				$autoPaymentPercent = apply_filters('nmm_autopay_percent', $nmmSettings->get_autopay_processing_percent($cryptoId), $paymentAmount, $cryptoId, $address);

				$percentDifference = abs($transactionAmount - $paymentAmountSmallestUnit) / $transactionAmount;

				if ($percentDifference <= (1 - $autoPaymentPercent)) {
					$matchingPaymentRecords[] = $record;
				}

				NMM_Util::log(__FILE__, __LINE__, '---CryptoId, paymentAmount, paymentAmountSmallestUnit, transactionAmount, percentDifference:' . $cryptoId . ',' . $paymentAmount .',' . $paymentAmountSmallestUnit . ',' .  $transactionAmount . ',' .  $percentDifference);
			}

			// Transaction does not match any order payment
			if (count($matchingPaymentRecords) == 0) {
				// Do nothing
			}
			if (count($matchingPaymentRecords) > 1) {
				// We have a collision, send admin note to each order
				foreach ($matchingPaymentRecords as $matchingRecord) {
					$orderId = $matchingRecord['order_id'];
					$order = new WC_Order($orderId);
					$order->add_order_note('This order has a matching ' . $cryptoId . ' transaction but we cannot verify it due to other orders with similar payment totals. Please reconcile manually. Transaction Hash: ' . $txHash);
				}
				
				
				$nmmSettings->add_consumed_tx($cryptoId, $address, $txHash);
			}
			if (count($matchingPaymentRecords) == 1) {
				// We have validated a transaction: update database to paid, update order to processing, add transaction to consumed transactions
				$orderId = $matchingPaymentRecords[0]['order_id'];
				$orderAmount = $matchingPaymentRecords[0]['order_amount'];				

				$paymentRepo->set_status($orderId, $orderAmount, 'paid');
				$paymentRepo->set_hash($orderId, $orderAmount, $txHash);

				$order = new WC_Order($orderId);
				$orderNote = sprintf(
						'Order payment of %s %s verified at %s. Transaction Hash: %s',
						NMM_Cryptocurrencies::get_price_string($crypto->get_id(), $transactionAmount / (10**$crypto->get_round_precision())),
						$cryptoId,
						date('Y-m-d H:i:s', time()),
						apply_filters('nmm_order_txhash', $txHash, $cryptoId));
				
				$order->payment_complete();
				$order->add_order_note($orderNote);				

				update_post_meta($orderId, 'transaction_hash', $txHash);

				$nmmSettings->add_consumed_tx($cryptoId, $address, $txHash);
			}		
		}		
	}

	private static function get_address_transactions($cryptoId, $address) {
		if ($cryptoId === 'ETH') {
			$result = NMM_Blockchain::get_eth_address_transactions($address);
		}
		if ($cryptoId === 'BCH') {
			$result = NMM_Blockchain::get_bch_address_transactions($address);
		}
		if ($cryptoId === 'DOGE') {
			$result = NMM_Blockchain::get_doge_address_transactions($address);
		}
		if ($cryptoId === 'ZEC') {
			$result = NMM_Blockchain::get_zec_address_transactions($address);
		}
		if ($cryptoId === 'DASH') {
			$result = NMM_Blockchain::get_dash_address_transactions($address);
		}
		if ($cryptoId === 'XRP') {
			$result = NMM_Blockchain::get_xrp_address_transactions($address);
		}
		if ($cryptoId === 'ETC') {
			$result = NMM_Blockchain::get_etc_address_transactions($address);
		}
		if ($cryptoId === 'XLM') {
			$result = NMM_Blockchain::get_xlm_address_transactions($address);
		}
		if ($cryptoId === 'BSV') {
			$result = NMM_Blockchain::get_bsv_address_transactions($address);
		}
		if ($cryptoId === 'EOS') {
			$result = NMM_Blockchain::get_eos_address_transactions($address);
		}
		if ($cryptoId === 'TRX') {
			$result = NMM_Blockchain::get_trx_address_transactions($address);
		}
		if ($cryptoId === 'ONION') {
			$result = NMM_Blockchain::get_onion_address_transactions($address);
		}
		if ($cryptoId === 'BLK') {
			$result = NMM_Blockchain::get_blk_address_transactions($address);
		}
		if ($cryptoId === 'ADA') {
			$result = NMM_Blockchain::get_ada_address_transactions($address);	
		}
		if ($cryptoId === 'XTZ') {
			$result = NMM_Blockchain::get_xtz_address_transactions($address);	
		}
		if ($cryptoId === 'REP') {
			$result = NMM_Blockchain::get_erc20_address_transactions('REP', $address);	
		}
		if ($cryptoId === 'MLN') {
			$result = NMM_Blockchain::get_erc20_address_transactions('MLN', $address);	
		}
		if ($cryptoId === 'GNO') {
			$result = NMM_Blockchain::get_erc20_address_transactions('GNO', $address);	
		}
		if ($cryptoId === 'LTC') {
			$result = NMM_Blockchain::get_ltc_address_transactions($address);
		}
		if ($cryptoId === 'BTC') {
			$result = NMM_Blockchain::get_btc_address_transactions($address);	
		}
		if ($cryptoId === 'BAT') {
			$result = NMM_Blockchain::get_erc20_address_transactions('BAT', $address);	
		}
		if ($cryptoId === 'BNB') {
			$result = NMM_Blockchain::get_erc20_address_transactions('BNB', $address);	
		}
		if ($cryptoId === 'HOT') {
			$result = NMM_Blockchain::get_erc20_address_transactions('HOT', $address);	
		}
		if ($cryptoId === 'LINK') {
			$result = NMM_Blockchain::get_erc20_address_transactions('LINK', $address);	
		}
		if ($cryptoId === 'OMG') {
			$result = NMM_Blockchain::get_erc20_address_transactions('OMG', $address);	
		}
		if ($cryptoId === 'ZRX') {
			$result = NMM_Blockchain::get_erc20_address_transactions('ZRX', $address);	
		}
		if ($cryptoId === 'GUSD') {
			$result = NMM_Blockchain::get_erc20_address_transactions('GUSD', $address);	
		}
		if ($cryptoId === 'WAVES') {
			$result = NMM_Blockchain::get_waves_address_transactions($address);	
		}
		if ($cryptoId === 'DCR') {
			$result = NMM_Blockchain::get_dcr_address_transactions($address);	
		}
		if ($cryptoId === 'LSK') {
			$result = NMM_Blockchain::get_lsk_address_transactions($address);	
		}
		if ($cryptoId === 'XEM') {
			$result = NMM_Blockchain::get_xem_address_transactions($address);	
		}
		if ($cryptoId === 'XMY') {
			$result = NMM_Blockchain::get_xmy_address_transactions($address);	
		}
		if ($cryptoId === 'BTX') {
			$result = NMM_Blockchain::get_btx_address_transactions($address);	
		}
		if ($cryptoId === 'GRS') {
			$result = NMM_Blockchain::get_grs_address_transactions($address);	
		}
        if ($cryptoId === 'DGB') {
            $result = NMM_Blockchain::get_dgb_address_transactions($address);
        }
        if ($cryptoId === 'USDC') {
			$result = NMM_Blockchain::get_erc20_address_transactions('USDC', $address);
		}
		
		if ($result['result'] === 'error') {			
			NMM_Util::log(__FILE__, __LINE__, 'BAD API CALL');
			throw new \Exception('Could not reach external service to do auto payment processing.');
		}		

		return $result['transactions'];
	}

	public static function cancel_expired_payments() {
		global $woocommerce;
		$nmmSettings = new NMM_Settings(get_option(NMM_REDUX_ID));

		$paymentRepo = new NMM_Payment_Repo();
		$unpaidPayments = $paymentRepo->get_unpaid();

		foreach ($unpaidPayments as $paymentRecord) {
			$orderTime = $paymentRecord['ordered_at'];
			$cryptoId = $paymentRecord['cryptocurrency'];			

			$paymentCancellationTimeHr = $nmmSettings->get_autopay_cancellation_time($cryptoId);
			$paymentCancellationTimeSec = $paymentCancellationTimeHr * 60 * 60;
			$timeSinceOrder = time() - $orderTime;
			NMM_Util::log(__FILE__, __LINE__, 'cryptoID: ' . $cryptoId . ' payment cancellation time sec: ' . $paymentCancellationTimeSec . ' time since order: ' . $timeSinceOrder);

			if ($timeSinceOrder > $paymentCancellationTimeSec) {
				$orderId = $paymentRecord['order_id'];
				$orderAmount = $paymentRecord['order_amount'];
				$address = $paymentRecord['address'];
				
				$paymentRepo->set_status($orderId, $orderAmount, 'cancelled');

				$order = new WC_Order($orderId);

				$orderNote = sprintf(
					'Your ' . $cryptoId . ' order was <strong>cancelled</strong> because you were unable to pay for %s hour(s). Please do not send any funds to the payment address.',
					round($paymentCancellationTimeSec/3600, 1),
					$address);

				add_filter('woocommerce_email_subject_customer_note', 'NMM_change_cancelled_email_note_subject_line', 1, 2);
	    		add_filter('woocommerce_email_heading_customer_note', 'NMM_change_cancelled_email_heading', 1, 2);   
	    		
				$order->update_status('wc-cancelled');
				$order->add_order_note($orderNote, true);

				NMM_Util::log(__FILE__, __LINE__, 'Cancelled ' . $cryptoId . ' payment: ' . $orderId . ' which was using address: ' . $address . 'due to non-payment.');
			}
		}
	}
}

?>