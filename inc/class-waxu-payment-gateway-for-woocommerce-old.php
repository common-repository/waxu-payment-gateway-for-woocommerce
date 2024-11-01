<?php




/**
 * @param WP_REST_Request $request
 *
 * @return array
 */

function rest_track_waxu_order_status_callback($request) {
    $order_id = $request->get_param('id');
    $order = wc_get_order($order_id);
    $order_status = $order->get_status();
    if ($order_status == 'pending') {
        /**
         * @var WC_Waxu_Payment_Gateway $waxu_gateway
         */
        $waxu_gateway = WC_Payment_Gateways::instance()->payment_gateways()['waxu'];
        $order_status = $waxu_gateway->get_pay_status($order);
    }
    return array('status' => $order_status, 'text' => $order_status);
}

/**
 * @param WP_REST_Request $request
 *
 * @return bool
 */
function rest_track_waxu_order_status_permission_callback($request) {
    $order_id = $request->get_param('id');
    $order = wc_get_order($order_id);

    // not an order
    if (!$order) {
        return false;
    }

    // logged in users
    if (is_user_logged_in()) {
        $user = wp_get_current_user();

        // owner or administrators
        if ($order->get_customer_id() == $user->ID || current_user_can('administrator')) {
            return true;
        }
    }

    // guest - not a waxu payment order
    if ($order->get_payment_method() == 'waxu') {
        return true;
    }

    return false;
}

function rest_track_waxu_order_status() {
    register_rest_route('wc-waxu/v1', '/trackOrderStatus/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'rest_track_waxu_order_status_callback',
        'permission_callback' => 'rest_track_waxu_order_status_permission_callback',
    ));
}

add_action('rest_api_init', 'rest_track_waxu_order_status');

/**
 * track the order status
 * @param int $order_id
 */
