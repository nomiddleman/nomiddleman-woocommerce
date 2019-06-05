<?php

class NMM_Lightning {

	public static function get_invoice($invoice) {
		$nmmSettings = new NMM_Settings(get_option(NMM_REDUX_ID));
		$mac = $nmmSettings->get_mac();
		$certPath = $nmmSettings->get_cert_path();
		$endpoint = $nmmSettings->get_endpoint();
		$headers = [
			'Grpc-Metadata-macaroon: ' . $mac,
			'Content-type: application/json',
		];


		$args = [
			'headers' => $headers,
			'sslverify' => true,
			'sslcertificates' => $certPath,
			'body' => json_encode($invoice),
		];

		$response = wp_remote_post($endpoint . '/v1/invoices', $args);

		error_log('ln response: ' . $response);

		$body = json_decode($response['body']);
	}



}


?>