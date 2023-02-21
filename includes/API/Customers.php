<?php

namespace DPOS\API;

use DPOS\Traits\Singleton;
use WP_User_Query;
class Customers
{

    use Singleton;

    function __construct()
    {
        add_action('rest_api_init', [$this, 'rest_routes']);
    }

    public function rest_routes()
    {
        
        register_rest_route('dpos/v1', 'customers-by-name', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_customers_by_name'],
                //'permission_callback' => [$this, 'get_per'],
            ]
        ]);
    }

    function get_customers_by_name($request)
    {
        
        $customer_query_string = $request->get_param( 'query_string' );
 
        $query = new WP_User_Query( array(
            'search'         => '*'.esc_attr( $customer_query_string ).'*',
            'search_columns' => array(
                // 'user_login',
                // 'user_nicename',
                'user_email',
                // 'user_url',
            ),
        ) );
         
        $customers = $query->get_results(); 

        foreach ($customers as $customer) {

            $pl[] =
            [
                'id' => $customer->ID,
                'email' => $customer->user_email,
                'name' => $customer->display_name,
            ];
        }
        return  wp_send_json_success($pl);
    }


   
}