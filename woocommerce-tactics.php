<?php
/**
* Plugin Name: Start From for WooCommerce
* Plugin URI: thinkin-smart.com
* Description: Show for your variable products inside woocommerce only the start price to the user
* Version: 1.0.0
* Author: Thinkin Smart Team
* Author URI: thinkin-smart.com
* License: GPLv3
* Text Domain: tactics
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'plugins_loaded', 'myplugin_load_textdomain' );

function myplugin_load_textdomain() {
  load_plugin_textdomain( 'tactics', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

if ( ! class_exists( 'WC_Settings_Tactics' ) ) :
function my_plugin_add_settings() {
	/**
	 * Settings class
	 *
	 * @since 1.0.0
	 */
	class WC_Settings_Tactics extends WC_Settings_Page {
		/**
		 * Setup settings class
		 *
		 * @since  1.0
		 */
		public function __construct() {
			$this->id    = 'tactics';
			$this->label = __( 'Tactics', 'tactics' );
			add_filter( 'woocommerce_settings_tabs_array',        array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id,      array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'woocommerce_sections_' . $this->id,      array( $this, 'output_sections' ) );
		}
		/**
		 * Get settings array
		 *
		 * @since 1.0.0
		 * @param string $current_section Optional. Defaults to empty string.
		 * @return array Array of settings
		 */
		public function get_settings() {

				/**
				 * Filter Plugin Section 1 Settings
				 *
				 * @since 1.0.0
				 * @param array $settings Array of the plugin settings
				 */
				$settings = apply_filters( 'Tactics_settings', array(
					array(
						'name' => __( 'Conversion Tactics', 'tactics' ),
						'type' => 'title',
            'desc' => __( 'Looking for a boost in conversion? Adjust in here your front end details.', 'tactics' ),
						'id'   => 'conversion_tactics',
          ),

          array(
						'name' => '',
						'type' => 'title',
						'desc' => __( 'Change the pricing appearance of your variable products:', 'tactics' ),
						'id'   => 'note_tactics',
          ),

          array(
            'title'    => __( 'Starting Title', 'tactics' ),
            'desc'     => __( 'Place in here the text that comes in front of the starting price.', 'tactics' ),
            'id'       => 'woocommerce_message_price',
            'default'  => '',
            'type'     => 'text',
            'desc_tip' => true,
          ),

					array(
						'type'     => 'select',
						'id'       => 'Tactics_select_option',
						'name'     => __( 'Location', 'tactics' ),
						'options'  => array(
							'archive'    => __( 'Showing on archive pages', 'tactics' ),
							'product'		 => __( 'Showing on product pages', 'tactics' ),
							'all'        => __( 'Showing on archive and product pages', 'tactics' ),
						),
						'class'    => 'wc-enhanced-select',
						'desc_tip' => __( 'You can display the change in several locations.', 'tactics' ),
						'default'  => 'Showing on archive pages',
					),

					array(
						'type' => 'sectionend',
						'id'   => 'Tactics_important_options'
					),

        ) );

				return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
			}

		/**
		 * Output the settings
		 *
		 * @since 1.0
		 */
		public function output() {
      global $current_section;

			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		}

    /**
	 	 * Save settings
	 	 *
	 	 * @since 1.0
		 */
		public function save() {
      global $current_section;

			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::save_fields( $settings );
		}
	}
	return new WC_Settings_Tactics();
}
add_filter( 'woocommerce_get_settings_pages', 'my_plugin_add_settings', 15 );
endif;

function cw_change_product_price_display( $price ) {
	$post = get_post();
	$val_select = get_option('Tactics_select_option');
	$isArchive = is_archive() && $val_select === 'archive';
	$isProduct = is_archive() == false && $val_select === 'product';
	$isAll = $val_select === 'all';
	$newPrice = get_option('woocommerce_message_price'). ' ' . $price;
	return $isArchive ? $newPrice : $isProduct ? $newPrice : $isAll ? $newPrice : $price;
}
add_filter( 'woocommerce_get_price_html', 'cw_change_product_price_display' );
add_filter( 'woocommerce_cart_item_price', 'cw_change_product_price_display' );