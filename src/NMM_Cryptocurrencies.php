<?php

// Crypto Helper
class NMM_Cryptocurrencies {

	public static function get() {
        // id, name, round_precision, icon_filename, refresh_time, symbol, has_hd, has_autopay, needs_confirmations, erc20contract
		$cryptoArray = array(
            
            // privacy mpk
            'BTC' => new NMM_Cryptocurrency('BTC', 'Bitcoin', 8, 'bitcoin_logo_small.png', 60, '₿', true, true, true, ''),
            'LTC' => new NMM_Cryptocurrency('LTC', 'Litecoin', 8, 'litecoin_logo_small.png', 60, 'Ł', true, true, true, ''),
            'QTUM' => new NMM_Cryptocurrency('QTUM', 'Qtum', 8, 'qtum_logo_small.png', 60, '', true, false, true, ''),            
            'DASH' => new NMM_Cryptocurrency('DASH', 'Dash', 8, 'dash_logo_small.png', 60, '', true, true, true, ''),            
            'DOGE' => new NMM_Cryptocurrency('DOGE', 'Dogecoin', 8, 'dogecoin_logo_small.png', 60, 'Ð', true, true, true, ''),
            'XMY' => new NMM_Cryptocurrency('XMY', 'Myriad', 8, 'myriad_logo_small.png', 60, '', true, true, true, ''),
            'BTX' => new NMM_Cryptocurrency('BTX', 'Bitcore', 8, 'bitcore_logo_small.png', 60, '', true, true, true, ''),
			
            // auto-pay coins            
            'ETH' => new NMM_Cryptocurrency('ETH', 'Ethereum', 18, 'ethereum_logo_small.png', 60, 'Ξ', false, true, true, ''),
            'DGB' => new NMM_Cryptocurrency('DGB', 'Digibyte', 8, 'digibyte_logo_small.png', 60, '', false, false, true, ''),
            'ZEC' => new NMM_Cryptocurrency('ZEC', 'Zcash', 8, 'zcash_logo_small.png', 60, 'ⓩ', false, true, true, ''),
            'DCR' => new NMM_Cryptocurrency('DCR', 'Decred', 8, 'decred_logo_small.png', 60, '', false, true, true, ''),            
            'ADA' => new NMM_Cryptocurrency('ADA', 'Cardano', 6, 'cardano_logo_small.png', 60, '', false, true, false, ''),
            'XTZ' => new NMM_Cryptocurrency('XTZ', 'Tezos', 6, 'tezos_logo_small.png', 60, '', false, true, false, ''),
            'TRX' => new NMM_Cryptocurrency('TRX', 'Tron', 6, 'tron_logo_small.png', 60, '', false, true, false, ''),
            'XLM' => new NMM_Cryptocurrency('XLM', 'Stellar', 7, 'stellar_logo_small.png', 60, '', false, true, false, ''),
            'BCH' => new NMM_Cryptocurrency('BCH', 'Bitcoin Cash', 8, 'bitcoincash_logo_small.png', 60, '', false, true, true, ''),
            'EOS' => new NMM_Cryptocurrency('EOS', 'EOS', 4, 'eos_logo_small.png', 60, '', false, true, false, ''),
            'BSV' => new NMM_Cryptocurrency('BSV', 'Bitcoin SV', 8, 'bitcoinsv_logo_small.png', 60, '', false, true, false, ''),            
            'XRP' => new NMM_Cryptocurrency('XRP', 'XRP', 6, 'xrp_logo_small.png', 60, '', false, true, false, ''),
            'ONION' => new NMM_Cryptocurrency('ONION', 'DeepOnion', 8, 'deeponion_logo_small.png', 60, '', false, true, true, ''),
            'BLK' => new NMM_Cryptocurrency('BLK', 'BlackCoin', 8, 'blackcoin_logo_small.png', 60, '', false, true, true, ''),
            'ETC' => new NMM_Cryptocurrency('ETC', 'Ethereum Classic', 18, 'ethereumclassic_logo_small.png', 60, '', false, true, true, ''),
            'LSK' => new NMM_Cryptocurrency('LSK', 'Lisk', 8, 'lisk_logo_small.png', 60, '', false, true, true, ''),
            'XEM' => new NMM_Cryptocurrency('XEM', 'NEM', 6, 'nem_logo_small.png', 60, '', false, true, true, ''),
            'WAVES' => new NMM_Cryptocurrency('WAVES', 'Waves', 8, 'waves_logo_small.png', 60, '', false, true, true, ''),            

            // tokens
            'HOT' => new NMM_Cryptocurrency('HOT', 'Holochain', 18, 'holochain_logo_small.png', 60, '', false, true, true, '0x6c6ee5e31d828de241282b9606c8e98ea48526e2'),
            'LINK' => new NMM_Cryptocurrency('LINK', 'Chainlink', 18, 'chainlink_logo_small.png', 60, '', false, true, true, '0x514910771af9ca656af840dff83e8264ecf986ca'),
            'BAT' => new NMM_Cryptocurrency('BAT', 'Basic Attention Token', 18, 'basicattentiontoken_logo_small.png', 60, '', false, true, true, '0x0d8775f648430679a709e98d2b0cb6250d2887ef'),            
            'MKR' => new NMM_Cryptocurrency('MKR', 'Maker', 18, 'maker_logo_small.png', 60, '', false, true, true, '0x9f8f72aa9304c8b593d555f12ef6589cc3a579a2'),
            'OMG' => new NMM_Cryptocurrency('OMG', 'OmiseGO', 18, 'omisego_logo_small.png', 60, '', false, true, true, '0xd26114cd6EE289AccF82350c8d8487fedB8A0C07'),
            'REP' => new NMM_Cryptocurrency('REP', 'Augur', 18, 'augur_logo_small.png', 60, '', false, true, true, '0x1985365e9f78359a9B6AD760e32412f4a445E862'),
            'GNO' => new NMM_Cryptocurrency('GNO', 'Gnosis', 18, 'gnosis_logo_small.png', 60, '', false, true, true, '0x6810e776880c02933d47db1b9fc05908e5386b96'),
            'MLN' => new NMM_Cryptocurrency('MLN', 'Melon', 18, 'melon_logo_small.png', 60, '', false, true, true, '0xbeb9ef514a379b997e0798fdcc901ee474b6d9a1'),
            'ZRX' => new NMM_Cryptocurrency('ZRX', '0x', 18, '0x_logo_small.png', 60, '', false, true, true, '0xe41d2489571d322189246dafa5ebde1f4699f498'),
            

            // no support
            'XMR' => new NMM_Cryptocurrency('XMR', 'Monero', 12, 'monero_logo_small.png', 60, 'ɱ', false, false, true, ''),
            'VRC' => new NMM_Cryptocurrency('VRC', 'Vericoin', 8, 'vericoin_logo_small.png', 60, '', false, false, true, ''),
            'BTG' => new NMM_Cryptocurrency('BTG', 'Bitcoin Gold', 8, 'bitcoingold_logo_small.png', 60, '', false, false, true, ''),
            'VET' => new NMM_Cryptocurrency('VET', 'VeChain', 18, 'vechain_logo_small.png', 60, '', false, false, true, ''),
            'BCD' => new NMM_Cryptocurrency('BCD', 'Bitcoin Diamond', 8, 'bitcoindiamond_logo_small.png', 60, '', false, false, true, ''),
            'BCN' => new NMM_Cryptocurrency('BCN', 'Bytecoin', 8, 'bytecoin_logo_small.png', 60, '', false, false, true, ''),
            'BNB' => new NMM_Cryptocurrency('BNB', 'Binance Coin', 18, 'binancecoin_logo_small.png', 60, '', false, false, true, ''),
            'GUSD' => new NMM_Cryptocurrency('GUSD', 'Gemini Dollar', 2, 'geminidollar_logo_small.png', 60, '', false, false, true, '0x056Fd409E1d7A124BD7017459dFEa2F387b6d5Cd'),
            
            
            // More searching required
            
            'POT' => new NMM_Cryptocurrency('POT', 'Potcoin', 18, 'potcoin_logo_small.png', 60, '', false, false, true, ''),
            // https://www.reddit.com/r/OntologyNetwork/comments/9duf28/api_to_get_ont_balance/
            'ONT' => new NMM_Cryptocurrency('ONT', 'Ontology', 18, 'ontology_logo_small.png', 60, '', false, false, true, ''),            
            
            // https://api.iogateway.cloud/api/Tangle/address/SDCUDAWKRZWFJFWROUAYVTKLZIGDNBDMBLZIWFWNXZLFRKPUGECMMZGPUFYZGANUZEP9VRPTFTVCKZVAWVRJTWZQDD/transactions
            
            'MIOTA' => new NMM_Cryptocurrency('MIOTA', 'Iota', 18, 'iota_logo_small.png', 60, '', false, false, true, ''),
        );

        return $cryptoArray;
	}

