<?php
/**
 * Plugin Name: WooCommerce Min/Max Quantity Add-On
 * Description: A WooCommerce add-on plugin to set minimum and maximum quantity limits for orders.
 * Version: 1.0.0
 * Author: Haris Ishar
 * Author URI: https://github.com/harisishar/
 */

// Add validation for minimum and maximum quantity limits
function wc_min_max_quantity_validation($passed, $product_id, $quantity) {
    $min_quantity = get_post_meta($product_id, 'wc_min_quantity', true);
    $max_quantity = get_post_meta($product_id, 'wc_max_quantity', true);
    
    if ($min_quantity && $quantity < $min_quantity) {
        wc_add_notice(__('Minimum quantity required: ' . $min_quantity, 'woocommerce'), 'error');
        $passed = false;
    }
    
    if ($max_quantity && $quantity > $max_quantity) {
        wc_add_notice(__('Maximum quantity allowed: ' . $max_quantity, 'woocommerce'), 'error');
        $passed = false;
    }
    
    return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'wc_min_max_quantity_validation', 10, 3);

// Add quantity fields to the product edit page in the admin
function wc_min_max_quantity_fields() {
    global $woocommerce, $post;
    
    echo '<div class="options_group">';
    
    woocommerce_wp_text_input(array(
        'id' => 'wc_min_quantity',
        'label' => __('Minimum Quantity', 'woocommerce'),
        'placeholder' => '',
        'description' => __('Set the minimum quantity allowed for this product.', 'woocommerce'),
        'desc_tip' => true
    ));
    
    woocommerce_wp_text_input(array(
        'id' => 'wc_max_quantity',
        'label' => __('Maximum Quantity', 'woocommerce'),
        'placeholder' => '',
        'description' => __('Set the maximum quantity allowed for this product.', 'woocommerce'),
        'desc_tip' => true
    ));
    
    echo '</div>';
}
add_action('woocommerce_product_options_inventory_product_data', 'wc_min_max_quantity_fields');

// Save quantity fields data when a product is saved
function wc_min_max_quantity_fields_save($post_id) {
    $min_quantity = isset($_POST['wc_min_quantity']) ? absint($_POST['wc_min_quantity']) : '';
    $max_quantity = isset($_POST['wc_max_quantity']) ? absint($_POST['wc_max_quantity']) : '';
    
    update_post_meta($post_id, 'wc_min_quantity', $min_quantity);
    update_post_meta($post_id, 'wc_max_quantity', $max_quantity);
}
add_action('woocommerce_process_product_meta', 'wc_min_max_quantity_fields_save');