function waxu_track_order_status() {
    if (!is_wc_endpoint_url('view-order')) {
        return;
    }

    $order_id = get_query_var('view-order');
    $order = wc_get_order($order_id);
    if (!$order || $order->get_payment_method() != 'waxu' || !$order->has_status('pending')) {
        return;
    }
    ?>
    <script type="template/html" id="waxu-error">
        <div class="waxu-notice">
            <div class="waxu-notice__inner">
                <img class="waxu-notice__logo" src="<?php echo WC_WAXU_PAYMENT_GATEWAY_BASE_URL; ?>/images/ww.png">
                <img class="waxu-notice__image" src="<?php echo WC_WAXU_PAYMENT_GATEWAY_BASE_URL; ?>/images/error.png">
                <strong>Ooops!!!</strong>
                <p>Paiement annulé ou expiré.</p>
                <a href="#" class="waxu-notice__btn-close">OK</a>
            </div>
        </div>
    </script>
    <script type="template/html" id="waxu-success">
        <div class="waxu-notice">
            <div class="waxu-notice__inner">
                <img class="waxu-notice__logo" src="<?php echo WC_WAXU_PAYMENT_GATEWAY_BASE_URL; ?>/images/ww.png">
                <img class="waxu-notice__image waxu-notice__image-success" src="<?php echo WC_WAXU_PAYMENT_GATEWAY_BASE_URL; ?>/images/success.png">
                <p>Paiement completé.</p>
                <a href="#" class="waxu-notice__btn-close">OK</a>
            </div>
        </div>
    </script>
    <script defer async="async">
      (function (window, document, $) {
        'use strict';

        var endpoint = '<?php echo home_url('/wp-json/wc-waxu/v1/trackOrderStatus/' . $order_id); ?>';
        var isLoadingStatus = false;
        var waxuErrorHtml = $('#waxu-error').html();
        var waxuSuccessHtml = $('#waxu-success').html();
		
		var url = '<?=$_SESSION['webpayUrl'];?>';
        var modal_is_Opened = false;
		
		if (modal_is_Opened == false){
			openwaxuURL(url);
			modal_is_Opened = true;
		}
			

        if ($('.order-status').length < 1) {
          $('.woocommerce-form-login').before('<div class="order-status" style="text-align: center;">Paiement WaXu en attente &nbsp;&nbsp;</div>');
        }

        $('.order-status').append('<img src="<?php echo admin_url('/images/loading.gif'); ?>" style="display: inline;">');
        fetchOrderStatus();
		
        const loadStatusInterval = setInterval(function () {
          // console.log({isLoadingStatus});
          if (isLoadingStatus) {
            return;
          }
          isLoadingStatus = true;
          fetchOrderStatus();
        }, 5000);
		
		/*var timer = setInterval(function() { 
		if(typeof win !== 'undefined'){
			if(win.closed) {
				clearInterval(timer);
				
		
			}		
		}
			
		}, 5000);*/

        function fetchOrderStatus() {
          $.ajax({
            method: 'GET',
            // beforeSend: function (xhr) {
            //   xhr.setRequestHeader('X-WP-Nonce', restNonce);
            // },
            url: endpoint,
            data: {},
            success: function (response) {
				// console.log(response);//NABIL	
				
				var url = '<?=$_SESSION['webpayUrl'];?>';
				
					// console.log("undefined = " + url);
					// Fixes dual-screen position Most browsers Firefox
					/*var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
					var dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

					var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
					var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

					var systemZoom = width / window.screen.availWidth;
					var left = (width - 450) / 2 ;
					var top = (height - 800) / 2 ;	
					
					var win = window.open(url,'winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=450,height=800' + ', top=' + top + ', left=' + left);*/
					//firstCall=false;
					//openwaxuURL(url);
				
				
				
					
			  
              if (response.status !== 'pending') {
                clearInterval(loadStatusInterval);
                $('.order-status').html('');
                // alert('Your payment is ' + response.status);
                // console.log(response.status, response.status === 'completed');
                if (response.status === 'completed') {
                    
                  $('body').append(waxuSuccessHtml);
                } else {
                  $('body').append(waxuErrorHtml.replace('{{order_status}}', response.text));
                }
              } /*else {
				  var infoStatus = 'Paiement WaXu en attente...';
                $('.order-status').html(infoStatus + '<img src="<?php echo admin_url('/images/loading.gif'); ?>" style="display: inline;">');
				
              }*/
            },
            error: function (error) {
              // console.log(error);
              window.location.reload();
            },
            complete: function () {
              isLoadingStatus = false;
            }
          });
        }

        $(document).on('click', '.waxu-notice__btn-close', function () {
            closewaxu();
          $('.waxu-notice').remove();
        });
      })(window, document, jQuery);
    </script>
    <style type="text/css">
        .waxu-notice {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999999;
            background-color: rgba(0, 0, 0, 0.2);
            width: 100%;
            height: 100%;
        }

        .waxu-notice__inner {
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.5);
            background-color: #fff;
            width: 300px;
            max-width: 90%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .waxu-notice__inner * {
            width: 100%;
            margin: 0;
        }

        .waxu-notice__logo {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 70px;
        }

        .waxu-notice__image {
            width: 100px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .waxu-notice__image-success {
            margin-bottom: 15px;
        }

        .waxu-notice strong {
            font-size: 26px;
        }

        .waxu-notice .waxu-notice__btn-close {
            display: inline-block;
            background-color: #00a0d2;
            color: #fff;
            padding: 5px 35px;
            width: auto;
            margin-top: 15px;
            position: relative;
        }

        .waxu-notice .waxu-notice__btn-close:hover:after {
            content: '';
            display: block;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.2);
        }
    </style>
    <?php
}

add_action('wp_footer', 'waxu_track_order_status');

