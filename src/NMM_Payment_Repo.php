<?php

class NMM_Payment_Repo {
	private $tableName;	

	public function __construct() {
		global $wpdb;		

		$this->tableName = $wpdb->prefix . NMM_PAYMENT_TABLE;
	}

	public function insert($address, $cryptocurrency, $orderId, $paymentAmount, $status, $hdAddress = '0') {
		NMM_Util::log(__FILE__, __LINE__, 'inserting ' . $address . ' into db as ' . $status . ' with order amount of: ' . $paymentAmount);
		global $wpdb;
		$currentTime = time();
		$query = "INSERT INTO `$this->tableName`
					(`address`,  `cryptocurrency`,  `order_id`, `order_amount`, `status`, `ordered_at`, `hd_address`) VALUES
					('$address', '$cryptocurrency', '$orderId', '$paymentAmount', '$status', '$currentTime', '$hdAddress')";

		$wpdb->query($query);
	}

	public function get_unpaid() {
		global $wpdb;

		$query = "SELECT `address`,
						 `cryptocurrency`,
						 `order_id`,
						 `order_amount`,
						 `status`,
						 `ordered_at`
				  FROM `$this->tableName`
				  WHERE `status` = 'unpaid'";

		$results = $wpdb->get_results($query, ARRAY_A);

		return $results;
	}

	public function get_distinct_unpaid_addresses() {
		global $wpdb;

		$query = "SELECT DISTINCT `address`, `cryptocurrency` FROM `$this->tableName` WHERE `status` = 'unpaid'";
		
		$results = $wpdb->get_results($query, ARRAY_A);

		return $results;
	}

	public function get_unpaid_for_address($cryptoId, $address) {
		global $wpdb;

		$query = "SELECT `cryptocurrency`,
						 `order_id`,
						 `order_amount`,
						 `status`,
						 `ordered_at`
				  FROM `$this->tableName`
				  WHERE `status` = 'unpaid'
				  AND `address` = '$address'
				  AND `cryptocurrency` = '$cryptoId'";

		$results = $wpdb->get_results($query, ARRAY_A);

		return $results;
	}

	public function set_status($orderId, $orderAmount, $status) {
		global $wpdb;
		NMM_Util::log(__FILE__, __LINE__, 'updating ' . $orderId . ' to ' . $status);		
		
		$query = "UPDATE `$this->tableName`
				  SET `status` = '$status'
				  WHERE `order_amount` = '$orderAmount'
				  AND `order_id` = '$orderId'";				  
		
		$wpdb->query($query);
	}

	public function set_hash($orderId, $orderAmount, $hash) {
		global $wpdb;
		
		
		$query = "UPDATE `$this->tableName`
				  SET `tx_hash` = '$hash'
				  WHERE `order_amount` = '$orderAmount'
				  AND `order_id` = '$orderId'";				  
		
		$wpdb->query($query);
	}

	public function set_ordered_at($orderId, $orderAmount, $orderedAt) {
		global $wpdb;
		
		
		$query = "UPDATE `$this->tableName`
				  SET `ordered_at` = '$orderedAt'
				  WHERE `order_amount` = '$orderAmount'
				  AND `order_id` = '$orderId'";				  
		
		$wpdb->query($query);
	}
}

?>