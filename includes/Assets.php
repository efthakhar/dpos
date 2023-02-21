<?php


namespace DPOS;

use DPOS\Traits\Singleton;

class Assets{
    
     use Singleton;

    function __construct()
    {
        add_action( 'admin_enqueue_scripts', [$this,'load_assets'] );
        add_filter( 'script_loader_tag', [$this,'filter_script'], 10, 3 );
    }

    function load_assets($hook)
    {
	
        if( $hook != 'toplevel_page_dpos' ) 
        {
             return;
        }
        wp_enqueue_style( 'dpos_main_css',  DPOS_DIR.'vuejs/dist/index.css' );
        wp_enqueue_script('dpos_main_js', DPOS_DIR.'vuejs/dist/index.js' ); 
        
        wp_localize_script('dpos_main_js','dposApi',
            [
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' )
            ]
        );
    }

    
    function filter_script( $tag, $handle, $source ) 
    {

        if ( 'dpos_main_js' === $handle ) {
            $tag = '<script type="module" crossorigin src="' . $source . '" ></script>';
        }
         
        return $tag;
    }

    
}

    


?>