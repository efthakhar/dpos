<?php

namespace DPOS;

use DPOS\Traits\Singleton;

class Pages{

    use Singleton;

    function __construct()
    {
        add_action( 'admin_menu', [$this,'dpos_register_admin_pages'] );
    }


    function dpos_register_admin_pages() 
    {
        add_menu_page(
            __( 'dpos', 'dpos' ),
            __( 'DPOS', 'dpos' ),
            'manage_options',
            'dpos',
            [$this,'dpos_admin_page_contents'],
            'dashicons-superhero',
            1
        );

        add_submenu_page(
            'dpos',
            __( 'home', 'dpos' ),
            __( 'Home', 'dpos' ),
            'manage_options',
            'admin.php?page=dpos#/',
            NULL
        );

        add_submenu_page(
            'dpos',
            __( 'pos', 'dpos' ),
            __( 'POS', 'woo-necessary-pdf' ),
            'manage_options',
            'admin.php?page=dpos#/pos',
            NULL
        );

         remove_submenu_page('dpos', 'dpos');
    }


    function dpos_admin_page_contents() 
    {
        
        ?>
            <div id="app"></div>
        <?php
    
    }




}

    


?>