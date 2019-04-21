<?php

class NMM_Carousel_Repo {
	private $tableName;
	public function __construct() {
		global $wpdb;
		$this->tableName = $wpdb->prefix . NMM_CAROUSEL_TABLE;
		
		$countCryptos = count(NMM_Cryptocurrencies::get());
		$countCryptosInDb = self::get_count();
		
		if ($countCryptos != $countCryptosInDb) {			
			self::init();
		}
	}

	public static function init() {
		global $wpdb;
		$tableName = $wpdb->prefix . NMM_CAROUSEL_TABLE;
		
		$cryptos = NMM_Cryptocurrencies::get();

		$needsInsert = false;

		$query = "INSERT INTO `$tableName` (`cryptocurrency`) VALUES";

		foreach ($cryptos as $crypto) {
			$cryptoId = $crypto->get_id();
		
			if (!self::record_exists($cryptoId)) {
				$query .= " ('$cryptoId'),";
				$needsInsert = true;
			}
		}

		if ($needsInsert) {
			$query = rtrim($query, ',');

			@$wpdb->query($query);
		}
	}

	public static function get_count() {
		global $wpdb;		
		$tableName = $wpdb->prefix . NMM_CAROUSEL_TABLE;
		$query = "SELECT count(*) FROM `$tableName`";

		$result = $wpdb->get_var($query);		

		return $result;
	}

	public static function record_exists($cryptoId) {
		global $wpdb;		
		$tableName = $wpdb->prefix . NMM_CAROUSEL_TABLE;
		$query = "SELECT count(*) FROM `$tableName` WHERE `cryptocurrency` = '$cryptoId'";

		$result = $wpdb->get_var($query);		

		return $result;
	}

	public function set_index($cryptoId, $index) {
		global $wpdb;
		NMM_Util::log(__FILE__, __LINE__, 'Updating index for ' . $cryptoId . ' to ' . $index);
		
		$query = "UPDATE `$this->tableName` SET `current_index` = '$index' WHERE `cryptocurrency` = '$cryptoId'";

		$wpdb->query($query);
	}

	public function get_index($cryptoId) {
		global $wpdb;		

		$query = "SELECT `current_index` FROM `$this->tableName` WHERE `cryptocurrency` = '$cryptoId'";

		$currentIndex = $wpdb->get_var($query);
		NMM_Util::log(__FILE__, __LINE__, 'Getting index: ' . $currentIndex);		
		return $currentIndex;
	}

	public function set_buffer($cryptoId, $buffer) {
		global $wpdb;
		//NMM_Util::log(__FILE__, __LINE__, 'Updating buffer for ' . $cryptoId . ' to ' . print_r($buffer, true));

		$serializedBuffer = NMM_Util::serialize_buffer($buffer);
		$query = "UPDATE `$this->tableName` SET `buffer` = '$serializedBuffer' WHERE `cryptocurrency` = '$cryptoId'";

		$wpdb->query($query);
	}

	public function get_buffer($cryptoId) {
		global $wpdb;		

		$query = "SELECT `buffer` FROM `$this->tableName` WHERE `cryptocurrency` = '$cryptoId'";

		$serializedResult = $wpdb->get_results($query, ARRAY_A);
		
		$result = unserialize($serializedResult[0]['buffer']);

		NMM_Util::log(__FILE__, __LINE__, 'Getting buffer: ' . print_r($result, true));
		
		return $result;
	}
}

?>