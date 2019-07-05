<?php

function NMM_change_cancelled_email_note_subject_line($subject, $order) {
	$subject = 'Order ' . $order->get_id() . ' has been cancelled due to non-payment';

	return $subject;

}

function NMM_change_cancelled_email_heading($heading, $order) {
	$heading = "Your order has been cancelled. Do not send any cryptocurrency to the payment address.";

	return $heading;
}

function NMM_change_partial_email_note_subject_line($subject, $order) {
	$subject = 'Partial payment received for Order ' . $order->get_id();

	return $subject;
}

function NMM_change_partial_email_heading($heading, $order) {
	$heading = 'Partial payment received for Order ' . $order->get_id();

	return $heading;
}

function NMM_update_database_when_admin_changes_order_status( $orderId, $oldOrderStatus, $newOrderStatus ) {	
  
	$paymentAmount = 0.0;
	
	$paymentAmount = get_post_meta($orderId, 'crypto_amount', true);

	// this order was not made by us
	if ($paymentAmount === 0.0 || !$paymentAmount) {
    error_log('ending early');
		return;
  }
	

	$paymentRepo = new NMM_Payment_Repo();

	// If admin updates from needs-payment to has-payment, stop looking for matching transactions
	if ($oldOrderStatus === 'pending' && $newOrderStatus === 'processing') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'paid');
	}
	if ($oldOrderStatus === 'pending' && $newOrderStatus === 'completed') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'paid');
	}
	if ($oldOrderStatus === 'on-hold' && $newOrderStatus === 'processing') {
    error_log('updating order '. $orderId . ' to paid');
		$paymentRepo->set_status($orderId, $paymentAmount, 'paid');
	}
	if ($oldOrderStatus === 'on-hold' && $newOrderStatus === 'completed') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'paid');
	}

	// If admin updates from has-payment to needs-payment, start looking for matching transactions
	if ($oldOrderStatus === 'processing' && $newOrderStatus === 'pending') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
	}
	if ($oldOrderStatus === 'processing' && $newOrderStatus === 'on-hold') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
	}
	if ($oldOrderStatus === 'completed' && $newOrderStatus === 'pending') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
	}
	if ($oldOrderStatus === 'completed' && $newOrderStatus === 'on-hold') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
	}

	// If admin updates from needs-payment to cancelled, stop looking for matching transactions
	if ($oldOrderStatus === 'pending' && $newOrderStatus === 'cancelled') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
	}
	if ($oldOrderStatus === 'pending' && $newOrderStatus === 'failed') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
	}
	if ($oldOrderStatus === 'on-hold' && $newOrderStatus === 'cancelled') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
	}
	if ($oldOrderStatus === 'on-hold' && $newOrderStatus === 'failed') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
	}

	// If admin updates from cancelled to needs-payment, start looking for matching transactions
	if ($oldOrderStatus === 'cancelled' && $newOrderStatus === 'on-hold') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
		$paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
	}
	if ($oldOrderStatus === 'cancelled' && $newOrderStatus === 'pending') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
		$paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
	}
	if ($oldOrderStatus === 'failed' && $newOrderStatus === 'on-hold') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
		$paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
	}
	if ($oldOrderStatus === 'failed' && $newOrderStatus === 'pending') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
		$paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
	}

  // WC PREFIX
  // If admin updates from needs-payment to has-payment, stop looking for matching transactions
  if ($oldOrderStatus === 'wc-pending' && $newOrderStatus === 'wc-processing') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'paid');
  }
  if ($oldOrderStatus === 'wc-pending' && $newOrderStatus === 'wc-completed') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'paid');
  }
  if ($oldOrderStatus === 'wc-on-hold' && $newOrderStatus === 'wc-processing') {
    error_log('updating order '. $orderId . ' to paid');
    $paymentRepo->set_status($orderId, $paymentAmount, 'paid');
  }
  if ($oldOrderStatus === 'wc-on-hold' && $newOrderStatus === 'wc-completed') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'paid');
  }
  

  // If admin updates from has-payment to needs-payment, start looking for matching transactions
  if ($oldOrderStatus === 'wc-processing' && $newOrderStatus === 'wc-pending') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
  }
  if ($oldOrderStatus === 'wc-processing' && $newOrderStatus === 'wc-on-hold') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
  }
  if ($oldOrderStatus === 'wc-completed' && $newOrderStatus === 'wc-pending') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
  }
  if ($oldOrderStatus === 'wc-completed' && $newOrderStatus === 'wc-on-hold') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
  }

  // If admin updates from needs-payment to cancelled, stop looking for matching transactions
  if ($oldOrderStatus === 'wc-pending' && $newOrderStatus === 'wc-cancelled') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
  }
  if ($oldOrderStatus === 'wc-pending' && $newOrderStatus === 'wc-failed') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
  }
  if ($oldOrderStatus === 'wc-on-hold' && $newOrderStatus === 'wc-cancelled') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
  }
  if ($oldOrderStatus === 'wc-on-hold' && $newOrderStatus === 'wc-failed') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
  }

  // If admin updates from cancelled to needs-payment, start looking for matching transactions
  if ($oldOrderStatus === 'wc-cancelled' && $newOrderStatus === 'wc-on-hold') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
    $paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
  }
  if ($oldOrderStatus === 'wc-cancelled' && $newOrderStatus === 'wc-pending') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
    $paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
  }
  if ($oldOrderStatus === 'wc-failed' && $newOrderStatus === 'wc-on-hold') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
    $paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
  }
  if ($oldOrderStatus === 'wc-failed' && $newOrderStatus === 'wc-pending') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
    $paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
  }
}

