<?php
/*
WC requires at least: 3.0.0
WC tested up to: 3.6.4
Plugin Name: Nomiddleman Bitcoin and Crypto Payments for WooCommerce
Plugin URI:  https://wordpress.org/plugins/nomiddleman-crypto-payments-for-woocommerce/
Description: WooCommerce Bitcoin and Cryptocurrency Payment Gateway
Author: nomiddleman
Author URI: https://nomiddlemancrypto.io

Version: 2.4.1
Copyright: © 2019 Nomiddleman Crypto (email : support@nomiddlemancrypto.io)
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

*/

add_action('plugins_loaded', 'NMM_init_gateways');
register_activation_hook(__FILE__, 'NMM_activate');
register_deactivation_hook(__FILE__, 'NMM_deactivate');
register_uninstall_hook(__FILE__, 'NMM_uninstall');
define('NMM_HD_TABLE', 'nmmpro_hd_addresses');
define('NMM_PAYMENT_TABLE', 'nmmpro_payments');  
define('NMM_CAROUSEL_TABLE', 'nmmpro_carousel');
define('NMM_LOGFILE_NAME', 'nmm.log');
define('NMM_REDUX_ID', 'nmmpro_redux_options');
define('NMM_EXTENSION_KEY', 'nmm_registered_extensions');

require_once(plugin_basename('src/NMM_Settings.php'));

function NMM_init_gateways(){

    if (!class_exists('WC_Payment_Gateway')) {
        return;
    };

    define('NMM_PLUGIN_DIR', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));    
    define('NMM_PLUGIN_FILE', __FILE__);
    define('NMM_ABS_PATH', dirname(NMM_PLUGIN_FILE));
    define('NMM_PLUGIN_BASENAME', plugin_basename(NMM_PLUGIN_FILE));

    define('NMM_CRON_JOB_URL', plugins_url('', __FILE__) . '/src/NMM_Cron.php');
    define('NMM_VERSION', '2.4.1');
    
    define('NMM_REDUX_SLUG', 'nmmpro_options');

    if ( !class_exists( 'ReduxFramework' ) && file_exists( NMM_ABS_PATH . '/src/vendor/ReduxFramework/ReduxCore/framework.php' ) ){
        require_once( NMM_ABS_PATH . '/src/vendor/ReduxFramework/ReduxCore/framework.php' );
    }  
    
    // Vendor
    if (!class_exists('bcmath_Utils')) {
        require_once(plugin_basename('src/vendor/bcmath_Utils.php'));
    }
    if (!class_exists('CurveFp')) {
        require_once(plugin_basename('src/vendor/CurveFp.php'));
    }
    if (!class_exists('HdHelper')) {
        require_once(plugin_basename('src/vendor/HdHelper.php'));
    }
    if (!class_exists('gmp_Utils')) {
        require_once(plugin_basename('src/vendor/gmp_Utils.php'));
    }
    if (!class_exists('NumberTheory')) {
        require_once(plugin_basename('src/vendor/NumberTheory.php'));
    }
    if (!class_exists('Point')) {
        require_once(plugin_basename('src/vendor/Point.php'));
    }
    if (!class_exists('\CashAddress\CashAddress')) {
        require_once(plugin_basename('src/vendor/CashAddress.php'));
    }

    // Http
    require_once(plugin_basename('src/NMM_Exchange.php'));
    require_once(plugin_basename('src/NMM_Blockchain.php'));

    // Database
    require_once(plugin_basename('src/NMM_Carousel_Repo.php'));
    require_once(plugin_basename('src/NMM_Hd_Repo.php'));
    require_once(plugin_basename('src/NMM_Payment_Repo.php'));

    // Simple Objects
    require_once(plugin_basename('src/NMM_Cryptocurrency.php'));
    require_once(plugin_basename('src/NMM_Transaction.php'));
    
    // Business Logic
    require_once(plugin_basename('src/NMM_Cryptocurrencies.php'));
    require_once(plugin_basename('src/NMM_Carousel.php'));
    require_once(plugin_basename('src/NMM_Hd.php'));    
    require_once(plugin_basename('src/NMM_Payment.php'));

    // Misc
    require_once(plugin_basename('src/NMM_Util.php'));
    require_once(plugin_basename('src/NMM_Hooks.php'));
    require_once(plugin_basename('src/NMM_Cron.php'));
    require_once(plugin_basename('src/NMM_Admin.php'));
    require_once(plugin_basename('src/NMM_Settings.php'));
    
    require_once(plugin_basename('src/NMM_Validation.php'));

    // Core
    require_once(plugin_basename('src/NMM_Gateway.php'));
    
    add_filter ('cron_schedules', 'NMM_add_interval');

    add_action('NMM_cron_hook', 'NMM_do_cron_job');
    add_action( 'woocommerce_process_shop_order_meta', 'NMM_update_database_when_admin_changes_order_status', 10, 2 );     
    
    add_action('redux/page/' . NMM_REDUX_ID . '/load', 'NMM_load_redux_css');
    add_filter('redux/validate/' . NMM_REDUX_ID . '/before_validation', array('NMM_Validation', 'validate_redux_options'), 10, 2);    
    
    add_action( 'admin_notices', 'NMM_display_flash_notices', 12 );    

    if (is_admin()) {
        add_action( 'admin_enqueue_scripts', 'NMM_load_js' );
        add_action( 'wp_ajax_firstmpkaddress', 'NMM_first_mpk_address_ajax');
    }    

    NMM_Register_Extensions();

    if (!wp_next_scheduled('NMM_cron_hook')) {
        wp_schedule_event(time(), 'seconds_30', 'NMM_cron_hook');
    }    
}

