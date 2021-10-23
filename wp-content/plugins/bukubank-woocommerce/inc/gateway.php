<?php

class WC_Bukubank_Gateway extends WC_Payment_Gateway
{


	/**
	 * Constructor for the gateway.
	 */
	public function __construct()
	{

		$this->id                 = 'bukubank';
		$this->icon               = apply_filters('woocommerce_bukubank_icon', '');
		$this->has_fields         = false;
		$this->method_title       = __('Bukubank payment', 'woocommerce');
		$this->method_description = __('Recevice payment from bukubank.com via BCA, Mandiri, etc', 'woocommerce');

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option('title');
		$this->description  = $this->get_option('description');
		$this->instructions = $this->get_option('instructions');

		// Bukubank account fields shown on the thanks page and in emails.
		$this->account_details = get_option(
			'woocommerce_bukubank_accounts',
			array(
				array(
					'bank_name'      => $this->get_option('bank_name'),
					'account_number' => $this->get_option('account_number'),
					'account_name'   => $this->get_option('account_name'),
				),
			)
		);

		// Actions.
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'save_account_details'));
		add_action('woocommerce_thankyou_bukubank', array($this, 'thankyou_page'));
		add_action('woocommerce_review_order_before_payment',  array($this, 'refresh_checkout_on_payment_methods_change'));

		// You can also register a webhook here
		add_action('woocommerce_api_bukubank', array($this, 'webhook'));

		// Customer Emails.
		add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields()
	{

		$this->form_fields = array(
			'enabled' => array(
				'title' => __('Enable/Disable', 'woocommerce'),
				'type' => 'checkbox',
				'label' => __('Enable Payment Gateway', 'woocommerce'),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __('Title', 'wc_bukubank'),
				'type' => 'text',
				'description' => __('Payment method title which the customer will see during checkout', 'woocommerce'),
				'default' => __('Transfer Bank (Dicek Otomatis)', 'woocommerce'),
				'desc_tip'      => true,
			),
			'description' => array(
				'title' => __('Description', 'wc_bukubank'),
				'type' => 'textarea',
				'description' => __('Payment method description which the customer will see during checkout', 'woocommerce'),
				'default' => __('Pembayaran untuk BCA, Mandiri, BNI dan BRI.', 'woocommerce'),
				'desc_tip'      => true,
			),
			'instructions' => array(
				'title' => __('Instructions', 'wc_bukubank'),
				'type' => 'textarea',
				'description' => __('Instructions that will be added to the thank you page and emails.', 'woocommerce'),
				'default' => __('Pay your order in cash upon delivery.', 'woocommerce'),
				'desc_tip'      => true,
			),
			'account_details' => array(
				'type' => 'account_details',
			),
			'payment_notification_url' => array(
				'title'              => __('Payment Notification URL', 'woocommerce'),
				'type'              => 'text',
				'css'               => 'min-width:420px;',
				'default'             => add_query_arg('wc-api', 'bukubank', get_bloginfo('url') . '/'),
				'description'              => __('Masukan URL ini kedalam pengaturan Payment Notification', 'woocommerce'),
				'custom_attributes' => array(
					'readonly'  => 'readonly'
				)
			),
			'api_key' => array(
				'title' => __('Api Key', 'woocommerce'),
				'type' => 'text',
				'css'      => 'min-width:420px;',
				'description' => __('Masukan Bukubank API Key. Dapatkan key <a href="https://bukubank.com/clientarea/csettings_thirdparty/index" target="_new">disini</a>', 'woocommerce')
			),
			'success_status' => array(
				'title' => __('Status Berhasil', 'woocommerce'),
				'type' => 'select',
				'description' => __('Status setelah berhasil menemukan order yang telah dibayar', 'woocommerce'),
				'default'   =>  'processing',
				'options' => array(
					'completed'     => 'Completed',
					'on-hold'       => 'On Hold',
					'processing'    => 'Processing'
				)
			),
			'range_order' => array(
				'title' => __('Batas lama pengecekkan invoice', 'woocommerce'),
				'type' => 'number',
				'description' => __('Pengecekkan invoice berdasarkan x hari ke belakang (default: 7 hari kebelakang)', 'woocommerce'),
				'default' => 7,
				'custom_attributes' => array(
					'min'  => 1,
					'max'  => 31
				)
			),
			'change_day' => array(
				'title' => __('Perubahan status di hari ke?', 'woocommerce'),
				'type' => 'select',
				'description' => __('Setelah konsumen checkout dan belum bayar, pilih hari ke berapa status order berubah otomatis dari ON-HOLD ke PENDING.', 'woocommerce'),
				'default'   =>  'disable',
				'options' => array(
					'disable'      => 'Tidak Aktif',
					'1'      => 'H+1',
					'2'      => 'H+2',
					'3'      => 'H+3',
					'4'      => 'H+4',
					'5'      => 'H+5'
				)
			),
			'operand_kodeunik' => array(
				'title'     => __('Operasi kode unik', 'woocommerce'),
				'type'      => 'select',
				'default'   =>  'increase',
				'description' => __('Operasi kode unik terhadap harga total harga', 'woocommerce'),
				'options'   => array(
					'increase'      => 'Penambahan Total',
					'decrease'      => 'Pengurangan Total'
				)
			),
			'noakhir' => array(
				'title'     => __('Nilai maksimal kode unik ', 'woocommerce'),
				'type'      => 'select',
				'default'   =>  'increase',
				'description' => __('Nilai maksimal kode unik terhadap harga total harga', 'woocommerce'),
				'options'   => array(
					'99'       => '1 s/d 99',
					'999'      => '1 s/d 999'
				)
			)
		);
	}


	public function refresh_checkout_on_payment_methods_change()
	{
?>
		<script type="text/javascript">
			(function($) {
				$('form.checkout').on('change', 'input[name^="payment_method"]', function() {
					$('body').trigger('update_checkout');
				});
			})(jQuery);
		</script>
	<?php
	}

	/**
	 * Generate account details html.
	 *
	 * @return string
	 */
	public function generate_account_details_html()
	{

		ob_start();

	?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html_e('Account details:', 'woocommerce'); ?></th>
			<td class="forminp" id="bukubank_accounts">
				<div class="wc_input_table_wrapper">
					<table class="widefat wc_input_table sortable" cellspacing="0">
						<thead>
							<tr>
								<th class="sort">&nbsp;</th>
								<th><?php esc_html_e('Bank name', 'woocommerce'); ?></th>
								<th><?php esc_html_e('Account number', 'woocommerce'); ?></th>
								<th><?php esc_html_e('Account name', 'woocommerce'); ?></th>
							</tr>
						</thead>
						<tbody class="accounts">
							<?php
							$i = -1;
							if ($this->account_details) {
								foreach ($this->account_details as $account) {
									$i++;

									echo '<tr class="account">
										<td class="sort"></td>
										<td><input type="text" value="' . esc_attr(wp_unslash($account['bank_name'])) . '" name="bukubank_bank_name[' . esc_attr($i) . ']" /></td>
										<td><input type="text" value="' . esc_attr($account['account_number']) . '" name="bukubank_account_number[' . esc_attr($i) . ']" /></td>
										<td><input type="text" value="' . esc_attr(wp_unslash($account['account_name'])) . '" name="bukubank_account_name[' . esc_attr($i) . ']" /></td>
									</tr>';
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="4"><a href="#" class="add button"><?php esc_html_e('+ Add account', 'woocommerce'); ?></a> <a href="#" class="remove_rows button"><?php esc_html_e('Remove selected account(s)', 'woocommerce'); ?></a></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<script type="text/javascript">
					jQuery(function() {
						jQuery('#bukubank_accounts').on('click', 'a.add', function() {

							var size = jQuery('#bukubank_accounts').find('tbody .account').length;

							jQuery('<tr class="account">\
									<td class="sort"></td>\
									<td><input type="text" name="bukubank_bank_name[' + size + ']" /></td>\
									<td><input type="text" name="bukubank_account_number[' + size + ']" /></td>\
									<td><input type="text" name="bukubank_account_name[' + size + ']" /></td>\
								</tr>').appendTo('#bukubank_accounts table tbody');

							return false;
						});
					});
				</script>
			</td>
		</tr>
<?php
		return ob_get_clean();
	}

	/**
	 * Save account details table.
	 */
	public function save_account_details()
	{

		$accounts = array();

		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- Nonce verification already handled in WC_Admin_Settings::save()
		if (isset($_POST['bukubank_account_name']) && isset($_POST['bukubank_account_number']) && isset($_POST['bukubank_bank_name'])) {

			$bank_names      = wc_clean(wp_unslash($_POST['bukubank_bank_name']));
			$account_numbers = wc_clean(wp_unslash($_POST['bukubank_account_number']));
			$account_names   = wc_clean(wp_unslash($_POST['bukubank_account_name']));

			foreach ($account_names as $i => $name) {
				if (!isset($account_names[$i])) {
					continue;
				}

				$accounts[] = array(
					'account_name'   => $account_names[$i],
					'account_number' => $account_numbers[$i],
					'bank_name'      => $bank_names[$i],
				);
			}
		}
		// phpcs:enable

		update_option('woocommerce_bukubank_accounts', $accounts);
	}

	/**
	 * Output for the order received page.
	 *
	 * @param int $order_id Order ID.
	 */
	public function thankyou_page($order_id)
	{

		if ($this->instructions) {
			echo wp_kses_post(wpautop(wptexturize(wp_kses_post($this->instructions))));
		}
		$this->bank_details($order_id);
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param WC_Order $order Order object.
	 * @param bool     $sent_to_admin Sent to admin.
	 * @param bool     $plain_text Email format: plain text or HTML.
	 */
	public function email_instructions($order, $sent_to_admin, $plain_text = false)
	{

		if (!$sent_to_admin && 'bukubank' === $order->get_payment_method() && $order->has_status('on-hold')) {
			if ($this->instructions) {
				echo wp_kses_post(wpautop(wptexturize($this->instructions)) . PHP_EOL);
			}
			$this->bank_details($order->get_id());
		}
	}

	/**
	 * Get bank details and place into a list format.
	 *
	 * @param int $order_id Order ID.
	 */
	private function bank_details($order_id = '')
	{

		if (empty($this->account_details)) {
			return;
		}

		// Get order and store in $order.
		$order = wc_get_order($order_id);

		$bukubank_accounts = apply_filters('woocommerce_bukubank_accounts', $this->account_details);

		if (!empty($bukubank_accounts)) {
			$account_html = '';
			$has_details  = false;

			foreach ($bukubank_accounts as $bukubank_account) {
				$bukubank_account = (object) $bukubank_account;

				if ($bukubank_account->account_name) {
					$account_html .= '<h3 class="wc-bacs-bank-details-account-name">' . wp_kses_post(wp_unslash($bukubank_account->account_name)) . ':</h3>' . PHP_EOL;
				}

				$account_html .= '<ul class="wc-bacs-bank-details order_details bacs_details">' . PHP_EOL;

				// Bukubank account fields shown on the thanks page and in emails.
				$account_fields = apply_filters(
					'woocommerce_bukubank_account_fields',
					array(
						'bank_name'      => array(
							'label' => __('Bank', 'woocommerce'),
							'value' => $bukubank_account->bank_name,
						),
						'account_number' => array(
							'label' => __('Account number', 'woocommerce'),
							'value' => $bukubank_account->account_number,
						),
					),
					$order_id
				);

				foreach ($account_fields as $field_key => $field) {
					if (!empty($field['value'])) {
						$account_html .= '<li class="' . esc_attr($field_key) . '">' . wp_kses_post($field['label']) . ': <strong>' . wp_kses_post(wptexturize($field['value'])) . '</strong></li>' . PHP_EOL;
						$has_details   = true;
					}
				}

				$account_html .= '</ul>';
			}

			if ($has_details) {
				echo '<section class="woocommerce-bacs-bank-details"><h2 class="wc-bacs-bank-details-heading">' . esc_html__('Our bank details', 'woocommerce') . '</h2>' . wp_kses_post(PHP_EOL . $account_html) . '</section>';
			}
		}
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment($order_id)
	{

		$order = wc_get_order($order_id);

		if ($order->get_total() > 0) {
			// Mark as on-hold (we're awaiting the payment).
			$order->update_status(apply_filters('woocommerce_bukubank_process_payment_order_status', 'on-hold', $order), __('Awaiting Bukubank payment', 'woocommerce'));
		} else {
			$order->payment_complete();
		}

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url($order),
		);
	}


	private function sanitize_array($data = array())
	{
		if (!is_array($data) || !count($data)) {
			return array();
		}

		foreach ($data as $k => $v) {
			if (!is_array($v) && !is_object($v)) {
				$data[$k] = htmlspecialchars(trim($v));
			}
			if (is_array($v)) {
				$data[$k] = $this->sanitize_array($v);
			}
		}

		return $data;
	}


	/*
	 * In case you need a webhook, like PayPal IPN etc
	 */
	public function webhook()
	{


		bukubank_autopending_status_order();

		$api_key = $this->get_option('api_key');
		$incomingApiKey = sanitize_text_field($_SERVER['HTTP_X_API_KEY']);

		// validasi API Key
		if (strcmp($api_key, $incomingApiKey) != 0) {
			exit('Not Authorized');
		}

		$post = file_get_contents("php://input");
		$rows = json_decode($post);

		if (json_last_error() !== JSON_ERROR_NONE) {
			exit("Invalid JSON");
		}

		if (empty($rows)) {
			exit("Empty Record");
		}


		$result = array();
		foreach ((array) $rows as $row) {
			$transid = $row->mutasi_id;
			$totalbayar = $row->amount;
			$mutasi_tipe = $row->type;
			$bankmodule = $row->module;
			$range_order = $this->get_option('range_order', 3);

			if ($mutasi_tipe == 'D') {
				$result[$transid] = "Mutasi Tipe D";
				continue;
			}

			//error_log('Start1');

			$args = array(
				'post_type'     => 'shop_order',
				'meta_query' => array(
					array(
						'key'     => '_order_total',
						'value'   => (int) $totalbayar,
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
			$query = new WP_Query($args);


			if ($query->have_posts()) {

				if ($query->found_posts > 1) {

					/** Send notification to admin */
					$admin_email = get_bloginfo('admin_email');
					$message = sprintf(__('Hai Admin.')) . "\r\n\r\n";
					$message .= sprintf(__('Ada order yang sama, dengan nominal Rp %s'), $totalbayar) . "\r\n\r\n";
					$message .= sprintf(__('Mohon dicek manual.')) . "\r\n\r\n";
					wp_mail($admin_email, sprintf(__('[%s] Ada nominal order yang sama - Bukubank'), get_option('blogname')), $message);
					$result[$transid] = "Email Admin $totalbayar";
					continue;

				} else {
					$results = array();

					while ($query->have_posts()) {
						$query->the_post();
						$order = new WC_Order(get_the_ID());
						if ($order->has_status($this->get_option('success_status', 'processing'))) {
							continue;
						}						
						if ($transid > 0) {						
							$order->add_order_note(sprintf('Pembayaran Mutasi <a href="https://bukubank.com/clientarea/cmutasi/gotomutasi/%d" target="_bukubank">#%d</a> (%s) - Bukubank', $transid, $transid, $bankmodule));
						}	
						$order->update_status($this->get_option('success_status', 'processing'));
						array_push($results, array(
							'order_id'  =>  $order->get_order_number(),
							'status'    =>  $order->get_status(),
						));
					}
					wp_reset_postdata();
					error_log(json_encode($results));
					$result[$transid] = "Success Status"; 
					continue;
				}
			}
			$result[$transid] = "No Query Found"; 
			
		}
		exit(json_encode($result));
	}

}