function NMM_add_flash_notice($notice = "", $type = "error", $dismissible = true) {
    // Here we return the notices saved on our option, if there are not notices, then an empty array is returned
    $notices = get_option( "my_flash_notices", array() );
 
    $dismissible_text = ( $dismissible ) ? "is-dismissible" : "";
 
    // We add our new notice.
    array_push( $notices, array( 
            "notice" => $notice, 
            "type" => $type, 
            "dismissible" => $dismissible_text
        ) );
 
    // Then we update the option with our notices array
    update_option("my_flash_notices", $notices );
}
 
/**
 * Function executed when the 'admin_notices' action is called, here we check if there are notices on
 * our database and display them, after that, we remove the option to prevent notices being displayed forever.
 * @return void
 */ 
function NMM_display_flash_notices() {
    $notices = get_option( "my_flash_notices", array() );
     
    // Iterate through our notices to be displayed and print them.
    foreach ( $notices as $notice ) {
        printf('<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
            $notice['type'],
            $notice['dismissible'],
            $notice['notice']
        );
    }
 
    // Now we reset our options to prevent notices being displayed forever.
    if( ! empty( $notices ) ) {
        delete_option( "my_flash_notices", array() );
    }
}

function NMM_load_redux_css($stuff) {
    $cssPath = NMM_PLUGIN_DIR . '/assets/css/nmm-redux-settings.css';    
    wp_enqueue_style('nmm-styles', $cssPath, array(), NMM_VERSION);
}

function NMM_load_js($stuff) {

	if (!is_array($_GET)) {
		return;
	}

	if (!array_key_exists('page', $_GET)) {
		return;
	}
		
	$page = $_GET['page'];	
	
	if ($page === 'nmmpro_options') {
		$jsPath = NMM_PLUGIN_DIR . '/assets/js/nmm-redux-mpk.js';

		if (NMM_Util::p_enabled()) {
			wp_enqueue_script('nmm-scripts', $jsPath, array( 'jquery', 'nmmp-admin-scripts' ), NMM_VERSION);	            
        }
        else {        	
        	wp_enqueue_script('nmm-scripts', $jsPath, array( 'jquery' ), NMM_VERSION);
        }		
	}
}

function NMM_first_mpk_address_ajax() {
	
		if (!isset($_POST) || !is_array($_POST) || !array_key_exists('mpk', $_POST) || !array_key_exists('cryptoId', $_POST)) {
			return;
		}

		$mpk = sanitize_text_field($_POST['mpk']);
		$cryptoId = sanitize_text_field($_POST['cryptoId']);
		$hdMode = sanitize_text_field($_POST['hdMode']);		
		
		if (!NMM_Hd::is_valid_mpk($cryptoId, $mpk)) {
			return;
		}
		
		if (!NMM_Util::p_enabled() && (NMM_Hd::is_valid_ypub($mpk) || NMM_Hd::is_valid_zpub($mpk))) {
			$message = 'You have entered a valid Segwit MPK.';
			$message2 = '<a href="https://nomiddlemancrypto.io/extensions/segwit" target="_blank">Segwit MPKs are coming soon!</a>';

			echo json_encode([$message, $message2, '']);
			wp_die();
		}
		else {
			$firstAddress = NMM_Hd::create_hd_address($cryptoId, $mpk, 0, $hdMode);
			$secondAddress = NMM_Hd::create_hd_address($cryptoId, $mpk, 1, $hdMode);
			$thirdAddress = NMM_Hd::create_hd_address($cryptoId, $mpk, 2, $hdMode);

			echo json_encode([$firstAddress, $secondAddress, $thirdAddress]);

			wp_die();
		}
}

function NMM_filter_gateways($gateways){	
    global $woocommerce;
    
    $nmmSettings = new NMM_Settings(get_option(NMM_REDUX_ID));

    foreach (NMM_Cryptocurrencies::get() as $crypto) {
        if ($nmmSettings->crypto_selected_and_valid($crypto->get_id())) {
        	$gateways[] = 'NMM_Gateway';
            return $gateways;
        }
    }
    
    if (is_checkout()) {
	    unset($gateways['NMM_Gateway']);
	}
	else {
		$gateways[] = 'NMM_Gateway';
	}

    return $gateways;
}
?>