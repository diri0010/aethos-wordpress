<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		// Widget styling is now bundled in the React widget from SaaS
		// No need to load local CSS anymore
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		// Widget is now loaded from SaaS in render_widget()
		// No need to enqueue local scripts here
	}

	/**
	 * Render the widget.
	 *
	 * @since    1.0.0
	 */
	public function render_widget() {
		// Check global visibility setting (default false = widget hidden until enabled)
		$global_visibility = get_option('aethos_global_visibility', true);
		if (!$global_visibility) {
			return;
		}

		// Check page/post specific visibility
		$object_id = get_queried_object_id();
		
		// Handle empty string values as true (include all by default)
		$include_all_pages_raw = get_option('aethos_include_all_pages', true);
		$include_all_pages = ($include_all_pages_raw === '' || $include_all_pages_raw === null) ? true : (bool) $include_all_pages_raw;
		
		$included_pages = get_option('aethos_included_pages', array());
		if (!is_array($included_pages)) $included_pages = array();
		
		$excluded_pages = get_option('aethos_excluded_pages', array());
		if (!is_array($excluded_pages)) $excluded_pages = array();

		// If specific inclusion is required and current page is not included
		if (!$include_all_pages && !in_array($object_id, $included_pages)) {
			return;
		}

		// If page is specifically excluded
		if (in_array($object_id, $excluded_pages)) {
			return;
		}

		// Check category/tag visibility
		if (!$this->check_category_visibility($object_id)) {
			return;
		}

		// Check WooCommerce visibility if active
		if (class_exists('WooCommerce') && (is_product() || is_product_category())) {
			if (!$this->check_woocommerce_visibility($object_id)) {
				return;
			}
		}

		// Check Custom Post Type visibility
		$current_post_type = get_post_type($object_id);
		if ($current_post_type && !in_array($current_post_type, array('page', 'post', 'product'))) {
			if (!$this->check_cpt_visibility($object_id, $current_post_type)) {
				return;
			}
		}

		// Get API key and endpoint
		$api_key = get_option( 'aethos_api_key' );
		
		// Use Aethos_API class to get endpoint with proper fallback
		$api = new Aethos_API();
		$api_endpoint = $api->get_api_endpoint();
		
		// If no API key, don't render
		if ( empty( $api_key ) ) {
			return;
		}

		// Get site ID (using the API key as the site identifier for now)
		$site_id = $api_key;

		// Generate a secure init token
		$token_generator = new Aethos_Token_Generator();
		$init_token = $token_generator->generate_init_token();
		
		// Output the widget configuration and load React bundle from SaaS
		?>
		<div id="aethos-widget-root"></div>
		<!-- Load widget CSS from SaaS (includes Tailwind + Reachat styles) -->
		<link rel="stylesheet" href="<?php echo esc_url($api_endpoint . '/widget-bundle.css'); ?>" />
		<script>
			window.__AETHOS_WIDGET_CONFIG__ = {
				siteId: <?php echo json_encode($site_id); ?>,
				initToken: <?php echo json_encode($init_token); ?>,
				origin: <?php echo json_encode(home_url()); ?>,
				saasUrl: <?php echo json_encode($api_endpoint); ?>,
				wpContextUrl: <?php echo json_encode(rest_url('aethos/v1/context')); ?>,
				containerId: 'aethos-widget-root',
				config: {
					// These will be fetched from SaaS during init
					// But we can pass initial values if available
				}
			};
		</script>
		<script src="<?php echo esc_url($api_endpoint . '/widget-bundle.js'); ?>" defer></script>
		<?php
	}

	/**
	 * Check if widget should be visible based on category settings
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    The current post ID.
	 * @return   bool               True if visible, false otherwise.
	 */
	private function check_category_visibility($post_id) {
		if (!is_single($post_id)) {
			return true;
		}

		$include_all_categories = get_option('aethos_include_all_categories', true);
		$included_categories = get_option('aethos_included_categories', array());
		$excluded_categories = get_option('aethos_excluded_categories', array());

		$post_categories = wp_get_post_categories($post_id);

		// If specific inclusion is required
		if (!$include_all_categories) {
			$has_included_category = false;
			foreach ($post_categories as $cat_id) {
				if (in_array($cat_id, $included_categories)) {
					$has_included_category = true;
					break;
				}
			}
			if (!$has_included_category) {
				return false;
			}
		}

		// Check exclusions
		foreach ($post_categories as $cat_id) {
			if (in_array($cat_id, $excluded_categories)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if widget should be visible based on WooCommerce settings
	 *
	 * @since    1.0.0
	 * @param    int    $object_id    The current object ID.
	 * @return   bool                 True if visible, false otherwise.
	 */
	private function check_woocommerce_visibility($object_id) {
		// Product Pages
		if (is_product()) {
			$include_all_products = get_option('aethos_include_all_woo_products', true);
			$included_products = get_option('aethos_included_woo_products', array());
			$excluded_products = get_option('aethos_excluded_woo_products', array());

			if (!$include_all_products && !in_array($object_id, $included_products)) {
				return false;
			}

			if (in_array($object_id, $excluded_products)) {
				return false;
			}

			// Check product categories
			return $this->check_woo_category_visibility($object_id);
		}

		// Product Category Pages
		if (is_product_category()) {
			$term = get_queried_object();
			$term_id = $term->term_id;

			$include_all_cats = get_option('aethos_include_all_woo_categories', true);
			$included_cats = get_option('aethos_included_woo_categories', array());
			$excluded_cats = get_option('aethos_excluded_woo_categories', array());

			if (!$include_all_cats && !in_array($term_id, $included_cats)) {
				return false;
			}

			if (in_array($term_id, $excluded_cats)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check WooCommerce category visibility for a product
	 *
	 * @since    1.0.0
	 * @param    int    $product_id    The product ID.
	 * @return   bool                  True if visible, false otherwise.
	 */
	private function check_woo_category_visibility($product_id) {
		$include_all_cats = get_option('aethos_include_all_woo_categories', true);
		$included_cats = get_option('aethos_included_woo_categories', array());
		$excluded_cats = get_option('aethos_excluded_woo_categories', array());

		$product_cats = wc_get_product_term_ids($product_id, 'product_cat');

		if (!$include_all_cats) {
			$has_included = false;
			foreach ($product_cats as $cat_id) {
				if (in_array($cat_id, $included_cats)) {
					$has_included = true;
					break;
				}
			}
			if (!$has_included) {
				return false;
			}
		}

		foreach ($product_cats as $cat_id) {
			if (in_array($cat_id, $excluded_cats)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if widget should be visible based on Custom Post Type settings
	 *
	 * @since    1.0.0
	 * @param    int       $post_id      The current post ID.
	 * @param    string    $post_type    The post type slug.
	 * @return   bool                    True if visible, false otherwise.
	 */
	private function check_cpt_visibility($post_id, $post_type) {
		$include_all = get_option("aethos_include_all_{$post_type}", true);
		$included = get_option("aethos_included_{$post_type}", array());
		$excluded = get_option("aethos_excluded_{$post_type}", array());

		// If specific inclusion is required and current post is not included
		if (!$include_all && !in_array($post_id, $included)) {
			return false;
		}

		// If post is specifically excluded
		if (in_array($post_id, $excluded)) {
			return false;
		}

		return true;
	}

}
