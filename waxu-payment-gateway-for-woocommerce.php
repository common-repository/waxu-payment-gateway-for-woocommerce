<?php
/*
Plugin Name: WaXu Payment Gateway
Plugin URI:
Description: Paiement en ligne via Mobile Money /Mobile Money Online payments (Go to https://waxu.app)
Author: EXPERTIK LABS
Version: 3.0.0
Author URI: mailto:info@expertic.bj
*/

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	return;
}

define('WC_WAXU_PAYMENT_GATEWAY_ROOT', untrailingslashit(plugin_dir_path(__FILE__)));
define('WC_WAXU_PAYMENT_GATEWAY_BASE_URL', untrailingslashit(plugin_dir_url(__FILE__)));

require_once(WC_WAXU_PAYMENT_GATEWAY_ROOT . '/inc/class-waxu-payment-gateway-for-woocommerce.php');

function wc_waxu_add_to_gateways($gateways) {
	$gateways[] = 'WC_Waxu_Payment_Gateway';
	return $gateways;
}
add_filter('woocommerce_payment_gateways', 'wc_waxu_add_to_gateways');

/*
$plugins_dir = plugin_dir_url( __FILE__ ).'/wp-content/plugins/waxu-plugin';

function themeslug_enqueue_script() {
    wp_enqueue_script( 'waxupluginJS', plugin_dir_url( __FILE__ ).'/js/webpay.js', false );
}

add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_script' );
*/

add_action( 'wp_enqueue_scripts', 'my_plugin_assets' );
function my_plugin_assets() {
    wp_register_script( 'waxu-payment', plugins_url( '/js/webpay.js' , __FILE__ ) );
    wp_enqueue_script( 'waxu-payment' );
}

?>