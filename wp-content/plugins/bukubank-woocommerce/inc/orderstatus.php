<?php  // only copy if needed!
/** 
 * Register new status
**/
function bukubank_register_awaiting_confirm_order_status() {
    register_post_status( 'wc-awaiting-confirm', array(
        'label'                     => 'Waiting Confirmation',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Waiting Confirmation <span class="count">(%s)</span>', 'Waiting Confirmation <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'bukubank_register_awaiting_confirm_order_status' );

// Add to list of WC Order statuses
function bukubank_add_awaiting_confirm_to_order_statuses( $order_statuses ) {
    $new_order_statuses = array();
    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {
        $new_order_statuses[ $key ] = $status;
        if ( 'wc-on-hold' === $key ) {
            $new_order_statuses['wc-awaiting-confirm'] = 'Waiting Confirmation';
        }
    }
    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'bukubank_add_awaiting_confirm_to_order_statuses' );


