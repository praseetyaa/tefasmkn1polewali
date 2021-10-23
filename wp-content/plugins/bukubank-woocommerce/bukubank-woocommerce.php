<?php
/*
 * Plugin Name: Bukubank Woocommerce
 * Plugin URI: http://bukubank.com/
 * Description: A WooCommerce Extension that adds extra payment gateway - "Bukubank Payment Gateway"
 * Version: 1.0.5
 * Author: Bukubank.com
 * Author URI: https://bukubank.com/
 * WC requires at least: 3.1.0
 * WC tested up to: 5.5.2
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
**/


include('inc/orderstatus.php');
include('inc/konfirmasi.php');

add_filter( 'woocommerce_cart_calculate_fees', 'woocommerce_bukubank_fee', 10, 1);

function woocommerce_bukubank_fee($cart) {
            global $woocommerce;
            global $wpdb;

            $chosen_gateway = WC()->session->get( 'chosen_payment_method' );
            if ( $chosen_gateway != 'bukubank' ) {
                return;
            }

            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                return;
            }

            if ( $woocommerce->cart->subtotal <= 0) {
                return;
            }

	    $pg = WC()->payment_gateways->get_available_payment_gateways()['bukubank'];
            $operand_kodeunik = $pg->get_option('operand_kodeunik', 'increase');
	    $noakhir = $pg->get_option('noakhir', 999);
	    $range_order = $pg->get_option('range_order', 7);

	    $cart_total = $cart->cart_contents_total + $cart->shipping_total; 
	    $transient = 'kodeunik'.$cart_total.wp_get_session_token();
	 
            $kodeunik = get_transient( $transient );
            if (!$kodeunik)
            {
            	$kodeunik = bukubank_get_uqcodes($cart_total, $operand_kodeunik, $range_order, $noakhir);
	    	set_transient( $transient, $kodeunik, 60*5 ); 
            }        

            if(! is_cart()){
                $woocommerce->cart->add_fee( 'Kode Unik', $kodeunik, true, '' );
            }

}

function bukubank_get_uqcodes($cart_total = 0, $operand_kodeunik = 'decrease', $range_order = 7, $noakhir  = 999) {

    $range_order = 7;
    $loopCount = 0;
    while (empty($kodeunik) && ++$loopCount <= 10) {

    	$kodeunik = mt_rand( 1, $noakhir );
        if( $operand_kodeunik == 'decrease') {
                $kodeunik = (int) ($kodeunik * -1);
        }
	$totalunik = $cart_total + $kodeunik;

        $args = array(
                'post_type'     => 'shop_order',
                'meta_query' => array(
                    array(
                        'key'     => '_order_total',
                        'value'   => (int) $totalunik,
                        'type'    => 'numeric',
                        'compare' => '=',
                    ),
                ),
                'post_status'   => array('wc-on-hold', 'wc-pending', 'wc-awaiting-confirm'),
                'date_query'    => array(
                    array(
                        'column'    =>  'post_date_gmt',
                        'after'    =>  $range_order . ' days ago'
                    )
                )
        );
        $query = new WP_Query( $args );

        if ($query->have_posts())
	{
		$kodeunik = NULL;
		continue;
	}

	return $kodeunik;
    }

    return 1; // Cannot find kodeunik

}


register_activation_hook(dirname(__FILE__)."/bukubank-woocommerce.php", 'bukubank_activation');

function bukubank_activation() {
    if (! wp_next_scheduled ( 'bukubank_hourly_event' )) {
	wp_schedule_event(time(), 'hourly', 'bukubank_hourly_event');
    }
}

register_deactivation_hook(dirname(__FILE__)."/bukubank-woocommerce.php", 'bukubank_deactivation');

function bukubank_deactivation() {
	wp_clear_scheduled_hook('bukubank_hourly_event');
}

add_action('bukubank_hourly_event', 'bukubank_autopending_status_order');

/**
 * Change status order from on-hold to pending
 * @return [type] [description]
 */
function bukubank_autopending_status_order() {

    $payment_gateways = WC()->payment_gateways->get_available_payment_gateways()['bukubank'];
    $change_day = $payment_gateways->get_option('change_day', 'disable');

    /** Check if change day is disable and then skip */
    if ($change_day == 'disable')
        return false;

    $args = array(
        'post_type'     => 'shop_order',
        'post_status'   => array('wc-on-hold'),
        'date_query'    => array(
            array(
                'column'    =>  'post_date_gmt',
                'before'    =>  $change_day . ' days ago'
            )
        )
    );

    /**
     * Query get all order with status on-hold
     * @var WP_Query
     */
    $query = new WP_Query( $args );
    if ($query->have_posts()) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $order = new WC_Order( get_the_ID() );
            $order->add_order_note('Perubahan status On-Hold ke Pending - Bukubank');
            $order->update_status( 'wc-pending' );
        }
    }

    wp_reset_postdata();
}


function wc_bukubank_init() {
    global $woocommerce;														



    if( !isset( $woocommerce ) ) { return; }

    if( !defined( 'ABSPATH' ) ) exit;

    if( !class_exists( 'WC_Bukubank_Gateway' ) ) {        

        include('inc/gateway.php');
    }
}

add_action( 'plugins_loaded', 'wc_bukubank_init' );

function add_bukubank( $methods ) {
    $methods[] = 'WC_Bukubank_Gateway';
    return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'add_bukubank' );

