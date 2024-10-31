<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
/*
Plugin Name: Ni WooCommerce Product Variations Table
Description: Ni WooCommerce Product Variations Table plugin change the WooCommerce product variation dropdown to Variations table or Variations Grid.
Version: 1.6.3
Author:anzia
Author URI: http://naziinfotech.com/
Plugin URI: https://wordpress.org/plugins/ni-woocommerce-product-variations-table/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html
Requires at least: 4.7
Tested up to: 6.4.3
WC requires at least: 3.0.0
WC tested up to: 8.7.0
Last Updated Date: 24-March-2024
Requires PHP: 7.0
*/
if ( !class_exists( 'Ni_WooCommerce_Product_Variations_Table' ) ) {
	class Ni_WooCommerce_Product_Variations_Table{
		var $nipv_constant = array();
		public function __construct(){
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links_variations_table' ), 10, 2);
			$this->nipv_constant = array(
				 "prefix" 		  => "ni-",
				 "manage_options" => "manage_options",
				 "menu"   		  => "nipv-product-variation",
				);
			include_once("includes/ni-product-variations-table.php");
			$nipvt  =  new Ni_Product_Variations_Table($this->nipv_constant);
		}
		function plugin_action_links_variations_table($actions, $plugin_file){
			static $plugin;

			if (!isset($plugin))
				$plugin = plugin_basename(__FILE__);
			if ($plugin == $plugin_file) {
					  $settings_url = admin_url() . 'admin.php?page=nipv-settings';
						$settings = array('settings' => '<a href='. $settings_url.'>' . __('Settings', 'nipvt') . '</a>');
						$site_link = array('support' => '<a href="http://naziinfotech.com" target="_blank">' . __('Support', 'nipvt') . '</a>');
						$email_link = array('email' => '<a href="mailto:support@naziinfotech.com" target="_top">' . __('Email', 'nipvt') . '</a>');
				
						$actions = array_merge($settings, $actions);
						$actions = array_merge($site_link, $actions);
						$actions = array_merge($email_link, $actions);
					
				}
				
				return $actions;
				}
	}
	$niwpvt =  new Ni_WooCommerce_Product_Variations_Table();
}
?>