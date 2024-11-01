<?php
/*
Plugin Name: The Cao WooCommerce
Plugin URI: http://webbinhdan.com
Description: Add VINA, MOBI, VIETTEL, VTC, GATE... Card Gateways for WooCommerce. Ủng hộ đặt mua phiên bản <a target="_blank" href="http://webbinhdan.com/san-pham/the-cao-woocommerce-v-pro/"><strong>The Cao WooCommerce v.Pro</strong></a>.
Version: 1.1.1
Author: WebBinhDan.com
Author URI: http://webbinhdan.com
License: GPLv2 or later
Text Domain: the-cao-woocommerce
Domain Path: /languages/
*/

//Load Function
function the_cao_gateway_class() {
	
	//Add The Cao
	function add_theo_cao_gateway_class( $methods ) {
		$methods[] = 'WC_Gateway_The_Cao'; 
		return $methods;
	}		
	add_filter( 'woocommerce_payment_gateways', 'add_theo_cao_gateway_class' );
	
	// The Cao Class
	class WC_Gateway_The_Cao extends WC_Payment_Gateway {		
	
		public function __construct(){
			$this->id               = 'recharge_card';
			$this->icon             = apply_filters( 'woocommerce_stripe_icon', plugins_url( 'images/the.png' , __FILE__ ) );
			$this->has_fields       = true;
			$this->method_title     = __( 'Recharge Card', 'the-cao-woocommerce' );	
				
			$this->init_form_fields();
			$this->init_settings();
			
			$this->title            = $this->get_option( 'title' );
	
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
		
		public function init_form_fields() {
			global $woocommerce;				
				
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable/Disable', 'the-cao-woocommerce' ),
					'type' => 'checkbox',
					'label' => __( 'Enable Cheque Payment', 'the-cao-woocommerce' ),
					'default' => 'yes'
				),
				'title' => array(
					'title' => __( 'Title', 'the-cao-woocommerce' ),
					'type' => 'text',
					'description' => __( 'It show on payment page.', 'the-cao-woocommerce' ),
					'default' => __( 'Recharge Card', 'the-cao-woocommerce' ),
					'desc_tip'      => true,
				),				
				'merchant_id' => array(
					'title' => __( 'Merchant ID', 'the-cao-woocommerce' ),
					'type' => 'text',
					'description' => __( 'Get when register at Baokim.vn', 'the-cao-woocommerce' ),
					'desc_tip'      => true,
				),
				'api_username' => array(
					'title' => __( 'API Username', 'the-cao-woocommerce' ),
					'type' => 'text',
					'description' => __( 'Get when register at Baokim.vn', 'the-cao-woocommerce' ),
					'desc_tip'      => true,
				),
				'api_password' => array(
					'title' => __( 'API Password', 'the-cao-woocommerce' ),
					'type' => 'text',
					'description' => __( 'Contact to Baokim.vn support to get it.', 'the-cao-woocommerce' ),
					'desc_tip'      => true,
				),
				'secure_code' => array(
					'title' => __( 'Secure Code', 'the-cao-woocommerce' ),
					'type' => 'text',
					'description' => __( 'Contact to Baokim.vn support to get it.', 'the-cao-woocommerce' ),
					'desc_tip'      => true,
				),
				'description' => array(
					'title' => __( 'Customer Message', 'the-cao-woocommerce' ),
					'type' => 'textarea',
					'description' => __( 'Not required.', 'the-cao-woocommerce' ),
					'desc_tip'      => true,					
				),
				'api_details' => array(
					'title'       => __( 'The Cao WooCommerce v.Pro', 'woocommerce' ),
					'type'        => 'title',
					'description' => sprintf( __( 'Cảm ơn các bạn đã tin dùng, ủng hộ và góp ý cho plugin The Cao WooCommerce trong thời gian qua, hiện tại bên mình có tổng hợp một số tính năng phổ biến mà khách hàng đã yêu cầu phát triển riêng thường gặp để phát triển thành phiên bản The Cao WooCommerce v.Pro. Thực ra chữ v.Pro ở đây ko có nhiều ý nghĩa lắm mình chỉ muốn một cái tên khác với tên của phiên bản miễn phí nên dùng tạm, còn ý nghĩa thực có thể hiểu là phiên bản hỗ trợ tài chính cho nhà phát triển sẽ hợp lý hơn bởi mình đang phát triển một dự án khác cần đến rất nhiều sự trợ giúp.<br><br>Các tính năng chính của v.Pro ở thời điểm hiện tại:

<br><br>+) Bổ sung chức năng khách vãng lai có thể đặt hàng.

<br><br>+) Thêm thông báo chung tại trang thanh toán.

<br><br>+) Cho phép xem số dư của thẻ cào còn lại tại thời điển thanh toán.

<br><br>+) Thêm shortcode [sodu] để có thể chèn ở bất kì điểm nào bạn muốn xuất hiện số dư.

<br><br>+) Thông báo cập nhật số dư mới nếu tổng số tiền thanh toán chưa đủ để thực hiện giao dịch hiện tại.

<br><br>+) Bắt các lỗi nếu xảy ra trong quá trình giao dịch với server của nhà cung cấp để thông báo tới khách hàng.