function NMM_add_interval ($schedules)
{
    $schedules['seconds_5'] = array('interval'=>5, 'display'=>'debug');
    $schedules['seconds_30'] = array('interval'=>30, 'display'=>'Bi-minutely');
    $schedules['minutes_1'] = array('interval'=>60, 'display'=>'Once every 1 minute');
    $schedules['minutes_2'] = array('interval'=>120, 'display'=>'Once every 2 minutes');

    return $schedules;
}

function NMM_activate() {
    if (!wp_next_scheduled('NMM_cron_hook')) {
        wp_schedule_event(time(), 'seconds_30', 'NMM_cron_hook');
    }
    
    NMM_create_hd_mpk_address_table();
    NMM_create_payment_table();
    NMM_create_carousel_table();    
}

function NMM_deactivate() {
    wp_clear_scheduled_hook('NMM_cron_hook');    
}

function NMM_uninstall() {
    NMM_drop_mpk_address_table();
    NMM_drop_payment_table();
    NMM_drop_carousel_table();
}

function NMM_drop_mpk_address_table() {
    global $wpdb;
    $tableName = $wpdb->prefix . NMM_HD_TABLE;
    
    $query = "DROP TABLE IF EXISTS `$tableName`";
    $wpdb->query($query);
}

function NMM_drop_payment_table() {
    global $wpdb;    
    $tableName = $wpdb->prefix . NMM_PAYMENT_TABLE;    
    
    $query = "DROP TABLE IF EXISTS `$tableName`";
    $wpdb->query($query);
}

function NMM_drop_carousel_table() {
    global $wpdb;    
    $tableName = $wpdb->prefix . NMM_CAROUSEL_TABLE;    
    
    $query = "DROP TABLE IF EXISTS `$tableName`";
    $wpdb->query($query);
}