    public static function get_hd() {
        $cryptos = self::get();
        $privacyCryptos = [];

        foreach ($cryptos as $crypto) {
            if ($crypto->has_hd()) {
                $privacyCryptos[] = $crypto;
            }
        }

        return $privacyCryptos;
    }

    public static function get_erc20_tokens() {
        $cryptos = self::get();
        $erc20Tokens = [];

        foreach ($cryptos as $crypto) {            
            if ($crypto->is_erc20_token()) {                
                $erc20Tokens[$crypto->get_id()] = $crypto;
            }
        }

        return $erc20Tokens;
    }

    public static function get_non_erc20_tokens() {
        $cryptos = self::get();
        $nonErc20Tokens = [];

        foreach ($cryptos as $crypto) {
            if (!$crypto->is_erc20_token()) {
                $nonErc20Tokens[$crypto->get_id()] = $crypto;
            }
        }

        return $nonErc20Tokens;
    }

    public static function is_erc20_token($cryptoId) {

        if (array_key_exists($cryptoId, NMM_Cryptocurrencies::get_erc20_tokens())) {
            return true;
        }

        return false;
    }

    public static function get_erc20_contract($cryptoId) {
        $erc20Tokens = NMM_Cryptocurrencies::get_erc20_tokens();
        
        foreach ($erc20Tokens as $token) {
            if ($token->get_id() === $cryptoId) {                
                return $token->get_erc20_contract();
            }
        }

        return '';
    }


