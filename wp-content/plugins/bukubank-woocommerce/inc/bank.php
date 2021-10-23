<?php  // only copy if needed!

function bukubank_get_bank($api_key = '')
{

        if (!$api_key) return FALSE;

        $url = 'https://www.bukubank.com/api/v1/bank';

        $args = array(
                'method' => 'GET',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'sslverify' => false,
                'blocking' => true,
                'headers' => array(
                    'Authorization' => 'Bearer '.$api_key,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ),
                'cookies' => array()
        );

        /*
         * Your API interaction could be built with wp_remote_post()
 	 */
         $response = wp_remote_post( $url, $args );
 
         if( !is_wp_error( $response ) ) {
 
	         $body = json_decode( $response['body'], true );
                
	         // it could be different depending on your payment processor
	         if ( !empty($body) && is_array($body) ) {

                        return $body;
 
	         } else {
		        return FALSE;
	        }
 
        } else {
	        return FALSE;
        }

}


