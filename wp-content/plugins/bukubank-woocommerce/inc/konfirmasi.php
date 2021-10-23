<?php

// Add a custom metabox
add_action( 'add_meta_boxes_shop_order', 'bukubank_confirm_meta_boxes' );
function bukubank_confirm_meta_boxes( $post ) {
	
    if (!get_post_meta($post->ID, 'bukubank_confirm_meta_key', true))
    {
	return;
    }

    add_meta_box(
	'woocommerce-order-confirm',
	__( 'Konfirmasi Bayar' ),
	'bukubank_confirm_meta_box_content',
	'shop_order',
	'normal',
	'high'
    );

}

// Custom metabox content
function bukubank_confirm_meta_box_content( $post ){


    $metadata = get_post_meta($post->ID, 'bukubank_confirm_meta_key', true);

    echo '<table width="100%" cellpadding="2" cellspacing="0">
	    <tbody>
		<tr>
			<td width="150">Email address</td><td width="150"><a href="mailto:'.esc_html($metadata['email']).'">'.esc_html($metadata['email']).'</a></td>
			<td width="150">Bank Client</td><td width="150">'.esc_html($metadata['clientbank']).'</td>
		</tr>
		<tr>
			<td width="150">Tanggal Transfer</td><td>'.esc_html($metadata['tglbayar']).'</td>
			<td width="150">Nama Pemilik Rekening</td><td width="150">'.esc_html($metadata['accountname']).'</td>
		</tr>
		<tr>
			<td width="150">Jumlah pembayaran</td><td width="150">Rp ' . number_format($metadata['totalbayar'],0,',','.').'</td>
			<td width="150"></td>
		</tr>
		<tr>
			<td width="150">Tujuan Bank Transfer</td><td width="150">'.esc_html($metadata['destbank']).'</td>
			<td width="150"></td>
		</tr>
		<tr>
			<td width="150">Catatan</td><td colspan="3">'.esc_html($metadata['notes']).'</td>
		</tr>
		<tr>
			<td colspan="4">'.sprintf('<img src="%s" width="100" height="100" onClick="swap(this)" onmouseover="" style="cursor: pointer;max-width:100%%;" />', esc_html($metadata['attachment']['url'])).'</td>
		</tr>
	</tbody>
	</table>
	';

    echo '<script>
	 function swap(img)
         {
		if (img.width===100)
		{
		 img.style.height = "auto";
		 img.style.width = "auto";
		 }
		else
		{
		   img.style.height = "100px";
	  	   img.style.width = "100px";
		}
	}
	</script>';

}

// Saving or doing an action when submitting
add_action( 'save_post', 'bukubank_save_meta_box_data' );
function bukubank_save_meta_box_data( $post_id ){

    // Only for shop order
    if ( 'shop_order' != $_POST[ 'post_type' ] )
        return $post_id;

    // Check if our nonce is set (and our cutom field)
    if ( ! isset( $_POST[ 'trusted_list_nonce' ] ) && isset( $_POST['submit_trusted_list'] ) )
        return $post_id;

    $nonce = sanitize_key( $_POST[ 'trusted_list_nonce' ]);

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce ) )
        return $post_id;

    // Checking that is not an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // Check the user’s permissions (for 'shop_manager' and 'administrator' user roles)
    if ( ! current_user_can( 'edit_shop_order', $post_id ) && ! current_user_can( 'edit_shop_orders', $post_id ) )
        return $post_id;

    // Action to make or (saving data)
    if( isset( $_POST['submit_trusted_list'] ) ) {
        $order = wc_get_order( $post_id );
        // $customeremail = $order->get_billing_email();
        //$order->add_order_note(sprintf("test2"));
    }
}



