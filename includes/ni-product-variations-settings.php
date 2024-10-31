<?php 
if ( !class_exists( 'Ni_Product_Variations_Dashboard' ) ) {
	class  Ni_Product_Variations_Settings{
		var $options = array();
		function __construct() {
	    }
		function page_init(){
			$columns = array();
			//delete_option('nipv_setting_option');
			$this->options = get_option('nipv_setting_option');
			$nipv_setting_option = isset($this->options["nipv_setting_option"])?$this->options["nipv_setting_option"]:array();
			//$this->prettyPrint(	$nipv_setting_option );
			//echo isset(	$nipv_setting_option["image"]);

		
		?>
        
        <form method="post" name="frm_nipv_report" id="frm_nipv_report">
        	<?php $columns = $this->get_columns();?>
            <table class="table">
            	<tr>
                	<td colspan="  <?php echo count($columns) ?> "><?php _e(  'Ni Table variations settings', 'nipvt'); ?> </td>
                </tr>
            	<?php foreach($columns as $key=>$value):?>
            	<tr>
                	<td><input type="checkbox" <?php echo isset($nipv_setting_option[$key])?"checked":"";  ?>  value="<?php echo $value; ?>" name="nipv_setting_option[<?php echo $key; ?>]"> 	</td>
                    <td><?php echo $value; ?> </td>
                </tr>
			
             	<?php endforeach;?>
            </table>
            
        
        	<input type="hidden" name="action" value="ni_nipv_action">
            <input type="submit" value="Save">
        </form>
        <?php		
		}
		function get_columns(){
			$columns = array();
			$columns["image"] = __('Image', 'nipvt');
			$columns["variation"] = __('Variation', 'nipvt');
			$columns["sku"] = __('SKU', 'nipvt');
			$columns["price"] = __('Price', 'nipvt');
			$columns["stock_status"] = __('Status', 'nipvt');
			$columns["stock_quantity"] = __('Stock', 'nipvt');
			
			return 	 apply_filters('niwoopvt_setting_columns', $columns );
		}
		function prettyPrint($a) {
			echo "<pre>";
			print_r($a);
			echo "</pre>";
		} 
	}
}
?>