<?php

function NMM_do_cron_job() {
	global $wpdb;	
	
	$nmmSettings = new NMM_Settings(get_option(NMM_REDUX_ID));	
	// Number of clean addresses in the database at all times for faster thank you page load times
	$hdBufferAddressCount = 4;
	
	// Only look at transactions in the past two hours
	$autoPaymentTransactionLifetimeSec = 3 * 60 * 60;

	$startTime = time();	
	NMM_Util::log(__FILE__, __LINE__, 'Starting Cron Job...');
	
	NMM_Carousel_Repo::init();
	foreach (NMM_Cryptocurrencies::get() as $crypto) {
		$cryptoId = $crypto->get_id();
		
		if ($nmmSettings->hd_enabled($cryptoId)) {
			NMM_Util::log(__FILE__, __LINE__, 'Starting Hd stuff for: ' . $cryptoId);
			$mpk = $nmmSettings->get_mpk($cryptoId);
			$hdMode = $nmmSettings->get_hd_mode($cryptoId);
			$hdPercentToVerify = $nmmSettings->get_hd_processing_percent($cryptoId);
			$hdRequiredConfirmations = $nmmSettings->get_hd_required_confirmations($cryptoId);
			$hdOrderCancellationTimeHr = $nmmSettings->get_hd_cancellation_time($cryptoId);
			$hdOrderCancellationTimeSec = round($hdOrderCancellationTimeHr * 60 * 60, 0);
						
			NMM_Hd::check_all_pending_addresses_for_payment($cryptoId, $mpk, $hdRequiredConfirmations, $hdPercentToVerify, $hdMode);

			NMM_Hd::buffer_ready_addresses($cryptoId, $mpk, $hdBufferAddressCount, $hdMode);
			NMM_Hd::cancel_expired_addresses($cryptoId, $mpk, $hdOrderCancellationTimeSec, $hdMode);
		}		
	}

	NMM_Payment::check_all_addresses_for_matching_payment($autoPaymentTransactionLifetimeSec);	
	NMM_Payment::cancel_expired_payments();

	NMM_Util::log(__FILE__, __LINE__, 'total time for cron job: ' . NMM_get_time_passed($startTime));
}

function NMM_get_time_passed($startTime) {
	return time() - $startTime;
}

?>