function bukubank_registration_form( $orderid, $email, $totalbayar, $tglbayar, $destbank, $clientbank, $accountname, $notes ) {


        $select = '';
        if ($account_detail = get_option( 'woocommerce_bukubank_accounts'))
        {

                $select = '<select name="destbank" class="input-text">';
                foreach($account_detail as $account)
                {
                        $value = sprintf('%s #%s a/n %s', strtoupper($account['bank_name']), $account['account_number'], $account['account_name']);
                        $select .= '<option value="'.$value.'">'.$value.'</option>';
                }
                $select .= '</select>';
        }



        echo '
            <style>
            input, select{
                margin-bottom:20px;
	        width:100%;
            }
            </style>

            <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" enctype="multipart/form-data" autocomplete="off">
            <div>
            <label for="orderid">Nomor Order <abbr class="required" title="required">*</abbr></label><br/>
            <input type="number" id="orderid" name="orderid" class="input-text qty text" value="' . ( isset( $_POST['orderid'] ) ? esc_html($orderid) : '' ) . '" placeholder="639">
            </div>
             
            <div>
            <label for="email">Email <abbr class="required" title="required">*</abbr></label><br/>
            <input type="email" name="email" class="input-text" value="' . ( isset( $_POST['email']) ? esc_html($email) : '' ) . '" placeholder="foo@gmail.com">
            </div>
             
            <div>
            <label for="totalbayar">Jumlah pembayaran <abbr class="required" title="required">*</abbr></label><br/>
            <input type="number" id="totalbayar" name="totalbayar" class="input-text qty text" size="80" value="' . ( isset( $_POST['totalbayar']) ? esc_html($totalbayar) : '' ) . '" placeholder="10000">
            </div>
             
            <div>
            <label for="tglbayar">Tanggal Transfer <abbr class="required" title="required">*</abbr></label><br/>
            <input type="date" name="tglbayar" id="tglbayar" class="input-text" size="80" value="' . ( isset( $_POST['tglbayar']) ? esc_html($tglbayar) : date('Y-m-d') ) . '">
            </div>
             
            <div>
            <label for="destbank">Tujuan Bank Transfer <abbr class="required" title="required">*</abbr></label><br/>'.$select.'
            </div>
             
            <div>
            <label for="clientbank">Bank Anda <abbr class="required" title="required">*</abbr></label><br/>
            <input type="text" id="clientbank" name="clientbank" class="input-text" size="80" value="' . ( isset( $_POST['clientbank']) ? esc_html($clientbank) : '' ) . '" placeholder="Mandiri">
            </div>

            <div>
            <label for="accountname">Nama Rekening Bank Anda <abbr class="required" title="required">*</abbr></label><br/>
            <input type="text" id="accountname" name="accountname" class="input-text" size="80" value="' . ( isset( $_POST['accountname']) ? esc_html($accountname) : '' ) . '" placeholder="Budi Santoso">
            </div>
             
            <div>
	         <label> Slip Pembayaran <abbr class="required" title="required">*</abbr></label><br/>
	         <input type="file" name="attachmentFile" > 
            </div>
            <div>
            <label for="bio">Catatan Tambahan (optional)</label><br/>
            <textarea name="notes">' . ( isset( $_POST['notes']) ? esc_html($notes) : null ) . '</textarea>
            </div>
            <br/>
            <input type="submit" name="submit" style="width:auto;" value="Submit"/>
            </form>
        ';
}

function bukubank_validate_form( $orderid, $email, $totalbayar, $tglbayar, $destbank, $clientbank, $accountname, $notes )  {

        global $reg_errors;

        $reg_errors = new WP_Error;

	if (empty( $orderid ))
	{
		$reg_errors->add('field', '<strong>Nomor Order</strong> is missing');
	} else {
		if ((($order = wc_get_order( $orderid)) === FALSE) || ($order->status != 'on-hold')) 
		{
		    $reg_errors->add( 'orderid', '<strong>Nomor Order</strong> is not valid' );
		}
	}

        if ( empty( $email ))
	{
		$reg_errors->add('field', '<strong>Email</strong> is missing');
	} else {
		if ( !is_email( $email ) ) {
		    $reg_errors->add( 'email_invalid', '<strong>Email</strong> is not valid' );
		}
	}

        if ( empty( $totalbayar ))
	{
		$reg_errors->add('field', '<strong>Jumlah pembayaran</strong> is missing');
	}

        if ( empty( $tglbayar ))
	{
		$reg_errors->add('field', '<strong>Tanggal Transfer</strong> is missing');
	}

        if ( empty( $clientbank ))
	{
		$reg_errors->add('field', '<strong>Bank Anda</strong> is missing');
	}

        if ( empty( $accountname ))
	{
		$reg_errors->add('field', '<strong>Nama Rekening Bank Anda</strong> is missing');
	}

	if ($_FILES['attachmentFile']['size'] == 0)
	{
	    $reg_errors->add('attachmentfile', '<strong>Slip Pembayaran</strong> is missing');
	}


        if ( is_wp_error( $reg_errors ) && ! empty( $reg_errors->errors ) ) {
	    echo '<ul class="woocommerce-error" role="alert">';
            foreach ( $reg_errors->get_error_messages() as $error ) {             
		echo "<li>$error</li>";                 
            }
	    echo '</ul>';
         
        }

}

