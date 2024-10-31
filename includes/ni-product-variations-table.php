<?php 
if ( !class_exists( 'Ni_Product_Variations_Table' ) ) {
	class Ni_Product_Variations_Table{
		var $nipv_constant = array();  
		function __construct($nipv_constant = array()) {
			$this->nipv_constant = $nipv_constant; 
			add_action( 'plugins_loaded', array( $this, 'ni_remove_variable_product_add_to_cart' ));
			//add_action( 'woocommerce_after_single_product_summary', array( $this, 'ni_woocommerce_after_single_product_summary' ));
			add_action('wp_head',  array(&$this,'wp_head' ));
			add_action( 'admin_menu',  array(&$this,'admin_menu' ));
			add_action( 'admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));
			add_action( 'wp_ajax_ni_nipv_action',  array(&$this,'ajax_ni_nipv_action' )); /*used in form field name="action" value="my_action"*/
			
			add_action( 'wp_ajax_ni_nipv_bulk_action',  array(&$this,'ni_nipv_bulk_action' ));
			add_action( 'wp_ajax_nopriv_ni_nipv_bulk_action',  array(&$this,'ni_nipv_bulk_action' ));
			
			$this->add_table_variations_page();
				
		}
		function add_table_variations_page(){
			include_once("ni-woocommerce-after-single-product-summary.php");
			$objtable = new Ni_wooCommerce_After_Single_Product_Summary();
		}
		function admin_menu(){
				add_menu_page(  __(  'Product Variation', 'nipvt'), __(  'Product Variation', 'nipvt'), $this->nipv_constant['manage_options'], $this->nipv_constant['menu'], array( $this, 'add_page'), 'dashicons-align-center', "58.4361" );
				add_submenu_page($this->nipv_constant["menu"], __(  'Dashboard', 'nipvt')  ,__(  'Dashboard', 'nipvt'), $this->nipv_constant['manage_options'],$this->nipv_constant["menu"], array( $this, 'add_page'));
				add_submenu_page($this->nipv_constant["menu"], __(  'Settings', 'nipvt') ,__(  'Settings', 'nipvt'),  $this->nipv_constant['manage_options'],'nipv-settings', array( $this, 'add_page'));
				
			add_submenu_page($this->nipv_constant["menu"], 'Other Plugins', 'Other Plugins', 'manage_options', 'niwoopvt-other-plugins' , array(&$this,'add_page'));	
				
				
				
		
		}
		function add_page(){
			$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
			if ($page == "nipv-product-variation"){
				include_once("ni-product-variations-dashboard.php");
				$objnipv =new  Ni_Product_Variations_Dashboard();
				$objnipv->page_init();
			}
			if ($page == "nipv-settings"){
				include_once("ni-product-variations-settings.php");
				$objnipv =new  Ni_Product_Variations_Settings();
				$objnipv->page_init();
			}
			if ($page =="niwoopvt-other-plugins"){
				include_once("niwoopvt-other-plugins.php");	
				$obj = new NiWooPVT_Other_Plugins();
				$obj->page_init();
			}
			
		}
		function wp_head(){
			$display  = "block";
			if (is_product()){
				wp_register_style('nipv-style', plugins_url( '../assets/css/nipv-style.css', __FILE__ ));
				wp_enqueue_style( 'nipv-style');
				
				wp_enqueue_script( 'nipv-tablesorter', plugins_url( '../assets/js/jquery.tablesorter.min.js', __FILE__ ) );
				?>
				<script type="text/javascript">
				jQuery(function($){
				  $("#nipv-tablesorter").tablesorter(); 
				  
				  $(document).on("change",".qty",function(){
						$(this).parent().parent().parent().find(".ni_add_to_cart_button").attr("data-quantity",$(this).val());
					});
				  
				});
				</script>
				<?php
			}
			?>
            <style>
            ._add_to_cart {
				display:<?php echo $display; ?>;
				}
            </style>
            <?php
		}
		function admin_enqueue_scripts(){
			$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
			if ($page == "nipv-product-variation"  || $page == "nipv-settings"  || $page ==  "niwoopvt-other-plugins"){
					
					wp_register_style( 'niwoopvt-css', plugins_url( '../assets/css/nipv-summary.css', __FILE__ ));
					wp_enqueue_style( 'niwoopvt-css' );
				
				
					wp_register_style( 'niwoopvt-font-awesome-css', plugins_url( '../assets/css/font-awesome.css', __FILE__ ));
		 			wp_enqueue_style( 'niwoopvt-font-awesome-css' );
					
					wp_register_script( 'niwoopvt-amcharts-script', plugins_url( '../assets/js/amcharts/amcharts.js', __FILE__ ) );
					wp_enqueue_script('niwoopvt-amcharts-script');
				
		
					wp_register_script( 'niwoopvt-light-script', plugins_url( '../assets/js/amcharts/light.js', __FILE__ ) );
					wp_enqueue_script('niwoopvt-light-script');
				
					wp_register_script( 'niwoopvt-pie-script', plugins_url( '../assets/js/amcharts/pie.js', __FILE__ ) );
					wp_enqueue_script('niwoopvt-pie-script');
					
					
					wp_register_style('niwoopvt-bootstrap-css', plugins_url('../assets/css/lib/bootstrap.min.css', __FILE__ ));
		 			wp_enqueue_style('niwoopvt-bootstrap-css' );
				
					wp_enqueue_script('niwoopvt-bootstrap-script', plugins_url( '../assets/js/lib/bootstrap.min.js', __FILE__ ));
					wp_enqueue_script('niwoopvt-popper-script', plugins_url( '../assets/js/lib/popper.min.js', __FILE__ ));
				
				
			}
			if ($page == "nipv-settings"){
				wp_enqueue_script( 'ni-ajax-script-nipv', plugins_url( '../assets/js/script.js', __FILE__ ), array('jquery') );
				wp_localize_script( 'ni-ajax-script-nipv','ni_nipv_ajax_object',array('ni_nipv_ajax_object_ajaxurl'=>admin_url('admin-ajax.php') ) );
				wp_enqueue_script( 'ni-ajax-script-nipv-report', plugins_url( '../assets/js/ni-woocommerce-product-variations-table.js', __FILE__ ) );
			}
		}
		function ni_remove_variable_product_add_to_cart(){
			remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
		}
		function prettyPrint($a) {
			echo "<pre>";
			print_r($a);
			echo "</pre>";
		}
		function get_nipv_price($price = 0){
			$new_price = 0;
			if ($price){
				$new_price = wc_price($price);
			}else{
				$new_price = wc_price(0);
			}
			return $new_price;	
		}
		
		/**
		 * AJAX add to cart.
		 */
		public static function add_to_cart() {
			ob_start();
			
			$return = array();
			
			$return['error'] = '';
			$return['success'] = '';
			$return['product_url'] = '';
	
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( ! isset( $_POST['product_id'] ) ) {
				return;
			}
	
			$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
			$product           = wc_get_product( $product_id );
			$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
			$product_status    = get_post_status( $product_id );
			$variation_id      = 0;
			$variation         = array();
	
			if ( $product && 'variation' === $product->get_type() ) {
				$variation_id = $product_id;
				$product_id   = $product->get_parent_id();
				$variation    = $product->get_variation_attributes();
			}
	
			if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {	
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );	
				if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
					//wc_add_to_cart_message( array( $product_id => $quantity ), true );
				}
				//wc_add_to_cart_message( array( $product_id => $quantity ), true );
				$return['success'] = true;	
			} else {				
				$return['error']       = true;
				$return['product_url'] = apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id );
			}
			return $return;
			// phpcs:enable
		}
		
		function ni_nipv_bulk_action(){
			$product_data = isset($_REQUEST['product_data']) ? $_REQUEST['product_data'] : array();
			foreach($product_data as $key => $cart_data){
				$_POST['product_id'] = isset($cart_data['product_id']) ? $cart_data['product_id'] : 0;
				$_POST['quantity'] = isset($cart_data['quantity']) ? $cart_data['quantity'] : 0;
				$return = $this->add_to_cart();
				//error_log(print_r($return,true));
			}
			die;	
		}
		
		function ajax_ni_nipv_action(){
			update_option("nipv_setting_option",$_REQUEST);
			//echo json_encode($_REQUEST);
			echo "settings saved successfully.";
			die;	
		}
 	}
}
?>