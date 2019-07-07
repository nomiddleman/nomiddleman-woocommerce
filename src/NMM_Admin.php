<?php

if ( ! class_exists( 'Redux' ) ) {
    return;
}

$nmm_redux_args = array(
    // TYPICAL -> Change these values as you need/desire
    'opt_name'             => NMM_REDUX_ID,
    // This is where your data is stored in the database and also becomes your global variable name.
    'display_name'         => apply_filters('nmm_settings_display_name', 'Nomiddleman Crypto Payments for Woocommerce'),
    'display_version'      => NMM_VERSION,
    'hide_reset'           => false,
    'disable_tracking'     => true,
    //'intro_text'           => apply_filters('nmm_settings_intro', 'Welcome to the Nomiddleman Settings Page'),
    'system_info'          => true,
    'hide_expand'          => true,
    'show_options_object'  => false,
    'ajax_save'            => true,
    'admin_theme'          => 'classic',
    // Version that appears at the top of your panel
    'menu_type'            => 'menu',
    //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
    'allow_sub_menu'       => true,
    // Show the sections below the admin menu item or not
    'menu_title'           => apply_filters('nmm_settings_menu_title', 'Nomiddleman Crypto Payments'),
    'page_title'           => apply_filters('nmm_settings_page_title', 'Nomiddleman Crypto Settings'),
    // You will need to generate a Google API key to use this feature.
    // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
    'google_api_key'       => '',
    // Set it you want google fonts to update weekly. A google_api_key value is required.
    'google_update_weekly' => false,
    // Must be defined to add google fonts to the typography module
    'async_typography'     => false,
    // Use a asynchronous font on the front end or font string
    //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
    'admin_bar'            => false,
    // Show the panel pages on the admin bar
    'admin_bar_icon'       => '',
    // Choose an icon for the admin bar menu
    'admin_bar_priority'   => 50,
    // Choose an priority for the admin bar menu
    'global_variable'      => '',
    // Set a different name for your global variable other than the opt_name
    'dev_mode'             => false,
    // Show the time the page took to load, etc
    'update_notice'        => false,
    // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
    'customizer'           => false,
    // Enable basic customizer support
    'open_expanded'        => false,                    // Allow you to start the panel in an expanded way initially.
    'disable_save_warn'    => true,                    // Disable the save warning when a user changes a field
    // OPTIONAL -> Give you extra features
    'page_priority'        => 56,
    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
    'page_parent'          => 'themes.php',
    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
    'page_permissions'     => 'manage_options',
    // Permissions needed to access the options panel.
    'menu_icon'            => NMM_PLUGIN_DIR . '/assets/img/redux-menu-icon.svg',
    // Specify a custom URL to an icon
    'last_tab'             => '0',
    // Force your panel to always open to a specific tab (by id)
    'page_icon'            => '',
    // Icon displayed in the admin panel next to your menu_title
    'page_slug'            => NMM_REDUX_SLUG,
    // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
    'save_defaults'        => true,
    // On load save the defaults to DB before user clicks save or not
    'default_show'         => false,
    // If true, shows the default value next to each field that is not the default value.
    'default_mark'         => '',
    // What to print by the field's title if the value shown is default. Suggested: *
    'show_import_export'   => true,
    // Shows the Import/Export panel when not used as a field.
    // CAREFUL -> These options are for advanced use only
    'transient_time'       => 60 * MINUTE_IN_SECONDS,
    'output'               => true,
    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
    'output_tag'           => true,
    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
    // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
    'database'             => '',
    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
    'use_cdn'              => true,
    // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.
    // HINTS
    'hints'                => array(
        'icon'          => 'el el-question-sign',
        'icon_position' => 'right',
        'icon_color'    => 'lightgray',
        'icon_size'     => 'normal',
        'tip_style'     => array(
            'color'   => 'red',
            'shadow'  => true,
            'rounded' => false,
            'style'   => '',
        ),
        'tip_position'  => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
        'tip_effect'    => array(
            'show' => array(
                'effect'   => 'slide',
                'duration' => '500',
                'event'    => 'mouseover',
            ),
            'hide' => array(
                'effect'   => 'slide',
                'duration' => '500',
                'event'    => 'click mouseleave',
            ),
        ),
    )
);