<br><br>+) Lưu nhật kí các giao dịch thẻ cào vào hóa đơn của từng phiên giao dịch để bạn kiểm tra khi cần thiết.

<br><br>+) Admin với quyền set số dư tùy ý cho khách hàng là thành viên.

<br><br>Mức phí hiện tại cho phiên bản v.Pro là <strong>500.000</strong> VND, thanh toán bằng hình thức thẻ cào.

<br><br>===> <a style="text-decoration:none" target="_blank" href="%s">Ủng hộ đặt mua phiên bản <strong>The Cao WooCommerce v.Pro</strong> tại đây</a>

<br><br>p/s: một vài tính năng cần thết của v.Pro mình cũng đã thêm vào ở bản miễn phí trong lần cập nhật mới nhất này.
<br><br>Chúc các bạn phát triển site tốt!', 'woocommerce' ), 'http://webbinhdan.com/san-pham/the-cao-woocommerce-v-pro/' ),
				),
			);
	
		}
		
		public function payment_fields(){			
		?>
            <table>
                <?php if( $this->get_option('description') != false ) { ?>
                    <tr>
                        <td><label for="txtseri" class=""><?php echo __( 'Note', 'the-cao-woocommerce') ?></label></td>
                        <td><?php echo $this->get_option( 'description' ); ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><label for="txtpin" class=""><?php echo __( 'Card', 'the-cao-woocommerce') ?></label></td>
                    <td>
                        <select class="form-control" name="chonmang">
                        <option value="VIETEL">Viettel</option>
                        <option value="MOBI">Mobifone</option>
                        <option value="VINA">Vinaphone</option>
                        <option value="GATE">Gate</option>
                        <option value="VTC">VTC</option>
                        </select>  
                    </td>
                </tr>
                <tr>
                    <td><label for="txtpin" class=""><?php echo __( 'Card Number', 'the-cao-woocommerce') ?></label></td>
                    <td><input type="text" class="form-control" id="txtpin" name="txtpin" placeholder="<?php echo __( 'Card Number', 'the-cao-woocommerce') ?>" data-toggle="tooltip" data-title="<?php echo __( 'Number behind the foil layer', 'the-cao-woocommerce') ?>"/></td>
                </tr>
                <tr>
                    <td><label for="txtseri" class=""><?php echo __( 'Seri Number', 'the-cao-woocommerce') ?></label></td>
                    <td><input type="text" class="form-control" id="txtseri" name="txtseri" placeholder="<?php echo __( 'Seri Number', 'the-cao-woocommerce') ?>" data-toggle="tooltip" data-title="<?php echo __( 'Number on card', 'the-cao-woocommerce') ?>"></td>
                </tr>				
            </table>
		<?php
		}
		
		
		public function process_payment( $order_id ) {
			global $woocommerce;
			$order = new WC_Order( $order_id );		
			
			define('CORE_API_HTTP_USR', 'merchant_19002');
			define('CORE_API_HTTP_PWD', '19002mQ2L8ifR11axUuCN9PMqJrlAHFS04o');
			
			$bk = 'https://www.baokim.vn/the-cao/restFul/send';
			$seri = isset($_POST['txtseri']) ? $_POST['txtseri'] : '';
			$seri = preg_replace( '/[^a-zA-Z0-9]/i', "", $seri );
			
			$sopin = isset($_POST['txtpin']) ? $_POST['txtpin'] : '';
			$sopin = preg_replace( '/[^0-9]/i', "", $sopin );
			
			//(VINA, MOBI, VIETEL, VTC, GATE)
			$mang = isset($_POST['chonmang']) ? $_POST['chonmang'] : '';
			$mang = preg_replace( '/[^a-zA-Z0-9]/i', "", $mang );
			
			$user = isset($_POST['txtuser']) ? $_POST['txtuser'] : '';
			$user = preg_replace( '/[^a-zA-Z0-9]/i', "", $user );
			
			
			
				if($mang=='MOBI'){
						$ten = "Mobifone";
					}
				else if($mang=='VIETEL'){
						$ten = "Viettel";
					}
				else if($mang=='GATE'){
						$ten = "Gate";
					}
				else if($mang=='VTC'){
						$ten = "VTC";
					}
				else $ten ="Vinaphone";
			
			//MerchantID 
			$merchant_id = $this->get_option( 'merchant_id' );
			//Api username 
			$api_username = $this->get_option( 'api_username' );
			//Api Pwd
			$api_password = $this->get_option( 'api_password' );
			//TransactionId 
			$transaction_id = time();
			//Secure Code
			$secure_code = $this->get_option( 'secure_code' );
			
			$arrayPost = array(
				'merchant_id'=>$merchant_id,
				'api_username'=>$api_username,
				'api_password'=>$api_password,
				'transaction_id'=>$transaction_id,
				'card_id'=>$mang,
				'pin_field'=>$sopin,
				'seri_field'=>$seri,
				'algo_mode'=>'hmac'
			);
			
			ksort($arrayPost);
			
			$data_sign = hash_hmac('SHA1',implode('',$arrayPost),$secure_code);
			
			$arrayPost['data_sign'] = $data_sign;
			
			$curl = curl_init($bk);
			
			curl_setopt_array($curl, array(
				CURLOPT_POST=>true,
				CURLOPT_HEADER=>false,
				CURLINFO_HEADER_OUT=>true,
				CURLOPT_TIMEOUT=>30,
				CURLOPT_RETURNTRANSFER=>true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_HTTPAUTH=>CURLAUTH_DIGEST|CURLAUTH_BASIC,
				CURLOPT_USERPWD=>CORE_API_HTTP_USR.':'.CORE_API_HTTP_PWD,
				CURLOPT_POSTFIELDS=>http_build_query($arrayPost)
			));
			
			$data = curl_exec($curl);
			
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			$result = json_decode($data,true);
			date_default_timezone_set('Asia/Ho_Chi_Minh');
			$time = time();
						
			if($status==200){
				$amount = $result['amount'];

				switch($amount) {
					case 10000: $xu = 10000; break;
					case 20000: $xu = 20000; break;
					case 30000: $xu = 30000; break;
					case 50000: $xu= 50000; break;
					case 100000: $xu = 100000; break;
					case 200000: $xu = 200000; break;
					case 300000: $xu = 300000; break;
					case 500000: $xu = 500000; break;
					case 1000000: $xu = 1000000; break;
				}
				
				//Update your money
				$user_ID = get_current_user_id();
				
				//User
				if($user_ID!=0){
					
					$your_money=get_user_meta($user_ID, "your_money", true);
					$your_money_temp=$your_money+$amount;
					update_user_meta( $user_ID, "your_money", $your_money_temp );
					
					//Get total
					global $woocommerce;
					$wc_order = new WC_Order( $order_id );
					$grand_total = $wc_order->order_total;
					$grand_total = (int)$grand_total;
					
					//get your money
					$your_money=get_user_meta($user_ID, "your_money", true);
					
				}
				//None user
				else{
					
					$your_money=get_metadata('post', $order_id, "your_money", true);
					$your_money_temp=$your_money+$amount;
					update_metadata('post', $order_id, "your_money", $your_money_temp );
					
					//Get total
					global $woocommerce;
					$wc_order = new WC_Order( $order_id );
					$grand_total = $wc_order->order_total;
					$grand_total = (int)$grand_total;
					
					//get your money
					$your_money=get_metadata('post', $order_id, "your_money", true);
					
				}
				
				if($your_money>=$grand_total){
					
					//Update your money
					$your_money_temp=$your_money-$grand_total;
					if($user_ID!=0){
						update_user_meta( $user_ID, "your_money", $your_money_temp );
					}
					else{
						update_metadata('post', $order_id, "your_money", $your_money_temp );
					}
					
					//Add note
					$data = array(
						'comment_post_ID' => $order_id,
						'comment_author' => 'WooCommerce',
						'comment_content' => $ten . __( ', Card Number: ', 'the-cao-woocommerce') .$sopin. __( ', Seri Number: ', 'the-cao-woocommerce') .$seri,
						'comment_type' => 'order_note',
						'comment_parent' => 0,
						'user_id' => 0,
						'comment_agent' => 'WooCommerce',
						'comment_approved' => 1,
					);						
					wp_insert_comment($data);
					
					// Mark as on-hold (we're awaiting the cheque)
					$order->update_status('completed', __( 'Completed', 'the-cao-woocommerce' ));
		
					// Reduce stock levels
					$order->reduce_order_stock();
				
					// Remove cart
					$woocommerce->cart->empty_cart();
				
					// Return thankyou redirect
					return array(
						'result' => 'success',
						'redirect' => $this->get_return_url( $order )
					);
				}
				else{
					
					//Add note
					$data = array(
						'comment_post_ID' => $order_id,
						'comment_author' => 'WooCommerce',
						'comment_content' => $ten . __( ', Card Number: ', 'the-cao-woocommerce') .$sopin. __( ', Seri Number: ', 'the-cao-woocommerce') .$seri,
						'comment_type' => 'order_note',
						'comment_parent' => 0,
						'user_id' => 0,
						'comment_agent' => 'WooCommerce',
						'comment_approved' => 1,
					);						
					wp_insert_comment($data);
					
					wc_add_notice( __('This card does not have enough money for the transaction. Your current balance:', 'the-cao-woocommerce') . $your_money , 'error' );
					wc_add_notice( __('Please add more money to complete!', 'the-cao-woocommerce') , 'error' );
					return;
				}				
			
			}
			else{
				
				//Add note
				$data = array(
					'comment_post_ID' => $order_id,
					'comment_author' => 'WooCommerce',
					'comment_content' => $ten . __( ', Card Number: ', 'the-cao-woocommerce') .$sopin. __( ', Seri Number: ', 'the-cao-woocommerce') .$seri,
					'comment_type' => 'order_note',
					'comment_parent' => 0,
					'user_id' => 0,
					'comment_agent' => 'WooCommerce',
					'comment_approved' => 1,
				);						
				wp_insert_comment($data);
							  
				$error = $result['errorMessage'];
				
				wc_add_notice( __('Error:', 'the-cao-woocommerce') . $status . $error , 'error' );
				wc_add_notice( __('Please check the card again or contact to support about this error!', 'the-cao-woocommerce') , 'error' );
				return;
			}
						
		}
		
	}
	
}
add_action( 'plugins_loaded', 'the_cao_gateway_class' );



