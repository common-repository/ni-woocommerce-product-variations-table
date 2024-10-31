<?php
if ( !class_exists( 'Ni_wooCommerce_After_Single_Product_Summary' ) ) { 
	class Ni_wooCommerce_After_Single_Product_Summary{
		function __construct() {
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'ni_woocommerce_after_single_product_summary' ));
		}
		function get_default_columns(){
			$columns = array();
			$columns["image"] = __('Image', 'nipvt');
			$columns["variation"] = __('Variation', 'nipvt');
			$columns["sku"] = __('SKU', 'nipvt');
			$columns["price"] = __('Price', 'nipvt');
			$columns["stock_status"] = __('Stock Status', 'nipvt');
			return $columns;
		}
		function ni_woocommerce_after_single_product_summary(){
				global $product;
				
				$columns = array();
				$this->options = get_option('nipv_setting_option');
				$columns = isset($this->options["nipv_setting_option"])?$this->options["nipv_setting_option"]:array();
				
				$output = '';
				$variation_product = '';
				$sale_price = 0;
				$regular_price = 0;
				$cart_url = wc_get_cart_url();
				if(! $product->has_child() ) { 
					return '';
				}
				
				$attribute_slugs 	  = array();
				$term_names 		   = array();
				$available_variations = $product->get_available_variations();
				foreach ($available_variations as $key => $value) {
						$attributes = isset($value['attributes']) ? $value['attributes'] : array();					
						
						foreach ($attributes as $attribute_key => $attribute_slug) {
							if(!isset($attribute_slugs[$attribute_slug])){
								if(strpos($attribute_key, 'attribute_pa') === false){
									/*
										Custom Attribute
									*/
								}else{
									$attribute_slugs[$attribute_slug] = $attribute_slug;
								}
							}
						}
				}
				
				if(count($attribute_slugs) > 0){
					global $wpdb;			
					$terms = $wpdb->get_results("SELECT name, slug FROM  `{$wpdb->terms}` WHERE slug IN ('".implode("','",$attribute_slugs)."')");
					foreach ($terms as $term_slug => $term) {
						$term_names[$term->slug] = $term->name;
					}
				}
				
				if (count($columns)==0){
					$columns = $this->get_default_columns();
				}
				
				$columns['quantity'] = __('Quantity','nipvt');
				$columns['add_to_cart_column'] = __('Add To Cart','nipvt');
				$columns = apply_filters('nipvt_table_final_columns',$columns);
				do_action('nipvt_table_header');
				?>
                <div class="nipv_table_variation_box">
                 <table  cellspacing="0" class="nipv_table tablesorter" style="width:100%" id="nipv-tablesorter">
                    <thead>
                        <tr>
                             <?php foreach($columns  as $key=>$value): ?>
                                <?php switch ($key) {
                                    case "image":
                                    ?>
                                     <th data-sorter="false" style="width:5%"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                    case "variation":
                                    ?>
                                     <th style="width:20%"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                    case "sku":
                                    ?>
                                     <th style="width:10%"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                    case "price":
                                    ?>
                                     <th class="{sorter: 'digit'}" style="width:10%; text-align:right;"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                    case "stock_status":
                                    ?>
                                     <th style="width:14%"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                    case "stock_quantity":
                                    ?>
                                     <th class="{sorter: 'digit'} stock_column"  style="width:5%;"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                    case "variation_description":
                                    ?>
                                     <th  style="width:50%"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                    case "quantity":
                                    ?>
                                     <th style="width:10%;text-align:center" class="quantity_column" data-sorter="false"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                    case "add_to_cart_column":
                                    ?>
                                     <th data-sorter="false" class="add_to_cart_column"><?php echo $value; ?></th>
                                    <?php	
                                    break;
                                } ?>
                               
                             <?php endforeach; ?>
                        </tr>
                    </thead>	
                    <tbody>
                        <?php
						$args_quantity_input = array();						
                        foreach ($available_variations as $key => $value) {
						    $product_variation = wc_get_product($value['variation_id']);
							$product_id = $value['variation_id'];
						    if(!$product_variation->variation_is_visible())continue;
                        	?>
                                <tr>
                                <?php 
                                foreach ($columns as $k => $v){
                                    switch ($k) {
                                        case "image":
                                            ?>
                                                <td valign="middle"><?php  echo $product_variation->get_image(); ?></td>
                                            <?php
                                            break;
                                        case "variation":
                                            $all_variation = array();
                                            foreach($product_variation->get_attributes() as $key => $val ) {
                                                $all_variation[] = isset($term_names[$val]) ? $term_names[$val] : $val;
                                            } 
                                            ?>
                                                <td valign="middle"><?php echo esc_html(implode(" - ", $all_variation)); ?></td>
                                            <?php
                                            break;
                                        case "sku":
                                            ?>
                                                <td valign="middle"><?php echo esc_html ($product_variation->get_sku()); ?></td>
                                            <?php
                                            break;
                                        case "price":
                                            ?>
                                                <td valign="middle" style="text-align:right"><?php echo $product_variation->get_price_html();?></td>
                                            <?php
                                            break;
                                        case "stock_status":
                                            ?>
                                                <td valign="middle"><?php echo esc_html($product_variation->get_stock_status());?></td>
                                            <?php
                                            break;
                                        case "stock_quantity":
                                            ?>
                                                <td valign="middle" style="text-align:right"><?php echo esc_html($product_variation->get_stock_quantity()); ?></td>
                                            <?php
                                            break;
                                        case "variation_description":
                                            ?>
                                                <td valign="middle"><?php echo $product_variation->get_variation_description();?></td>
                                            <?php
                                            break;
                                        case "quantity":
                                            ?>
                                                <td valign="middle" class="quantity_column" style="width:10%;text-align:center">
													<?php echo woocommerce_quantity_input($args_quantity_input);?>
                                                    <input type="hidden" class="product_id" value="<?php echo $product_id;?>" />
                                                </td>
                                            <?php
                                            break;
                                        case "add_to_cart_column":
                                            ?>
                                            <td valign="middle" class="add_to_cart_column">
                                                <span class="_add_to_cart">
                                                  <?php if(  $product_variation->is_in_stock()) { 
                                                    $url 		= add_query_arg( 'add-to-cart', $product_id, $cart_url);
                                                    ?>
                                                     <a rel="nofollow" href="<?php echo $url; ?>" data-quantity="1" data-product_id="<?php  echo $product_id; ?>" data-product_sku="" class="button product_type_simple add_to_cart_button ajax_add_to_cart ni_add_to_cart_button"> <?php _e(  'Add to cart', 'nipvt') ?> </a>
                                                    <?php	
                                                  }
                                                  ?>	  
                                                </span>
                                            </td>
                                            <?php
                                            break;	
                                    }	
                                }?>
                                </tr>
                        	<?php 
						}
					?>
                    </tbody>
    			</table>
                </div>
                <?php do_action('nipvt_table_footer');
		}
		
		
		
		function prettyPrint($a) {
			echo "<pre>";
			print_r($a);
			echo "</pre>";
		} 
	}
}
?>