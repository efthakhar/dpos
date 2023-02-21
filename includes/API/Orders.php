<?php

namespace DPOS\API;

use DPOS\Traits\Singleton;

class Orders
{

    use Singleton;

    function __construct()
    {
        add_action('rest_api_init', [$this, 'rest_routes']);
    }

    public function rest_routes()
    {
        register_rest_route('dpos/v1', 'orders', [
            [
                'methods' => 'POST',
                'callback' => [$this, 'create_order'],
                //'permission_callback' => [$this, 'create_order_permission'],
            ]
        ]);
    }

    function create_order($request)
    {

        $products = $request->get_param( 'cart_products' );
        
        $order = wc_create_order();

        //$order->set_customer_id( 1 );

        foreach($products as $product)
        {
            $order->add_product( wc_get_product($product['id']), $product['quantitycd']);
        }   
       

        $order->calculate_totals(); 
        $order->update_status("completed", 'Imported order', TRUE);
        $order->save();

        wp_send_json_success($order);
    }
}