function wc_waxu_payment_gateway() {
    class WC_Waxu_Payment_Gateway extends WC_Payment_Gateway {
        private $api_key = '';

        /**
         * Constructor for the gateway.
         */
        public function __construct() {
            $this->id = 'waxu';
            $this->icon = WC_WAXU_PAYMENT_GATEWAY_BASE_URL . '/images/waxu_wc.png';//NABIL
            $this->has_fields = true;
            $this->method_title = 'WaXu';
            $this->method_description = 'Allow customers to conveniently checkout from WaXu App.' . '<br />' . 'Facilite les achats en ligne via notre appli WaXu.';
            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();
            // Define user set variables.
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->instructions = $this->get_option('instructions');
            $this->country_codes = $this->get_option('country_codes');
            $this->api_base_url = $this->get_option('api_base_url');
            $this->api_service = $this->get_option('api_service');
            $this->api_key = $this->get_option('api_key');
			$this->web_pay_url = 'https://waxu.app/pay';
            // Actions.
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_thankyou_waxu', array($this, 'thankyou_page'));
            add_action('woocommerce_checkout_create_order', array($this, 'save_order_payment_gateway_details'), 10, 2);
        }

        /**
         * @param WC_Order $order
         * @param array $data
         */
        public function save_order_payment_gateway_details($order, $data) {
//			var_dump($_POST); die;
            if ($data['payment_method'] == 'waxu') {
//				$order->save_meta_data('_waxu_country_code', sanitize_text_field($_POST['waxu_country_code']));
//				$order->save_meta_data('_waxu_phone', sanitize_text_field($_POST['waxu_phone']));
                
				//NABIL
				//$order->add_meta_data('_waxu_country_code', sanitize_textarea_field($_POST['waxu_country_code']));
                //$order->add_meta_data('_waxu_phone', sanitize_text_field($_POST['waxu_phone']));
            }
        }

        /**
         * Validate frontend fields.
         *
         * Validate payment fields on the frontend.
         *
         * @return bool
         */
        public function validate_fields() {
            // $waxu_country_code = esc_attr($_POST['waxu_country_code']);
			//NABIL
            /*$waxu_phone = sanitize_text_field($_POST['waxu_phone']);
            $waxu_phone = trim($waxu_phone);
            $waxu_phone = str_replace(' ', '', $waxu_phone);
            $is_phone_containing_numbers_only = preg_match('@^\d+$@', $waxu_phone);
            if (strlen($waxu_phone) < 8) {
                wc_add_notice('Phone number should contain at least 8 digits.', 'error');
                return false;
            }
            if (!$is_phone_containing_numbers_only) {
                wc_add_notice('Phone number should contain digits only.', 'error');
                return false;
            }*/
            return true;
        }

        private function get_country_code_options() {
            $country_codes = preg_split('/\r\n|[\r\n]/', $this->country_codes);
            // var_dump($country_codes);
            $options = array();
            foreach ($country_codes as $string) {
                $string_parts = explode(' ', $string);
                // var_dump($string_parts);
                // $options[$string_parts[1]] = $string_parts[0];
                $code = array_pop($string_parts);
                $options[$code] = implode(' ', $string_parts);
            }
            return $options;
        }

        /**
         * Output the "payment type" fields in checkout.
         */
        public function payment_fields() {
            if ($description = $this->get_description()) {
                echo wpautop(wptexturize($description));
            }
            // $this->get_country_code_options();
            /*woocommerce_form_field('waxu_country_code', array(
                'type' => 'select',
                'required' => true,
                'class' => array('form-row-wide'),
                'label' => 'Votre Pays',
                'options' => $this->get_country_code_options(),
            ), '');
            woocommerce_form_field('waxu_phone', array(
                'type' => 'tel',
                'required' => true,
                'class' => array('form-row-wide'),
                'label' => 'No de Téléphone',
            ), '');*/
        }

        /**
         * Initialise Gateway Settings Form Fields.
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable bank transfer', 'woocommerce'),
                    'default' => 'no',
                ),
                'title' => array(
                    'title' => __('Title', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                    'default' => 'WaXu',
                    'desc_tip' => true,
					'css' => 'border:none;pointer-events:none',
                ),
                'description' => array(
                    'title' => __('Description', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'woocommerce'),
                    'default' => 'Paiement sécurisé via WaXu' . PHP_EOL . 'Paiement via Appli/Mobile Money/VISA MASTERCARD AMEX JCB DISCOVERY',
                    'desc_tip' => true,
					'css' => 'border:none;pointer-events:none',
                ),
                'instructions' => array(
                    'title' => __('Instructions', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Instructions that will be added to the thank you page and emails.', 'woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'api_base_url' => array(
                    'title' => 'API Base URL',
                    'type' => 'text',
                    'description' => 'The api base url',
                    'default' => 'https://api.waxuapp.com',
					'css' => 'border:none;pointer-events:none',
                    // 'desc_tip' => true,
					//border:none; and then pointer-events:none;
                ),
                'api_service' => array(
                    'title' => 'API Service',
                    'type' => 'text',
                    'description' => 'The api service',
                    'default' => '',
                    // 'desc_tip' => true,
                ),
                'api_key' => array(
                    'title' => 'API Key',
                    'type' => 'text',
                    'description' => 'The api key',
                    'default' => '',
                    // 'desc_tip' => true,
                ),
                'country_codes' => array(
                    'title' => 'Country list',
                    'type' => 'textarea',
                    'description' => 'one per line',
                    'default' => '',
//					'desc_tip' => true,
                    'css' => 'height:200px'
                ),
                'fetch_country_list_button' => array(
                    'title' => 'Fetch country list',
                    'type' => 'fetch_country_list_button',
                    'default' => '',
                ),
            );
        }


        public function generate_fetch_country_list_button_html($key, $value) {
            ob_start();
            ?>
            <tr>
                <th>Fetch country list</th>
                <td>
                    <input type="checkbox" name="fetch_country_list" value="1">
                    <p class="description">Check this box to refetch country list</p>
                </td>
            </tr>
            <?php $content = ob_get_clean();
            return $content;
        }

        public function process_admin_options() {
            parent::process_admin_options();
            // var_dump($_POST);
            if (!isset($_POST['fetch_country_list'])) {
                return;
            }
            $this->fetch_country_list();
        }

        public function fetch_country_list() {
            $this->api_key = isset($_POST['woocommerce_waxu_api_key']) ? sanitize_text_field($_POST['woocommerce_waxu_api_key']) : $this->api_key;
            if (!$this->api_key) {
                return;
            }
            $params = array(
                'action' => 'getCountryList',
                'waxuapikey' => $this->api_key,
            );
            $request_endpoint = add_query_arg($params, $this->api_base_url);
            // var_dump($request_endpoint); die;
            $response = wp_remote_get($request_endpoint);
            if (is_wp_error($response)) {
                return;
            }
            // var_dump($response);
            $response_data = json_decode($response['body'], true);
            // var_dump($response_data); die;
            $country_list = array();
            foreach ($response_data['list'] as $item) {
                $country_list[] = $item['nomPays'] . ' ' . $item['indicatif'];
            }
            // $_POST['woocommerce_waxu_country_codes'] = implode(PHP_EOL, $country_list);
            $country_codes = implode(PHP_EOL, $country_list);
            $this->update_option('country_codes', $country_codes);
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page() {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false) {
            if ($this->instructions && !$sent_to_admin && 'offline' === $order->payment_method && $order->has_status('on-hold')) {
                echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
            }
        }

        /**
         * @param WC_Order $order
         *
         * @return array|WP_Error
         */
        private function send_bills_online($order) {
            $params = array(
                'action' => 'sendBillsOnLine',
                'facture' => $order->get_id(),
                'service' => $this->api_service,
                'montant' => $order->get_total(),
                'waxuapikey' => $this->api_key,
                'phonewaxu' => sprintf('%s%s', $order->get_meta('_waxu_country_code'), $order->get_meta('_waxu_phone')),
                'option' => 'POL',
            );
            $request_endpoint = add_query_arg($params, $this->api_base_url);
            // var_dump($request_endpoint); die;
            $response = wp_remote_get($request_endpoint);
            // var_dump($response); die;
            return $response;
        }

        /**
         * @param WC_Order $order
         */
        public function get_pay_status($order) {
			$currency_code = $order->get_currency();
			$currency_symbol = get_woocommerce_currency_symbol( $currency_code );
            $params = array(
                'action' => 'getStatus',
                'facture' => $order->get_id(),
                'service' => $this->api_service,
                'amount' => $order->get_total(),
                'waxuapikey' => $this->api_key,
                'phonewaxu' => '00000000000',
				'currency' => $order->get_currency(),
                'option' => 'POL',
            );
            $request_endpoint = add_query_arg($params, $this->api_base_url);
            // var_dump($request_endpoint); die;
            $response = wp_remote_get($request_endpoint);
            
             $postdata = http_build_query($params);
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.waxuapp.com',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $postdata,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                // 'Cookie: PHPSESSID=4a49bf5ead46d68d96c44801919b34e9'
              ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // echo $response;
            
            
            
            
            // var_dump($response);
            // if (is_wp_error($response)) {
                // return $order->get_status();
            // }
             $response_data = json_decode($response, true);
           
           // return $this->api_base_url;
           
            $status =strtolower($response_data['status']);
            // for testing
            // $status = 'declined';
            // $status = 'paid';
            // paid
            if ($status === 'paid') {
                $order->update_status('completed');
                update_post_meta($order->get_id(), '_transaction_id', $response_data['transacID']);
                return 'completed';
            } else if ($status === 'expired') {
                $order->update_status('failed');
            } else if ($status === 'declined') {
                $order->update_status('cancelled');
            } else if ($status === 'declined-user') {
                $order->update_status('cancelled');
            }
            // var_dump($response); die;
            return $order->get_status();
        }

        /**
         * Process the payment and return the result.
         *
         * @param int $order_id Order ID.
         * @return array
         */
        public function process_payment($order_id) {
			
			/*
			
			*/
			try {
				$order = wc_get_order($order_id);
				$webpayUrl = $this->api_base_url.'/api?billNo='.$order->get_id().'&amount='.$order->get_total().'&currency='.$order->get_currency().'&service='.$this->api_service.'&waxuapikey='.$this->api_key;
				$_SESSION['webpayUrl'] = $webpayUrl;
				/*add_action( 'wp_enqueue_scripts', 'modal_assets' );
				function modal_assets() {
					wp_register_script( 'waxu-modal', plugins_url( '/js/waxumodal.js' , __FILE__ ) );
					wp_enqueue_script( 'waxu-modal' );
				}*/
				
				
			} catch (Exception $e) {
				echo 'Exception reçue : ',  $e->getMessage(), "\n";
			} finally {
				echo "Seconde fin.\n";
			}
            
			
			//NABIL
            /*$send_bills_online_response = $this->send_bills_online($order);
            if (is_wp_error($send_bills_online_response)) {
                wc_add_notice($send_bills_online_response->get_error_message(), 'error');
                return array('result' => 'error');
            }*/
//
//			if ($order->get_total() > 0) {
//				// Mark as on-hold (we're awaiting the payment).
//				$order->update_status(apply_filters('woocommerce_bacs_process_payment_order_status', 'on-hold', $order), __('Awaiting BACS payment', 'woocommerce'));
//			} else {
//				$order->payment_complete();
//			}
//
//			// Remove cart.
//			WC()->cart->empty_cart();
//
//			// Return thankyou redirect.
//			return array(
//				'result' => 'success',
//				'redirect' => $this->get_return_url($order),
//			);


			

            return array(
                'result' => 'success',
                'redirect' => $order->get_view_order_url(),
            );
        }
    }
}

// Session manage
function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

function myEndSession() {
    session_destroy ();
}
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');


add_action('plugins_loaded', 'wc_waxu_payment_gateway', 11);
?>