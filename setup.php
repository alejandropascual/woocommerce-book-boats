<?php
/**
 * Plugin Name: Woocommerce Book Boats
 * Description: Interface for Booking Boats
 * Version: 1.00
 * Released: October, 2015
 * Author: Alejandro Pascual
 * Author URI: http://www.elapsl.com
 * License: GPL2
 * Text Domain: aps-book-boats
 * Domain Path: /lang
 **/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


function aps_is_woocommerce_active() {
    $plugins = (array) get_option( 'active_plugins', array() );
    if ( is_multisite() )
        $plugins = array_merge( $plugins, get_site_option( 'active_sitewide_plugins', array() ) );
    return in_array( 'woocommerce/woocommerce.php', $plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $plugins );
}

if ( aps_is_woocommerce_active() ) {

    class WC_Bookboats {

        public function __construct() {
            define( 'WC_BOOKBOATS_VERSION', '1.0.0' );
            define( 'WC_BOOKBOATS_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
            define( 'WC_BOOKBOATS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
            define( 'WC_BOOKBOATS_MAIN_FILE', __FILE__ );

            if ( ! defined( 'ABB_PLUGIN_DIR' ) ) { define( 'ABB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); }
            if ( ! defined( 'ABB_PLUGIN_URL' ) ) { define( 'ABB_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); }

            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
            add_action( 'woocommerce_loaded', array( $this, 'includes' ) );
            add_action( 'plugins_loaded', array( $this, 'init' ) );
            add_action( 'init', array( $this, 'init_post_types' ) );

            if ( is_admin() ) {
                $this->admin_includes();
            }
        }

        public function load_plugin_textdomain() {
            $locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-bookboats' );
            $dir    = trailingslashit( WP_LANG_DIR );

            load_textdomain( 'woocommerce-bookboats', $dir . 'woocommerce-bookings/woocommerce-bookboats-' . $locale . '.mo' );
            load_plugin_textdomain( 'woocommerce-bookboats', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        public function init() {
            include( 'includes/class-wc-bookboats-cart-manager.php' );
            include( 'includes/class-wc-bookboats-checkout-manager.php' );
        }

        public function includes() {

            include( 'includes/wc-bookboats-functions.php' );
            include( 'includes/class-wc-bookboats.php' );
            include( 'includes/class-wc-bookboats-controller.php' );
            include( 'includes/bookboats-form/class-wc-bookboats-form.php' );
            require_once ABB_PLUGIN_DIR . 'includes/admin/class-wc-bookboats-ajax.php';

            // Products
            include( 'includes/class-wc-product-bookboats.php' );
        }

        public function admin_includes() {
            require_once ABB_PLUGIN_DIR . 'includes/admin/class-wc-bookboats-admin.php';
            //require_once ABB_PLUGIN_DIR . 'includes/admin/class-wc-bookboats-ajax.php';
        }

        public function init_post_types() {

            register_post_type( 'wc_bookboat',
                apply_filters( 'woocommerce_register_post_type_wc_bookingboat',
                    array(
                        'label'  => __( 'Booking Boats', 'woocommerce-bookboats' ),
                        'labels' => array(
                            'name'               => __( 'Bookings Boats', 'woocommerce-bookboats' ),
                            'singular_name'      => __( 'Booking', 'woocommerce-bookboats' ),
                            'add_new'            => __( 'Add Booking', 'woocommerce-bookboats' ),
                            'add_new_item'       => __( 'Add New Booking', 'woocommerce-bookboats' ),
                            'edit'               => __( 'Edit', 'woocommerce-bookboats' ),
                            'edit_item'          => __( 'Edit Booking', 'woocommerce-bookboats' ),
                            'new_item'           => __( 'New Booking', 'woocommerce-bookboats' ),
                            'view'               => __( 'View Booking', 'woocommerce-bookboats' ),
                            'view_item'          => __( 'View Booking', 'woocommerce-bookboats' ),
                            'search_items'       => __( 'Search Bookings', 'woocommerce-bookboats' ),
                            'not_found'          => __( 'No Bookings found', 'woocommerce-bookboats' ),
                            'not_found_in_trash' => __( 'No Bookings found in trash', 'woocommerce-bookboats' ),
                            'parent'             => __( 'Parent Bookings', 'woocommerce-bookboats' ),
                            'menu_name'          => _x( 'Bookings Boats', 'Admin menu name', 'woocommerce-bookboats' ),
                            'all_items'          => __( 'All Bookings', 'woocommerce-bookboats' ),
                        ),
                        'description' 			=> __( 'This is where bookings are stored.', 'woocommerce-bookboats' ),
                        'public' 				=> false,
                        'show_ui' 				=> true,
                        'capability_type' 		=> 'product',
                        'map_meta_cap'			=> true,
                        'publicly_queryable' 	=> false,
                        'exclude_from_search' 	=> true,
                        'show_in_menu' 			=> true,
                        'hierarchical' 			=> false,
                        'show_in_nav_menus' 	=> false,
                        'rewrite' 				=> false,
                        'query_var' 			=> false,
                        'supports' 				=> array( '' ),
                        'has_archive' 			=> false,
                    )
                )
            );
        }

    }

    $GLOBALS['wc_bookboats'] = new WC_Bookboats();
}


/*
define( 'WC_BOOKBOATS_VERSION', '1.0.0' );
define( 'WC_BOOKBOATS_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
define( 'WC_BOOKBOATS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'WC_BOOKBOATS_MAIN_FILE', __FILE__ );

if ( ! class_exists( 'ABB' ) ) :

    final class ABB {

        private static $singleton;

        public $settings;

        public static function singleton() {

            if ( !isset( self::$singleton ) && !( self::$singleton instanceof ABB ) ) {

                self::$singleton = new ABB;
                self::$singleton->setup_constants();
                add_action( 'plugins_loaded', array(self::$singleton, 'load_textdomain' ) );
                self::$singleton->includes();
            }
            return self::$singleton;
        }

        public function __clone() {
            // Cloning is not allowed
            _doing_it_wrong( __FUNCTION__, __( 'Clone is not allowed', 'abb' ), '1.0' );
        }

        public function __wakeup() {
            // Unserializing is not allowd
            _doing_it_wrong( __FUNCTION__, __( 'Unserializing is not allowed', 'abb' ), '1.0' );
        }

        private function setup_constants() {

            // Folder Path
            if ( ! defined( 'ABB_PLUGIN_DIR' ) ) {
                define( 'ABB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }

            // Folder URL
            if ( ! defined( 'ABB_PLUGIN_URL' ) ) {
                define( 'ABB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
        }

        private function includes() {

            // General
            //require_once ABB_PLUGIN_DIR . 'includes/shortcode.php';
            //require_once ABB_PLUGIN_DIR . 'includes/scripts-front.php';
            //require_once ABB_PLUGIN_DIR . 'includes/functions.php';
            //require_once ABB_PLUGIN_DIR . 'includes/ajax-actions.php';

            // Products
            require_once ABB_PLUGIN_DIR . 'includes/class-wc-product-bookboats.php';

            // Admin
            if ( is_admin() ) {
                require_once ABB_PLUGIN_DIR . 'includes/admin/class-wc-book-boats-admin.php';
                //require_once ABB_PLUGIN_DIR . 'includes/admin/metaboxes.php';
            }

        }

        public function load_textdomain() {
            load_plugin_textdomain( 'abb', false, plugin_basename( dirname( __FILE__ ) ) . "/languages/" );
        }
    }
endif;


function ABB() {
    return ABB::singleton();
}

// Create singleton
ABB();
*/