function NMM_create_hd_mpk_address_table() {
    global $wpdb;
    $tableName = $wpdb->prefix . NMM_HD_TABLE;    
    
    $query = "CREATE TABLE IF NOT EXISTS `$tableName` 
        (
            `id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
            `mpk` char(150) NOT NULL,
            `mpk_index` bigint(20) NOT NULL DEFAULT '0',
            `address` char(199) NOT NULL,
            `cryptocurrency` char(7) NOT NULL,
            `status` char(24)  NOT NULL DEFAULT 'error',
            `total_received` decimal( 16, 8 ) NOT NULL DEFAULT '0.00000000',
            `last_checked` bigint(20) NOT NULL DEFAULT '0',
            `assigned_at` bigint(20) NOT NULL DEFAULT '0',
            `order_id` bigint(10) NULL,            
            `order_amount` decimal(16, 8) NOT NULL DEFAULT '0.00000000',
            `all_order_ids` text NULL,
    
            PRIMARY KEY (`id`),
            UNIQUE KEY `hd_address` (`cryptocurrency`, `address`),
            KEY `status` (`status`),
            KEY `mpk_index` (`mpk_index`),
            KEY `mpk` (`mpk`)
        );";

    $wpdb->query($query);
}

function NMM_create_payment_table() {
    global $wpdb;
    $tableName = $wpdb->prefix . NMM_PAYMENT_TABLE;    
    
    $query = "CREATE TABLE IF NOT EXISTS `$tableName`
        (
            `id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
            `address` char(199) NOT NULL,
            `cryptocurrency` char(7) NOT NULL,
            `status` char(24)  NOT NULL DEFAULT 'error',
            `ordered_at` bigint(20) NOT NULL DEFAULT '0',
            `order_id` bigint(10) NOT NULL DEFAULT '0',
            `order_amount` decimal(32, 18) NOT NULL DEFAULT '0.000000000000000000',
            `tx_hash` char(255) NULL,
            `hd_address` tinyint(4) NOT NULL DEFAULT '0',

    
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_payment` (`order_id`, `order_amount`),
            KEY `status` (`status`)
        );";

    $wpdb->query($query);
}

function NMM_create_carousel_table() {
    global $wpdb;
    $tableName = $wpdb->prefix . NMM_CAROUSEL_TABLE;    

    $query = "CREATE TABLE IF NOT EXISTS `$tableName`
        (
            `id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
            `cryptocurrency` char(12) NOT NULL,
            `current_index` bigint(20) NOT NULL DEFAULT '0',
            `buffer` text NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `cryptocurrency` (`cryptocurrency`)
        );";

    $wpdb->query($query);

    require_once( plugin_basename( 'src/NMM_Cryptocurrency.php' ) );
    require_once( plugin_basename( 'src/NMM_Carousel_Repo.php' ) );
    require_once( plugin_basename( 'src/NMM_Util.php' ) );
    require_once( plugin_basename( 'src/NMM_Cryptocurrencies.php' ) );
    
    NMM_Carousel_Repo::init();

    $cryptos = NMM_Cryptocurrencies::get();

    $reduxOptions = get_option(NMM_REDUX_ID, array());

    if (!empty($reduxOptions)) {
        $nmmSettings = new NMM_Settings($reduxOptions);

        foreach ($cryptos as $crypto) {
            $addresses = $nmmSettings->get_addresses($crypto->get_id());
            if (!empty($addresses)) {
                $carouselRepo = new NMM_Carousel_Repo();
                $carouselRepo->set_buffer($crypto->get_id(), $addresses);
            }
        }
    }
}

function NMM_Register_Extensions() {    
    $extensionsDir = NMM_ABS_PATH . '/src/extensions/';
    $extensions = scandir($extensionsDir);
    $extensionsToLoad = [];
    if (!is_array($extensions)) {
        return;
    }
    foreach ($extensions as $extension) {
        if ( $extension === '.' || $extension === '..' || ! is_dir( $extensionsDir . $extension ) || substr( $extension, 0, 1 ) === '.' || substr( $extension, 0, 1 ) === '@' ) {
            continue;
        }

        $extensionsToLoad[] = $extension;
        @include_once(plugin_basename('src/extensions/' . $extension . '/NMM_' . ucfirst($extension) . '.php'));
    }

    update_option(NMM_EXTENSION_KEY, $extensionsToLoad);
}

add_filter('woocommerce_payment_gateways', 'NMM_filter_gateways');

?>