    public static function get_alpha() {
        $cryptoArray = NMM_Cryptocurrencies::get();
        
        $keys = array_map(function($val) {
                return $val->get_id();
            }, $cryptoArray);
        array_multisort($keys, $cryptoArray);
        return $cryptoArray;
    }

    // Php likes to convert numbers to scientific notation, so this handles displaying small amounts correctly
    public static function get_price_string($cryptoId, $amount) {
        $cryptos = self::get();
        $crypto = $cryptos[$cryptoId];

        // Round based on smallest unit of crypto
        $roundedAmount = round($amount, $crypto->get_round_precision(), PHP_ROUND_HALF_UP);

        // Forces displaying the number in decimal format, with as many zeroes as possible to display the smallest unit of crypto
        $formattedAmount = number_format($roundedAmount, $crypto->get_round_precision(), '.', '');

        // We probably have extra 0's on the right side of the string so trim those
        $amountWithoutZeroes = rtrim($formattedAmount, '0');

        // If it came out to an round whole number we have a dot on the right side, so take that off
        $amountWithoutTrailingDecimal = rtrim($amountWithoutZeroes, '.');

        return $amountWithoutTrailingDecimal;
    }

	public static function is_valid_wallet_address($cryptoId, $address) {
            
        if ($cryptoId === 'BTC') {            
            return preg_match('/^[13][a-km-zA-HJ-NP-Z0-9]{24,42}|bc[a-z0-9]{8,87}/', $address);
        }
        if ($cryptoId === 'ETH') {
            return preg_match('/0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'XMR') {
            // return preg_match('/4[0-9AB][1-9A-HJ-NP-Za-km-z]{93}/', $address);

            // 2-15-2019 not testing Monero
            return strlen($address) > 30;
        }
        if ($cryptoId === 'DOGE'){
            return preg_match('/^D{1}[5-9A-HJ-NP-U]{1}[1-9A-HJ-NP-Za-km-z]{32}/', $address);
        }
        if ($cryptoId === 'LTC') {
            return preg_match('/^[LM3][a-km-zA-HJ-NP-Z1-9]{26,33}|l[a-z0-9]{8,87}/', $address);
        }
        if ($cryptoId === 'ZEC') {
            $isTAddr =  preg_match('/^t1[a-zA-Z0-9]{33,36}/', $address);
            $isZAddr = preg_match('/^z[a-zA-Z0-9]{90,96}/', $address);

            return $isTAddr || $isZAddr;
        }
        if ($cryptoId === 'BCH') {
            $isOldAddress = preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,42}/', $address);
            $isNewAddress1 = preg_match('/^(q|p)[a-z0-9]{41}/', $address);
            $isNewAddress2 = preg_match('/^(Q|P)[A-Z0-9]{41}/', $address);

            return $isOldAddress || $isNewAddress1 || $isNewAddress2;
        }
        if ($cryptoId === 'DASH') {
            return preg_match('/^X[1-9A-HJ-NP-Za-km-z]{33}/', $address);
        }
        if ($cryptoId === 'XRP') {
            return preg_match('/^r[0-9a-zA-Z]{33}/', $address);
        }
        if ($cryptoId === 'ONION') {
            return preg_match('/^D[0-9a-zA-Z]{33}/', $address);
        }
        if ($cryptoId === 'BLK') {
            return preg_match('/^B[0-9a-zA-Z]{32,36}/', $address);
        }
        if ($cryptoId === 'VRC') {
            return preg_match('/^V[0-9a-zA-Z]{32,36}/', $address);
        }
        if ($cryptoId === 'ETC') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'REP') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'BTG') {
            return preg_match('/^[AG][a-km-zA-HJ-NP-Z0-9]{26,42}|bt[a-z0-9]{8,87}/', $address);
        }
        if ($cryptoId === 'EOS') {
            return strlen($address) == 12;
        }
        if ($cryptoId === 'BSV') {
            return preg_match('/^[13][a-km-zA-HJ-NP-Z0-9]{26,42}|q[a-z0-9]{9,88}/', $address);
        }
        if ($cryptoId === 'VET') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'TRX') {
            return preg_match('/^T[a-km-zA-HJ-NP-Z0-9]{26,42}/', $address);
        }
        if ($cryptoId === 'XLM') {
            return preg_match('/^G[A-Z0-9]{55}/', $address);
        }
        if ($cryptoId === 'QTUM') {
            return preg_match('/^Q[0-9a-zA-Z]{31,35}/', $address);
        }
        if ($cryptoId === 'ADA') {
            $match1 = preg_match('/^Ddz[0-9a-zA-Z]{80,120}/', $address);
            $match2 = preg_match('/^Ae2tdPwUPE[0-9a-zA-Z]{46,53}/', $address);

            return $match1 || $match2;
        }
        if ($cryptoId === 'XTZ') {
            return preg_match('/^tz1[0-9a-zA-Z]{30,39}/', $address);
        }
        if ($cryptoId === 'MLN') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'GNO') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'ONT') {
            return preg_match('/^A[0-9a-zA-Z]{31,35}/', $address);
        }
        if ($cryptoId === 'BAT') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'BCD') {
            return preg_match('/^1[0-9a-zA-Z]{31,35}/', $address);
        }
        if ($cryptoId === 'BCN') {
            return preg_match('/^2[0-9a-zA-Z]{91,99}/', $address);
        }
        if ($cryptoId === 'BNB') {
            return preg_match('/^bnb[a-zA-Z0-9]{37,48}/', $address) || preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'DCR') {
            return preg_match('/^D[0-9a-zA-Z]{31,35}/', $address);
        }
        if ($cryptoId === 'DGB') {
            return preg_match('/^D[0-9a-zA-Z]{31,35}/', $address);
        }
        if ($cryptoId === 'HOT') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'LINK') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'LSK') {
            return preg_match('/^[0-9a-zA-Z]{17,22}L/', $address);
        }
        if ($cryptoId === 'MIOTA') {
            return preg_match('/^[0-9a-zA-Z]{85,95}/', $address);
        }
        if ($cryptoId === 'MKR') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'OMG') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'POT') {
            return preg_match('/^P[0-9a-zA-Z]{31,35}/', $address);
        }
        if ($cryptoId === 'WAVES') {
            return preg_match('/^3[0-9a-zA-Z]{31,35}/', $address);
        }
        if ($cryptoId === 'XEM') {
            return preg_match('/^N[0-9a-zA-Z]{35,45}/', $address);
        }
        if ($cryptoId === 'ZRX') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'GUSD') {
            return preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        if ($cryptoId === 'XMY') {
            return preg_match('/^[M45][a-zA-Z0-9]{31,38}/', $address);   
        }
        if ($cryptoId === 'BTX') {            
            return preg_match('/^[2s][a-km-zA-HJ-NP-Z0-9]{24,42}|btx[a-z0-9]{8,87}/', $address);
        }

        // xrb_195mx9357zhmxsu53qqg3qbm6cqx3wq9h9wpdpj1b98n6mauj46mh9iwz1pg xrb_ 64
        // 51RguiuQAkVw5V6wQdkQnbpsm59szVrr91 M 4 5 34
        
        
        NMM_Util::log(__FILE__, __LINE__, 'Invalid cryptoId, contact plug-in developer.');        
        throw new Exception('Invalid cryptoId, contact plug-in developer.');
    }    
}

?>