function bukubank_confirm_upload_user_file( $file = array() , $parent_post_id = 0) {

      require_once( ABSPATH . 'wp-admin/includes/admin.php' );
      $file_return = wp_handle_upload( $file, array('test_form' => false ) );
      if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
          return false;
      } else {
          $filename = $file_return['file'];
          $attachment = array(
              'post_mime_type' => $file_return['type'],
              'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
              'post_content' => '',
              'post_status' => 'inherit',
              'guid' => $file_return['url']
          );
          $attachment_id = wp_insert_attachment( $attachment, $file_return['url'], $parent_post_id );
          require_once(ABSPATH . 'wp-admin/includes/image.php');
          $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
          wp_update_attachment_metadata( $attachment_id, $attachment_data );

          if( 0 < intval( $attachment_id ) ) {
          	return $attachment_id;
          }
      }
      return false;
}

function bukubank_complete_registration($orderid, $email, $totalbayar, $tglbayar, $destbank, $clientbank, $accountname, $notes) {
    global $reg_errors;

/*
    global $reg_errors, $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'user_url'      =>   $website,
        'first_name'    =>   $first_name,
        'last_name'     =>   $last_name,
        'nickname'      =>   $nickname,
        'description'   =>   $bio,
        );
        $user = wp_insert_user( $userdata );
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
	}
*/



	if ( 1 > count( $reg_errors->get_error_messages() ) ) {


		if ( ! function_exists( 'wp_handle_upload' ) ) {
 			require_once( ABSPATH . 'wp-admin/includes/file.php' );
 		}

 		$uploadedfile = $_FILES['attachmentFile'];

 		$upload_overrides = array( 'test_form' => false );

 		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

		
 		if ( $movefile && ! isset( $movefile['error'] ) ) { 
			
			$order = wc_get_order( $orderid );
			$userdata = array( 
				'email'=>$email,
				'totalbayar'=>$totalbayar,
				'tglbayar'=>$tglbayar,
				'destbank'=>$destbank,
				'clientbank'=>$clientbank,
				'accountname'=>$accountname,
				'notes'=>$notes,
				'attachment'=>$movefile
			);
						
			$order->update_meta_data( 'bukubank_confirm_meta_key', $userdata );
		        $order->add_order_note('Perubahan status ke Waiting Confirmation - Bukubank');
	                $order->update_status( 'wc-awaiting-confirm' ); 
			$order->save();

		    	echo '<ul class="woocommerce-info" role="alert">';
			echo '<li>Konfirmasi pembayaran berhasil.</li>';  
		    	echo '</ul>';
		}
		 
	}
}

function bukubank_custom_registration_shortcode() {
    // sanitize user form input
    global $orderid, $email, $totalbayar, $tglbayar, $destbank, $clientbank, $accountname, $notes;


    $orderid        =   sanitize_text_field( $_POST['orderid'] );
    $email          =   sanitize_email( $_POST['email'] );
    $totalbayar     =   sanitize_text_field( $_POST['totalbayar'] );
    $tglbayar       =   sanitize_text_field( $_POST['tglbayar'] );
    $destbank       =   sanitize_text_field( $_POST['destbank'] );
    $clientbank     =   sanitize_text_field( $_POST['clientbank'] );
    $accountname    =   sanitize_text_field( $_POST['accountname'] );
    $notes          =   esc_textarea( $_POST['notes'] );

    if ( isset($_POST['submit'] ) ) {
        bukubank_validate_form(
                $orderid,
                $email,
                $totalbayar,
                $tglbayar,
                $destbank,
                $clientbank,
                $accountname,
                $notes
        );
         
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        bukubank_complete_registration($orderid, $email, $totalbayar, $tglbayar, $destbank, $clientbank, $accountname, $notes);
    }
 

    bukubank_registration_form($orderid, $email, $totalbayar, $tglbayar, $destbank, $clientbank, $accountname, $notes);

}


// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'bukubank_custom_registration_shortcode' );
 
// The callback function that will replace [cr_custom_registration]
function custom_registration_shortcode() {
    ob_start();
    bukubank_custom_registration_function();
    return ob_get_clean();
}
