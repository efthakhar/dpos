<?php

namespace DPOS\API;

use DPOS\Traits\Singleton;

class Products
{

    use Singleton;

    function __construct()
    {
        add_action('rest_api_init', [$this, 'rest_routes']);
    }

    public function rest_routes()
    {
        register_rest_route('dpos/v1', 'products-by-search', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_products_by_search'],
                //'permission_callback' => [$this, 'get_per'],
            ]
        ]);
        register_rest_route('dpos/v1', 'products', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_products'],
                //'permission_callback' => [$this, 'get_per'],
            ]
        ]);
    }

    function get_products_by_search($request)
    {
        
        $product_query_string = $request->get_param( 'query_string' );

        $args =  ['like_name' => $product_query_string,];
           
        $products = wc_get_products($args);
        $pl = [];
        foreach ($products as $product) {
            $pl[] = [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'image' => wp_get_attachment_url($product->get_image_id()) ,
                'stock_quantity' => $product->get_stock_quantity(),
                'stock_status' =>$product->get_stock_status()
            ];
        }
        return  wp_send_json_success($pl);
    }


    function get_products($request)
    {
        // $args = ['limit' => 10, 'paginate' => true];
        // return $wc_products = wc_get_products( $args );




        $param = $request->get_params();

        $page = isset($param['page']) ? $param['page']:1;
        $categories  = isset($param['categories']) ? json_decode($param['categories']):[];
        $tags = isset($param['tags']) ? json_decode($param['tags']):[];

        $args = 
        [
                'status'            => array('draft', 'pending', 'private', 'publish'),
                'type'              => array_merge(array_keys(wc_get_product_types())),
                'parent'            => null,
                //'sku'               => '',
                'category'          => $categories,
                'tag'               => $tags,
                'limit'             =>  6 ,
                //'offset'            => null,
                'page'              => $page,
                'include'           => array(),
                'exclude'           => array(),
                'orderby'           => 'date',
                'order'             => 'DESC',
                'return'            => 'objects',
                'paginate' => true,
                'shipping_class'    => array(),
        ];
           
        $result = wc_get_products($args);
        $fetchedProducts = $result->products;
        $products = [];
        
        foreach ($fetchedProducts as $product) {
            $products[] = [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'image' => wp_get_attachment_url($product->get_image_id()),
                'stock_quantity' => $product->get_stock_quantity(),
                'stock_status' =>$product->get_stock_status()
            ];
        }

        return  wp_send_json_success([
                    'products'=>$products,
                    'total'=>$result->total,
                    'max_page'=>$result->max_num_pages,
                ]);
    }
}
