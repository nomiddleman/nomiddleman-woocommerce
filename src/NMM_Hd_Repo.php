<?php

// Repository for hd mpk storage in WP Database
class NMM_Hd_Repo {
	
	private $mpk;
	private $tableName;
	private $cryptoId;
	private $hdMode;

	public function __construct($cryptoId, $mpk, $hdMode) {
		global $wpdb;
		$this->mpk = $mpk;
		$this->cryptoId = $cryptoId;
		$this->hdMode = $hdMode;
		$this->tableName = $wpdb->prefix . NMM_HD_TABLE;
	}

	public function insert($address, $mpk_index, $status) {
		NMM_Util::log(__FILE__, __LINE__, 'inserting ' . $address . ' into db as ' . $status);
		global $wpdb;
		$currentTime = time();
		
		$query = "INSERT INTO `$this->tableName`
					(`address`, `cryptocurrency`, `mpk`, `mpk_index`, `status`, `hd_mode`) VALUES
					('$address', '$this->cryptoId', '$this->mpk', '$mpk_index', '$status', '$this->hdMode')";

		$wpdb->query($query);		
	}

	public function count_ready() {
		//statuses
		//========
		//complete - used by us, never to be used again
		//ready - ready to be used
		//error - what happens if bad data is in the database (NOT USED)
		//other - when we a non-hd address (NOT USED)
		//assigned - order is assigned to this address
		//dirty - not used by us, but has been used before		
		//underpaid - this was assigned by us but has not hit verified amount

		global $wpdb;		
		
		$query = "SELECT COUNT(*) FROM `$this->tableName`
				  WHERE `status` = 'ready'
				  AND `mpk` = '$this->mpk'
				  AND `cryptocurrency` = '$this->cryptoId'
				  AND `hd_mode` = '$this->hdMode'";
		$count = $wpdb->get_var($query);
		
		return $count;
	}

	// Returns the largest index of an address that has received payment, to establish the start of the gap
	public function get_next_index() {
		global $wpdb;
		
		$query = "SELECT MAX(`mpk_index`) FROM `$this->tableName` 
				  WHERE `mpk` = '$this->mpk'
				  AND `cryptocurrency` = '$this->cryptoId'
				  AND `hd_mode` = '$this->hdMode'";
		$largest = $wpdb->get_var($query);
		
		// start with third address to avoid messy logic
		if ($largest === NULL || $largest === 0 || $largest === 1) {
			return 2;
		}

		return $largest + 1;
	}

	public function get_oldest_ready() {
		global $wpdb;

		$query = "SELECT `address` FROM `$this->tableName`
				  WHERE `mpk` = '$this->mpk'
				  AND `status` = 'ready'
				  AND `cryptocurrency` = '$this->cryptoId'
				  AND `hd_mode` = '$this->hdMode'
				  ORDER BY `mpk_index`
				  LIMIT 1";

		$address = $wpdb->get_var($query);
		NMM_Util::log(__FILE__, __LINE__, "Oldest ready address is: " . print_r($address, true));
		return $address;
	}

	public function get_pending() {
		global $wpdb;

		$query = "SELECT `order_id`, `address`, `order_amount`, `status`, `total_received` FROM `$this->tableName` 
				  WHERE `mpk` = '$this->mpk'
				  AND `cryptocurrency` = '$this->cryptoId'
				  AND `hd_mode` = '$this->hdMode'
				  AND (`status` = 'assigned' OR `status` = 'underpaid')";

		$results = $wpdb->get_results($query, ARRAY_A);

		return $results;
	}

	public function get_assigned() {
		global $wpdb;

		$query = "SELECT `order_id`, `address`, `assigned_at`, `total_received` FROM `$this->tableName` 
				  WHERE `mpk` = '$this->mpk'
				  AND `cryptocurrency` = '$this->cryptoId'
				  AND `hd_mode` = '$this->hdMode'
				  AND `status` = 'assigned'";

		$results = $wpdb->get_results($query, ARRAY_A);

		return $results;
	}

	public function set_total_received($address, $totalReceived) {
		global $wpdb;
		NMM_Util::log(__FILE__, __LINE__, 'Updating total received at ' . $address .' to: ' . $totalReceived);
		$query = "UPDATE `$this->tableName` SET `total_received` = '$totalReceived' WHERE `address` = '$address' AND `cryptocurrency` = '$this->cryptoId' AND `hd_mode` = '$this->hdMode'";
		$wpdb->query($query);
	}

	public function set_order_amount($address, $orderAmount) {
		global $wpdb;
		NMM_Util::log(__FILE__, __LINE__, 'Updating order amount at ' . $address . ' to: ' . $orderAmount);
		$query = "UPDATE `$this->tableName` SET `order_amount` = '$orderAmount' WHERE `address` = '$address' AND `cryptocurrency` = '$this->cryptoId' AND `hd_mode` = '$this->hdMode'";
		$wpdb->query($query);
	}

	public function set_status($address, $status) {
		global $wpdb;
		NMM_Util::log(__FILE__, __LINE__, 'Updating ' . $address . ' to ' . $status);
		if ($status === 'assigned') {
			$currentTime = time();
			$query = "UPDATE `$this->tableName` SET `status` = '$status', `assigned_at` = '$currentTime' WHERE `address` = '$address' AND `cryptocurrency` = '$this->cryptoId' AND `hd_mode` = '$this->hdMode'";
		}
		else {
			$query = "UPDATE `$this->tableName` SET `status` = '$status' WHERE `address` = '$address' AND `cryptocurrency` = '$this->cryptoId' AND `hd_mode` = '$this->hdMode'";
		}
		
		$wpdb->query($query);
	}

	public function set_order_id($address, $orderId) {
		global $wpdb;
		NMM_Util::log(__FILE__, __LINE__, 'Setting address ' . $address . ' order id to: ' . $orderId);
		$query = "UPDATE `$this->tableName` SET `order_id` = '$orderId' WHERE `address` = '$address' AND `cryptocurrency` = '$this->cryptoId' AND `hd_mode` = '$this->hdMode'";
		$wpdb->query($query);
	}
}

?>