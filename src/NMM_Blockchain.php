<?php

// Class that communicates with various blockchains via HTTP
class NMM_Blockchain {	

	public static function get_blockchaininfo_total_received_for_btc_address($address, $requiredConfirmations) {
		$userAgentString = self::get_user_agent_string();
		$request = 'https://blockchain.info/q/getreceivedbyaddress/' . $address . '?confirmations=' . $requiredConfirmations;

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));
			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$totalReceivedSatoshi = (float) json_decode($response['body']);
		$result = array (
			'result' => 'success',
			'total_received' => $totalReceivedSatoshi / 100000000,
		);

		return $result;
	}

	public static function get_blockexplorer_total_received_for_btc_address($address) {
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://blockexplorer.com/api/addr/' . $address . '/totalReceived';

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));
			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$totalReceivedSatoshi = (float) json_decode($response['body']);

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceivedSatoshi / 100000000,
		);

		return $result;
	}

	public static function get_chainso_total_received_for_btc_address($address) {
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://chain.so/api/v2/get_address_received/BTC/' . $address;

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));
			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$totalReceivedSatoshi = (float) json_decode($response['body'])->data->confirmed_received_value;

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceivedSatoshi,
		);

		return $result;
	}

	public static function get_blockcypher_total_received_for_ltc_address($address, $requiredConfirmations) {
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://api.blockcypher.com/v1/ltc/main/addrs/' . $address . '?confirmations=' . $requiredConfirmations;

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));
			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$totalReceivedMmltc = json_decode($response['body'])->total_received;
		$totalReceived = $totalReceivedMmltc / 100000000;

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceived,
		);

		return $result;
	}

	public static function get_chainso_total_received_for_ltc_address($address) {
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://chain.so/api/v2/get_address_received/LTC/' . $address;

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}
		
		$totalReceived = (float) json_decode($response['body'])->data->confirmed_received_value;		

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceived,
		);

		return $result;
	}

	public static function get_qtuminfo_total_received_for_qtum_address($address) {
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://qtum.info/api/address/' . $address;

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}
		
		$totalReceived = (float) json_decode($response['body'])->totalReceived / 100000000;

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceived,
		);

		return $result;
	}

	public static function get_dashblockexplorer_total_received_for_dash_address($address) {
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://dashblockexplorer.com/api/addr/' . $address . '/totalReceived';

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}
		
		$totalReceived = (float) json_decode($response['body']) / 100000000;

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceived,
		);

		return $result;
	}

	public static function get_chainso_total_received_for_doge_address($address) {		
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://chain.so/api/v2/get_address_received/DOGE/' . $address;

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));
			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$totalReceivedSatoshi = (float) json_decode($response['body'])->data->confirmed_received_value;

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceivedSatoshi,
		);

		return $result;
	}

	public static function get_blockbook_total_received_for_xmy_address($address) {		
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://blockbook.myralicious.com/api/address/' . $address;

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));
			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$totalReceived = (float) json_decode($response['body'])->balance;

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceived,
		);

		return $result;
	}

	public static function get_insight_total_received_for_btx_address($address) {		
		$userAgentString = self::get_user_agent_string();
		
		$request = 'https://insight.bitcore.cc/api/addr/' . $address;

		$args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));
			$result = array (
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$totalReceived = (float) json_decode($response['body'])->balance;

		$result = array (
			'result' => 'success',
			'total_received' => $totalReceived,
		);

		return $result;
	}


	public static function get_ada_address_transactions($address) {
		$request = 'https://cardanoexplorer.com/api/addresses/summary/' . $address;
		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->Right->caTxList;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();

		foreach ($rawTransactions as $rawTransaction) {
			$outputs = $rawTransaction->ctbOutputs;

			foreach ($outputs as $output) {
				if ($output[0] === $address) {
					$amount = $output[1]->getCoin;
					$transactions[] = new NMM_Transaction($amount,
														  10000,
														  $rawTransaction->ctbTimeIssued,
														  $rawTransaction->ctbId);
				}				
			}
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_bch_address_transactions($address) {
		$request = 'https://blockdozer.com/api/txs?address=' . $address;
		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->txs;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}

		if ($address[0] === 'p' || $address[0] === 'q') {
			$addressToMatch = \CashAddress\CashAddress::new2old($address, false);
		}
		else {
			$addressToMatch = $address;
		}

		$transactions = array();

		foreach ($rawTransactions as $rawTransaction) {
			$outputs = $rawTransaction->vout;

			foreach ($outputs as $output) {
				if (!isset($output->scriptPubKey->addresses)) {					
					continue;
				}

				if (in_array($addressToMatch, $output->scriptPubKey->addresses)) {
					$hash = $rawTransaction->txid;
					$timeStamp = $rawTransaction->time;
					$amount = $output->value * 100000000;
					$confirmations = $rawTransaction->confirmations;

					$transactions[] = new NMM_Transaction($amount, $confirmations, $timeStamp, $hash);
				}
			}
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_blk_address_transactions($address) {
		
		$request = 'https://blackcoin.holytransaction.com/ext/getaddress/' . $address;

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		if (property_exists($body, 'error')) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . $body->error);
			$result = array(
				'result' => 'error',
				'total_received' => '',
			);
			return $result;
		}

		$rawTransactionIds = $body->last_txs;
		if (!is_array($rawTransactionIds)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		
		foreach ($rawTransactionIds as $rawTransactionId) {			
			if ($rawTransactionId->type === 'vout' || $rawTransactionId->type === 'vin') {

				$txId = $rawTransactionId->addresses;

				$request2 = 'https://blackcoin.holytransaction.com/api/getrawtransaction?txid=' . $txId . '&decrypt=1';
				
				$response2 = wp_remote_get($request2);

				if (is_wp_error($response2) || $response2['response']['code'] !== 200) {
					continue;
				}

				$rawTransaction = json_decode($response2['body']);

				$vouts = $rawTransaction->vout;

				foreach ($vouts as $vout) {
					if ($vout->scriptPubKey->addresses[0] === $address) {
						$transactions[] = new NMM_Transaction($vout->value * 100000000,
															  $rawTransaction->confirmations,
															  $rawTransaction->time,
															  $rawTransaction->txid);
					}
				}


				
			}			
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_bsv_address_transactions($address) {
		
		$request = 'https://api.blockchair.com/bitcoin-sv/outputs?q=recipient(' . $address . ')';

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->data;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
				
			$transactions[] = new NMM_Transaction($rawTransaction->value,
												  10000,
												  strtotime($rawTransaction->time),
												  $rawTransaction->transaction_hash);
			
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_btc_address_transactions($address) {
		$userAgentString = self::get_user_agent_string();

        $request = 'https://blockexplorer.com/api/txs/?address=' . $address;

        $args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));
            $request2 = 'https://api.blockcypher.com/v1/btc/main/addrs/' . $address;
            $response2 = wp_remote_get($request2, $args);
            if (is_wp_error($response2) || $response2['response']['code'] !== 200) {
                $result = array(
                    'result' => 'error',
                    'total_received' => '',
                );

                return $result;
            }

            $body = json_decode($response2['body']);

            $rawTransactions = $body->txrefs;
            if (!is_array($rawTransactions)) {
                $result = array(
                    'result' => 'error',
                    'message' => 'No transactions found',
                );

                return $result;
            }
            $transactions = array();
            foreach ($rawTransactions as $rawTransaction) {
                if ($rawTransaction->tx_input_n == -1) {
                    $transactions[] = new NMM_Transaction(
                        $rawTransaction->value,
                        $rawTransaction->confirmations,
                        $rawTransaction->confirmed,
                        $rawTransaction->tx_hash);
                }
            }
            $result = array (
                'result' => 'success',
                'transactions' => $transactions,
            );

            return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->txs;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
			foreach ($rawTransaction->vout as $vout) {
				if ($vout->scriptPubKey->addresses[0] === $address) {
					$transactions[] = new NMM_Transaction($vout->value * 100000000,
														  $rawTransaction->confirmations,
														  $rawTransaction->time,
														  $rawTransaction->txid);
				}
			}


		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_btx_address_transactions($address) {
		
		$request = 'https://insight.bitcore.cc/api/addr/' . $address;
		
		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$transactionIds = $body->transactions;
		if (!is_array($transactionIds)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();

		foreach ($transactionIds as $transactionId) {

				$request2 = 'https://insight.bitcore.cc/api/tx/' . $transactionId;
				
				$response2 = wp_remote_get($request2);

				if (is_wp_error($response2) || $response2['response']['code'] !== 200) {
					continue;
				}

				$rawTransaction = json_decode($response2['body']);

				$vouts = $rawTransaction->vout;

			foreach ($rawTransaction->vout as $vout) {
				if ($vout->scriptPubKey->addresses[0] === $address) {
					$transactions[] = new NMM_Transaction($vout->value * 100000000, 
														  $rawTransaction->confirmations, 
														  $rawTransaction->time,
														  $rawTransaction->txid);		
				}
			}		
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}
	
	public static function get_dash_address_transactions($address) {		
		
		$request = 'https://dashblockexplorer.com/api/txs/?address=' . $address;
		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->txs;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
			foreach ($rawTransaction->vout as $vout) {
				if ($vout->scriptPubKey->addresses[0] === $address) {
					$transactions[] = new NMM_Transaction($vout->value * 100000000, 
														  $rawTransaction->confirmations, 
														  $rawTransaction->time,
														  $rawTransaction->txid);		
				}
			}
			
		
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_dcr_address_transactions($address) {
		
		$request = 'https://explorer.dcrdata.org/insight/api/txs/?address=' . $address;
		
		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->txs;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
			foreach ($rawTransaction->vout as $vout) {
				if ($vout->scriptPubKey->addresses[0] === $address) {
					$transactions[] = new NMM_Transaction($vout->value * 100000000, 
														  $rawTransaction->confirmations, 
														  $rawTransaction->time,
														  $rawTransaction->txid);		
				}
			}
			
		
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_doge_address_transactions($address) {
		
		$request = 'https://chain.so/api/v2/get_tx_received/DOGE/' . $address;

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->data->txs;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {			
				
			$transactions[] = new NMM_Transaction($rawTransaction->value * 100000000,
												  $rawTransaction->confirmations,
												  $rawTransaction->time,
												  $rawTransaction->txid);
			
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

    public static function get_dgb_address_transactions($address) {
        $args = array('timeout' => 25);
        $request = 'https://explorer-1.us.digibyteservers.io/api/txs/?address=' . $address;

        $response = wp_remote_get($request, $args);

        if (is_wp_error($response) || $response['response']['code'] !== 200) {
            NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

            //TODO: https://digiexplorer.info/
            $result = array(
                'result' => 'error',
                'total_received' => '',
            );

            return $result;
        }

        $body = json_decode($response['body']);

        $rawTransactions = $body->txs;
        if (!is_array($rawTransactions)) {
            $result = array(
                'result' => 'error',
                'message' => 'No transactions found',
            );

            return $result;
        }
        $transactions = array();
        foreach ($rawTransactions as $rawTransaction) {
            foreach ($rawTransaction->vout as $vout) {
                if ($vout->scriptPubKey->addresses[0] === $address) {
                    $transactions[] = new NMM_Transaction($vout->value * 100000000,
                        $rawTransaction->confirmations,
                        $rawTransaction->time,
                        $rawTransaction->txid);
                }
            }


        }

        $result = array (
            'result' => 'success',
            'transactions' => $transactions,
        );

        return $result;
    }

	public static function get_eos_address_transactions($address) {
		
		$request = 'https://api.eospark.com/api?module=account&action=get_account_related_trx_info&account=' . $address . '&apikey=a9564ebc3289b7a14551baf8ad5ec60a';

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200 || json_decode($response['body'])->errno == 429) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->data->trace_list;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
			if ($rawTransaction->receiver === $address) {
				$transactions[] = new NMM_Transaction($rawTransaction->quantity * 10000,
													  10000,
													  strtotime($rawTransaction->timestamp),
													  $rawTransaction->trx_id);
			}
			
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_etc_address_transactions($address) {
		
		$request = 'https://blockscout.com/etc/mainnet/api?module=account&action=txlist&address=' . $address;

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->result;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();


		foreach ($rawTransactions as $rawTransaction) {
			
			if (strtolower($rawTransaction->to) === strtolower($address)) {
				
				$transactions[] = new NMM_Transaction($rawTransaction->value, 
													  $rawTransaction->confirmations, 
													  $rawTransaction->timeStamp,
													  $rawTransaction->hash);
			}
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_eth_address_transactions($address) {
		
		$request = 'http://api.etherscan.io/api?module=account&action=txlist&address=' . $address . '&startblock=0&endblock=99999999&sort=desc';

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->result;

		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}

		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
			
			if (strtolower($rawTransaction->to) === strtolower($address)) {
				
				$transactions[] = new NMM_Transaction($rawTransaction->value, 
													  $rawTransaction->confirmations, 
													  $rawTransaction->timeStamp,
													  $rawTransaction->hash);
			}
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_grs_address_transactions($address) {
		
		$request = 'https://groestlsight.groestlcoin.org/api/txs?address=' . $address;
		
		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->txs;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
			foreach ($rawTransaction->vout as $vout) {
				if ($vout->scriptPubKey->addresses[0] === $address) {
					$transactions[] = new NMM_Transaction($vout->value * 100000000, 
														  $rawTransaction->confirmations, 
														  $rawTransaction->time,
														  $rawTransaction->txid);		
				}
			}
			
		
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_lsk_address_transactions($address) {
		
		$request = 'https://node08.lisk.io/api/transactions?recipientId=' . $address . '&limit=10&offset=0&sort=amount%3Aasc';

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->data;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {				
			$transactions[] = new NMM_Transaction($rawTransaction->amount, 
												  $rawTransaction->confirmations, 
												  time(),
												  $rawTransaction->id);
		
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_ltc_address_transactions($address) {
		$userAgentString = self::get_user_agent_string();

        $request = 'https://api.blockcypher.com/v1/ltc/main/addrs/' . $address;

        $args = array(
			'user-agent' => $userAgentString
		);

		$response = wp_remote_get($request, $args);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
            $result = array(
                'result' => 'error',
                'total_received' => '',
            );

            return $result;
        }

		$body = json_decode($response['body']);

        $rawTransactions = $body->txrefs;
        if (!is_array($rawTransactions)) {
            $result = array(
                'result' => 'error',
                'message' => 'No transactions found',
            );

            return $result;
        }
        $transactions = array();
        foreach ($rawTransactions as $rawTransaction) {
            if ($rawTransaction->tx_input_n == -1) {
                $transactions[] = new NMM_Transaction(
                    $rawTransaction->value,
                    $rawTransaction->confirmations,
                    $rawTransaction->confirmed,
                    $rawTransaction->tx_hash);
            }
        }
        $result = array (
            'result' => 'success',
            'transactions' => $transactions,
        );

        return $result;
	}

	public static function get_onion_address_transactions($address) {
		
		//$request = 'https://explorer.deeponion.org/ext/getaddress/' . $address;
		$request = 'http://onionexplorer.youngwebsolutions.com:3001/ext/getaddress/' . $address;
		
		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		if (property_exists($body, 'error')) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . $body->error);
			$result = array(
				'result' => 'error',
				'total_received' => '',
			);
			return $result;
		}

		$rawTransactionIds = $body->last_txs;
		if (!is_array($rawTransactionIds)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		
		foreach ($rawTransactionIds as $rawTransactionId) {			
			if ($rawTransactionId->type === 'vout' || $rawTransactionId->type === 'vin') {

				$txId = $rawTransactionId->addresses;

				$request2 = 'https://explorer.deeponion.org/api/getrawtransaction?txid=' . $txId . '&decrypt=1';
				
				$response2 = wp_remote_get($request2);

				if (is_wp_error($response2) || $response2['response']['code'] !== 200) {
					continue;
				}

				$rawTransaction = json_decode($response2['body']);

				$vouts = $rawTransaction->vout;

				foreach ($vouts as $vout) {
					if ($vout->scriptPubKey->addresses[0] === $address) {
						$transactions[] = new NMM_Transaction($vout->value * 100000000,
															  $rawTransaction->confirmations,
															  $rawTransaction->time,
															  $rawTransaction->txid);
					}
				}


				
			}			
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}	

	public static function get_trx_address_transactions($address) {
		
		$request = 'https://apilist.tronscan.org/api/transaction?address=' . $address;

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->data;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		
		foreach ($rawTransactions as $rawTransaction) {
			
			if ($rawTransaction->toAddress === $address && $rawTransaction->confirmed) {
				$transactions[] = new NMM_Transaction($rawTransaction->contractData->amount,
													  10000,
													  $rawTransaction->timestamp/1000,
													  $rawTransaction->hash);
			}
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_waves_address_transactions($address) {
		
		$request = 'https://nodes.wavesnodes.com/transactions/address/' . $address . '/limit/100';

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body[0];
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
			if ($rawTransaction->type == '4') {
				$transactions[] = new NMM_Transaction($rawTransaction->amount, 
													  10000, 
													  $rawTransaction->timestamp,
													  $rawTransaction->id);
			}
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_xem_address_transactions($address) {
		
		$request = 'http://108.61.168.86:7890/account/transfers/incoming?address=' . $address;

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->data;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {				
			$transactions[] = new NMM_Transaction($rawTransaction->transaction->amount, 
												  10000, 
												  time(),
												  $rawTransaction->meta->hash->data);
		
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_xlm_address_transactions($address) {
		$request = 'https://horizon.stellar.org/accounts/' . $address . '/payments?order=desc';

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->_embedded->records;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		
		foreach ($rawTransactions as $rawTransaction) {
			
			if ($rawTransaction->type === 'create_account') {
				if ($rawTransaction->account === $address) {
					$transactions[] = new NMM_Transaction($rawTransaction->starting_balance * 10000000,
												  10000,
												  strtotime($rawTransaction->created_at),
												  $rawTransaction->transaction_hash);
				}
			}
			if ($rawTransaction->type === 'payment') {
				if ($rawTransaction->to === $address) {
					$transactions[] = new NMM_Transaction($rawTransaction->amount * 10000000,
												  10000, 
												  strtotime($rawTransaction->created_at),
												  $rawTransaction->transaction_hash);
				}
			}
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_xmy_address_transactions($address) {
		
		$request = 'https://blockbook.myralicious.com/api/address/' . $address;
		
		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$transactionIds = $body->transactions;
		if (!is_array($transactionIds)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();

		foreach ($transactionIds as $transactionId) {

				$request2 = 'https://blockbook.myralicious.com/api/tx/' . $transactionId;
				
				$response2 = wp_remote_get($request2);

				if (is_wp_error($response2) || $response2['response']['code'] !== 200) {
					continue;
				}

				$rawTransaction = json_decode($response2['body']);

				$vouts = $rawTransaction->vout;

			foreach ($rawTransaction->vout as $vout) {
				if ($vout->scriptPubKey->addresses[0] === $address) {
					$transactions[] = new NMM_Transaction($vout->value * 100000000, 
														  $rawTransaction->confirmations, 
														  $rawTransaction->time,
														  $rawTransaction->txid);		
				}
			}		
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_xrp_address_transactions($address) {
		
		$request = 'https://data.ripple.com/v2/accounts/' . $address . '/transactions';

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->transactions;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {
			if ($rawTransaction->tx->Destination === $address) {
				
				$transactions[] = new NMM_Transaction($rawTransaction->tx->Amount,
												  10000, 
												  strtotime($rawTransaction->date),
												  $rawTransaction->hash);
			}			
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_xtz_address_transactions($address) {
		
		$request = 'https://api6.tzscan.io/v3/balance_updates/' . $address;

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();

		foreach ($rawTransactions as $rawTransaction) {
			if ($rawTransaction->account === $address && $rawTransaction->diff > 0) {
				
				$transactions[] = new NMM_Transaction($rawTransaction->diff,
												  10000, 
												  strtotime($rawTransaction->date->date),
												  strtotime($rawTransaction->date->date));
			}			
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_zec_address_transactions($address) {
		
		$request = 'https://chain.so/api/v2/get_tx_received/ZEC/' . $address;

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->data->txs;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();
		foreach ($rawTransactions as $rawTransaction) {			
				
			$transactions[] = new NMM_Transaction($rawTransaction->value * 100000000,
												  $rawTransaction->confirmations,
												  $rawTransaction->time,
												  $rawTransaction->txid);
			
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	public static function get_erc20_address_transactions($cryptoId, $address) {
		$request = 'http://api.etherscan.io/api?module=account&action=tokentx&address=' . $address . '&startblock=0&endblock=999999999&sort=asc';

		$response = wp_remote_get($request);

		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			NMM_Util::log(__FILE__, __LINE__, 'FAILED API CALL ( ' . $request . ' ): ' . print_r($response, true));

			$result = array(
				'result' => 'error',
				'total_received' => '',
			);

			return $result;
		}

		$body = json_decode($response['body']);

		$rawTransactions = $body->result;
		if (!is_array($rawTransactions)) {
			$result = array(
				'result' => 'error',
				'message' => 'No transactions found',
			);

			return $result;
		}
		$transactions = array();

		foreach($rawTransactions as $rawTransaction) {
			
			
			if (strtolower($rawTransaction->to) === strtolower($address) && $rawTransaction->tokenSymbol === $cryptoId) {

				$transactions[] = new NMM_Transaction($rawTransaction->value,
												  $rawTransaction->confirmations,
												  $rawTransaction->timeStamp,
												  $rawTransaction->hash);
			}
		}

		$result = array (
			'result' => 'success',
			'transactions' => $transactions,
		);

		return $result;
	}

	private static function get_user_agent_string() {
		return 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.12 (KHTML, like Gecko) Chrome/9.0.576.1 Safari/534.12';
	}
}

?>