//Show Balance
if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php"); 
}

if ( current_user_can('administrator') ) {
	add_action( 'show_user_profile', 'view_your_money' );
	add_action( 'edit_user_profile', 'view_your_money' );
	function view_your_money( $user ) { ?>
		<h3><?php echo __( 'Balance', 'the-cao-woocommerce') ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="your_money"><?php echo __( 'Current balance', 'the-cao-woocommerce') ?></label></th>
				<td>
					<input type="text" name="your_money" id="your_money" value="<?php echo esc_attr( get_user_meta($user->ID, 'your_money', true) ); ?>" class="regular-text" /><br />
					<span class="description"><?php echo __( 'Just typing numbers', 'the-cao-woocommerce') ?></span>
				</td>
			</tr>
		</table>
	<?php }
	
	
	add_action( 'personal_options_update', 'save_your_money' );
	add_action( 'edit_user_profile_update', 'save_your_money' );
	function save_your_money( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
		/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
		update_usermeta( $user_id, 'your_money', preg_replace( '/[^0-9]/i', "", $_POST['your_money'] ) );
	}
}
else{
	add_action( 'show_user_profile', 'view_your_money' );
	function view_your_money( $user ) { ?>
		<h3><?php echo __( 'Balance', 'the-cao-woocommerce') ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="your_money"><?php echo __( 'Current balance', 'the-cao-woocommerce') ?></label></th>
				<td>
					<input type="text" name="your_money" id="your_money" value="<?php echo esc_attr( get_user_meta($user->ID, 'your_money', true) ); ?>" class="regular-text" /><br />
					<span class="description"><?php echo __( 'Not typing here', 'the-cao-woocommerce') ?></span>
				</td>
			</tr>
		</table>
	<?php }
}


//Load language
function load_language_init() {
 $plugin_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages';
 load_plugin_textdomain( 'the-cao-woocommerce', false, $plugin_dir );
}
add_action('plugins_loaded', 'load_language_init');