Redux::setArgs(NMM_REDUX_ID, $nmm_redux_args);

function NMM_get_crypto_select_values() {
    $cryptoSelect = [];
    $cryptos = NMM_Cryptocurrencies::get_alpha();

    foreach ($cryptos as $crypto) {
        $cryptoSelect[$crypto->get_id()] = $crypto->get_name() . ' (' . $crypto->get_id() . ')';
    }

    return $cryptoSelect;   
}

$nmm_section = array(
    'title'  => 'General Settings',
    'id'     => 'general_settings',
    'desc'   => '',
    'fields'   => array(
        array(
            'id' => 'payment_label',
            'type' => 'text',            
            'default' => 'Pay with cryptocurrency',
            'title' => 'Payment Label',
            'desc' => 'This will be displayed on the checkout screen when the customer selects their payment option.',
        ),
             
    ),
);

$nmmCustomerMessage = array(
    array(
        'id' => 'payment_message_html',
        'type' => 'textarea',
        'default' => '',
        'title' => 'Customer Payment Message',
        'desc' => 'This is displayed above the crypto payment details on the payment screen (After the customer clicks "Checkout").',
    ),
);

$nmm_section['fields'] = array_merge($nmm_section['fields'], apply_filters('nmm_admin_customer_message', $nmmCustomerMessage));

Redux::setSection(NMM_REDUX_ID, $nmm_section);

$nmm_section = array(
    'title'  => 'Select Cryptocurrencies',
    'id'     => 'crypto_select_section',
    'desc'   => '',
    'fields'   => array(
        array(
            'id' => 'crypto_select',
            'type' => 'button_set',
            'multi' => true,
            'default' => [],
            'title' => 'Selected Cryptocurrencies',
            'options' => NMM_get_crypto_select_values(),
        ),
    ),
);

Redux::setSection(NMM_REDUX_ID, $nmm_section);

$nmm_cryptos = NMM_Cryptocurrencies::get_alpha();

