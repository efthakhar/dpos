<?php
/**
 * Plugin Name: dpos woo point of sale plugin
 * Plugin URI: 
 * Description: WooCommerce Pos Plugin
 * Author: Efthakhakhar Bin Alam Dihab
 * Author URI: https://github.com/efthakhar
 * Version: 1.0.0
 * Text Domain: dpos
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

use DPOS\API\Customers;
use DPOS\API\Orders;
use DPOS\API\Products;
use DPOS\Assets;
use DPOS\Pages;
use DPOS\Traits\Singleton;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';


class DPOS{

    use Singleton;

    function __construct()
    {   
        $this->define_constants();
    
		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }


    public function define_constants() 
    {
		define('DPOS', __FILE__ );
        define('DPOS_DIR',plugin_dir_url(__FILE__));
	}

    public function activate()
    {
        
    }

    public function dectivate()
    {
        
    }

    public function init_plugin() {
		$this->includes();
        $this->init_classes();
		//$this->init_hooks();

		do_action( 'dpos_loaded' );
	}

    public function includes()
    {

    }

    public function init_classes()
    {
        Pages::instance();
        Assets::instance();
        
        //APis
        Products::instance();
        Orders::instance();
        Customers::instance();
    }


}


DPOS::instance();  

     
add_filter( 'woocommerce_product_data_store_cpt_get_products_query', 'handle_custom_query_var', 10, 2 );
function handle_custom_query_var( $query, $query_vars ) 
{
    if ( isset( $query_vars['like_name'] ) && ! empty( $query_vars['like_name'] ) ) {
        $query['s'] = esc_attr( $query_vars['like_name'] );
    }
    return $query;
}