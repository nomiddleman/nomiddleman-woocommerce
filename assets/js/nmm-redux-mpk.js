var validMpk = function(cryptoId, mpk) {
	mpkStart = mpk.substring(0, 5);

	if (mpkStart === 'xpub6' || mpkStart === 'ypub6' || mpkStart === 'zpub6') {
		if (mpk.length === 111) {
			return true;
		}	
	}

	return false;
}

var hideRemoveLinks = function (cryptoId, numberOfSamples) {
	for (let i = 0; i < numberOfSamples; i++) {
		jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-' + i + ' + a').hide();
	}
}

var makeReadonly = function (cryptoId, numberOfSamples) {
	for (let i = 0; i < numberOfSamples; i++) {
		jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-' + i).attr({'readonly': 'true'});
	}
}

var hideAddButton = function (cryptoId) {
	jQuery('#nmmpro_redux_options-' + cryptoId + '_hd_mpk_sample_addresses .redux-multi-text-add').hide();
}

var getMpk = function (cryptoId) {
	return mpk = jQuery('#' + cryptoId + '_hd_mpk-textarea').val().trim();
}

var updateSampleText = function (cryptoId, numberOfSamples, text) {
	for (let i = 0; i < numberOfSamples; i++) {
		jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-' + i).val(text);
	}	
}

var xhr = {};

var generateMpkAddresses = function (cryptoId, numberOfSamples) {
	let mpk = getMpk(cryptoId);

	var hdMode = '0';

	if (typeof this.getHdMode === 'function') {
		console.log('getHdMode exists');
		hdMode = this.getHdMode(cryptoId);
	}

	if (xhr[cryptoId] != null) {
		xhr[cryptoId].abort();
		xhr[cryptoId] = null;
	}

	if (!validMpk(cryptoId, mpk)) {
		updateSampleText(cryptoId, numberOfSamples, 'Please enter a valid mpk');
		return;
	}

	console.log('hdMode: ' + hdMode);
	
	xhr[cryptoId] = jQuery.ajax({
		type: "POST",
		url: "admin-ajax.php",
		data: 
			{ action: 'firstmpkaddress', 							  
			  mpk: mpk,
			  cryptoId: cryptoId,
			  hdMode: hdMode
			},
		beforeSend: function () {
			updateSampleText(cryptoId, numberOfSamples, 'Generating HD Addresses...');			
			
			jQuery('#nmmpro_redux_options-' + cryptoId + '_hd_mpk_sample_addresses input').removeClass('flash-red');
			jQuery('#nmmpro_redux_options-' + cryptoId + '_hd_mpk_sample_addresses input').removeClass('flash-green');
			jQuery('#nmmpro_redux_options-' + cryptoId + '_hd_mpk_sample_addresses input').addClass('flash-yellow');	 
		}
	}).fail(function(response) {
		jQuery('#nmmpro_redux_options-' + cryptoId + '_hd_mpk_sample_addresses input').removeClass('flash-yellow');
		if (response.status === 0) {
			return;
		}
		updateSampleText(cryptoId, numberOfSamples, 'Address creation failed, please check your mpk.');
		
		jQuery('#nmmpro_redux_options-' + cryptoId + '_hd_mpk_sample_addresses input').addClass('flash-red');
		
	}).done(function(responseJson) {		
		jQuery('#nmmpro_redux_options-' + cryptoId + '_hd_mpk_sample_addresses input').removeClass('flash-yellow');
		jQuery('#nmmpro_redux_options-' + cryptoId + '_hd_mpk_sample_addresses input').addClass('flash-green');
		addresses = JSON.parse(responseJson);

		if (addresses[0] === 'You have entered a valid Segwit MPK.') {
			updateSampleText(cryptoId, numberOfSamples, '');
			jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-0').val(addresses[0]);
			if (jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-0').parent().children().length === 2) {
				jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-0').parent().append(addresses[1]);
			}			
		}
		else {
			jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-0').val(addresses[0]);
			jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-1').val(addresses[1]);
			jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-2').val(addresses[2]);				
		}
	}); //close jQuery.ajax(	
}

jQuery(document).ready(function() {
	let numberOfSamples = 3;

	let hdCryptos = ['BTC', 'LTC', 'DASH', 'DOGE', 'QTUM', 'XMY', 'BTX'];

	hdCryptos.forEach(function (cryptoId) {
		hideRemoveLinks(cryptoId, numberOfSamples);
		makeReadonly(cryptoId, numberOfSamples);
		hideAddButton(cryptoId);
		
		firstAddressValue = jQuery('#' + cryptoId + '_hd_mpk_sample_addresses-0').val();
		firstAddressEmpty = firstAddressValue === ' ';
		
		existingMpk = getMpk(cryptoId);
		existingMpkValid = existingMpk.length === 111;

		if (existingMpkValid) {
			generateMpkAddresses(cryptoId, numberOfSamples);
		}

		jQuery('#' + cryptoId + '_hd_mpk-textarea').on('keyup', function(){
			generateMpkAddresses(cryptoId, numberOfSamples);
		});

		if (typeof this.getHdMode === 'function') {
			var hdModeKey = 'input[name="nmmpro_redux_options[' + cryptoId + '_hd_mode]"]';
			jQuery(hdModeKey).on('change', function () {
				generateMpkAddresses(cryptoId, numberOfSamples);
			});
		}
	});
	
});