foreach ($nmm_cryptos as $nmm_crypto) {    

    $nmm_cryptoOptions = array('0' => 'Classic Mode');
    
    if ($nmm_crypto->has_autopay()) {
        $nmm_cryptoOptions['1'] = 'Autopay Mode <strong>(BETA)</strong>';
    }
    if ($nmm_crypto->has_hd()) {
        $nmm_cryptoOptions['2'] = 'Privacy Mode';
    }

	$nmm_section = array(
	    'title'  => $nmm_crypto->get_name() . ' (' . $nmm_crypto->get_id() . ')',
	    'id'     => $nmm_crypto->get_id() . '_redux_section',
	    'subsection' => true,
	    'desc'   => '',
	    'icon_type' => 'image',
	    'class' => 'crypto-subsection',
	    'icon'   => $nmm_crypto->get_logo_file_path(),
	    'fields'     => array(
	       array(
                'id'       => $nmm_crypto->get_id() . '_markup',
                'type'     => 'spinner',
                'title'    => 'Markup/Markdown %',                
                'min'      => -99.9,
                'max'      => 100,
                'step'     => 0.1,
                'default'  => 0.0,
                'subtitle' => 'This only increases the crypto amount owed, the original fiat value will still be displayed to the customer.',
                'desc' => 'This will increase/decrease the amount of cryptocurrency the customer will owe for the order.<br>(4.8 = 4.8% markup, -10.0 = 10% markdown)',
            ),
    	    array(
                'id'       => $nmm_crypto->get_id() . '_mode',
                'type'     => 'radio',
                'title'    => 'Mode',
                'ajax_save' => false,
                'options'  => $nmm_cryptoOptions,                
            ),                     
        ),
    );

    if ($nmm_crypto->has_autopay()) {
        $nmm_section['fields'][] = array(
            'id'       => $nmm_crypto->get_id() . '_autopayment_disclaimer',
            'type'     => 'info',
            'style'    => 'warning',            
            'title'    => '<h3 class="autopay-disclaimer-title">Autopay Disclaimer</h3>',
            'desc'     => '<div class="autopay-disclaimer-container">Please note Autopay Mode is still in <strong>beta</strong>. There is no guarantee every order will be processed correctly. If you have any questions contact us at support@nomiddlemancrypto.io.
                            <h3 class="autopay-disclaimer-adjustments">Adjusting the following settings can improve Autopay accuracy</h3>
                            <ul>
                                <li><strong>Wallet Addresses: </strong>Adding more addresses greatly increases autopay reliability while increasing privacy.<br /><i>We suggest having as many addresses as orders you get an hour in that cryptocurrency.</i></li>
                                <li><strong>Order Cancellation Timer: </strong> Reducing this will not only increase autopay reliability but also reduce the effects of volatility.<br /><i>We suggest a value of 1 hour for high throughput stores.</i></li>
                                <li><strong>Auto-Confirm Percentage: </strong> Do not touch this unless you know what you are doing. <br /><i>If you lower this setting you should have one wallet address for the maximum amount of orders you receive in an hour. (If you have a chance to get 30 orders in an hour in this cryptocurrency, we recommend 30 addresses.)</i></li>
                            </ul></div>',
            'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '1'),            
        );        
    }    
    $nmm_section['fields'][] = array(
        'id'       => $nmm_crypto->get_id() . '_addresses',
        'type'     => 'multi_text',
        'default'  => [''],
        'title'    => 'Wallet Addresses',
        'required' => array($nmm_crypto->get_id() . '_mode', 'not', '2'),
    );
    if ($nmm_crypto->has_hd()) {
        $hdOptions = apply_filters('nmm_admin_hd_wallet', $nmm_crypto->get_id());
        if ($hdOptions !== $nmm_crypto->get_id()) {
            $nmm_section['fields'][] = apply_filters('nmm_admin_hd_wallet', $nmm_crypto->get_id());
        }

        $nmm_section['fields'][] = array(
            'id'       => $nmm_crypto->get_id() . '_hd_mpk_sample_addresses',
            'type'     => 'multi_text',
            'title'    => 'Privacy Mode Sample Addresses',                  
            'default'  => [' ',' ',' '],
            'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '2'),            
            'subtitle' => '<span style="color: red;">PLEASE VERIFY YOU CONTROL THESE ADDRESSES BEFORE SAVING OR ELSE LOSS OF FUNDS WILL OCCUR!!!!</span><br /> Addresses will be generated when a valid MPK is input.',
            'desc' => '<span style="color: red;">PLEASE VERIFY YOU OWN THESE ADDRESSES BEFORE SAVING OR ELSE LOSS OF FUNDS WILL OCCUR!!!!</span><br /><br />Once you have entered a valid MPK we will generate these addresses.<br /><br />Due to lack of convention around MPK xpub prefixes, it is not possible to guess which address format an xpub should generate. We currently ONLY GENERATE LEGACY ADDRESSES starting with a "1".',
        );

        $nmm_section['fields'][] = array(
            'id'       => $nmm_crypto->get_id() . '_hd_mpk',
            'type'     => 'textarea',
            'title'    => 'Privacy Mode MPK',
            'default'  => '',
            'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '2'),            
            'desc' => 'Your HD Wallet Master Public Key. We highly recommend using a brand new MPK for each store you run. You run the risk of address reuse and incorrectly processed orders if you use your MPK for multiple stores and/or purposes (such as donations on another platform).',
        );
        
        

        $nmm_section['fields'][] = array(
            'id'       => $nmm_crypto->get_id() . '_hd_percent_to_process',
            'type'     => 'slider',
            'default'  => 1.000,
            'min'      => 0.800,
            'max'      => 1.000,
            'step'     => 0.001,
            'resolution' => 0.001,
            'title'    => 'HD Wallet Auto-Confirm Percentage',
            'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '2'),
            'desc' => 'Privacy Mode will automatically confirm payments that are this percentage of the total amount requested. (1 = 100%), (0.94 = 94%)',
        );
        
        $nmm_section['fields'][] = array(
            'id'       => $nmm_crypto->get_id() . '_hd_required_confirmations',
            'type'     => 'slider',
            'default'  => 2,
            'min'      => 0,
            'max'      => 100,
            'step'     => 1,
            'resolution' => 1,
            'title'    => 'Privacy Mode Required Confirmations',
            'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '2'),
            'desc' => 'This is the number of confirmations a payment needs to receive before it is considered a valid payment.',
        );
        
        $nmm_section['fields'][] = array(
            'id'       => $nmm_crypto->get_id() . '_hd_order_cancellation_time_hr',
            'type'     => 'slider',
            'default'  => 24,
            'min'      => 0.01,
            'max'      => 24 * 7,
            'step'     => 0.01,
            'resolution' => 0.1,
            'title'    => 'Privacy Mode Order Cancellation Timer (hr)',
            'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '2'),
            'desc' => 'This is the amount of time in hours that has to elapse before an order is cancelled automatically. (1.5 = 1 hour 30 minutes)',
        );
    }

    if ($nmm_crypto->has_autopay()) {        
        $nmm_section['fields'][] = array(
            'id'       => $nmm_crypto->get_id() . '_autopayment_percent_to_process',
            'type'     => 'slider',
            'default'  => 0.9999,
            'min'      => 0.9850,
            'max'      => 1.0000,
            'step'     => 0.0001,
            'resolution' => 0.0001,
            'title'    => 'Auto-Confirm Percentage',
            'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '1'),
            'desc' => 'Auto-Payment will automatically confirm payments that are within this percentage of the total amount requested. Contact support@nomiddlemancrypto.io before changing this value.',
        );
        if ($nmm_crypto->needs_confirmations()) {
            $nmm_section['fields'][] = array(
                'id'       => $nmm_crypto->get_id() . '_autopayment_required_confirmations',
                'type'     => 'slider',
                'default'  => 2,
                'min'      => 0,
                'max'      => 100,
                'step'     => 1,
                'resolution' => 1,
                'title'    => 'Required Confirmations',
                'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '1'),
                'desc' => 'This is the number of confirmations a payment needs to receive before it is considered a valid payment.',
            );
        }
        $nmm_section['fields'][] = array(
            'id'       => $nmm_crypto->get_id() . '_autopayment_order_cancellation_time_hr',
            'type'     => 'slider',
            'default'  => 1,
            'min'      => 0.01,
            'max'      => 168.00,
            'step'     => 0.01,
            'resolution' => 0.1,
            'title'    => 'Order Cancellation Timer (hr)',
            'required' => array($nmm_crypto->get_id() . '_mode', 'equals', '1'),
            'desc' => 'This is the amount of time in hours that has to elapse before an order is cancelled automatically. (1.5 = 1 hour 30 minutes)',
        );
    }	

    $nmmSettings = new NMM_Settings(get_option(NMM_REDUX_ID));

    if ($nmmSettings->crypto_selected_and_valid($nmm_crypto->get_id())) {
        $nmm_section['class'] = 'crypto-subsection valid-crypto';
    }
    Redux::setSection(NMM_REDUX_ID, $nmm_section);
    if (!$nmmSettings->crypto_selected($nmm_crypto->get_id())) {
        Redux::hideSection(NMM_REDUX_ID, $nmm_crypto->get_id() . '_redux_section', true);
    }
}

$nmm_section = array(
    'title'  => 'Pricing Options',
    'id'     => 'pricing_section',
    'desc'   => '',
    'fields'   => array(
        array(
            'id' => 'selected_price_apis',
            'type' => 'button_set',
            'default' => array(0),
            'multi' => true,
            'desc' => 'Price is average of APIs selected. At least one must be selected. Adding more can slow down thank you page loading.',
            'title' => 'Selected Pricing Options',
            'options' => array(
                '0' => 'CryptoCompare',
                '1' => 'HitBTC',
                '2' => 'GateIO',
                '3' => 'Bittrex',
                '4' => 'Poloniex',
            ),
        ),
    ),
);

Redux::setSection(NMM_REDUX_ID, $nmm_section);
?>