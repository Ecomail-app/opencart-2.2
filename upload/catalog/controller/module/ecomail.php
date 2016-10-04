<?php

    class ControllerModuleEcomail extends Controller {

        public function eventAddCustomer( $route, $customer_id, $data ) {
            
            $this->factoryHelper();

            if( $data['newsletter'] ) {
                if( $this->ecomail_helper->getConfigValue( 'api_key' ) ) {

                    $email = $data['email'];
                    $name  = array();
                    foreach(
                            array(
                                    $data['firstname'],
                                    $data['lastname']
                            ) as $v
                    ) {
                        if( $v ) {
                            $name[] = $v;
                        }
                    }
                    $name = implode(
                            ' ',
                            $name
                    );

                    $this->ecomail_helper->getApi()
                                         ->subscribeToList(
                                                 $this->ecomail_helper->getConfigValue( 'list_id' ),
                                                 array(
                                                         'email' => $email,
                                                         'name'  => $name
                                                 )
                                         );
                }
            }

        }

        public function eventEditNewsletter( $route, $result, $newsletter ) {
            
            $this->factoryHelper();

            if( $newsletter && !$this->customer->getNewsletter() ) {
                if( $this->ecomail_helper->getConfigValue( 'api_key' ) ) {

                    $email = $this->customer->getEmail();
                    $name  = array();
                    foreach(
                            array(
                                    $this->customer->getFirstName(),
                                    $this->customer->getLastName()
                            ) as $v
                    ) {
                        if( $v ) {
                            $name[] = $v;
                        }
                    }
                    $name = implode(
                            ' ',
                            $name
                    );

                    $this->ecomail_helper->getApi()
                                         ->subscribeToList(
                                                 $this->ecomail_helper->getConfigValue( 'list_id' ),
                                                 array(
                                                         'email' => $email,
                                                         'name'  => $name
                                                 )
                                         );
                }
            }

        }

        public function eventAddOrder( $route, $order_id ) {
            $this->load->model( 'account/order' );
            $this->load->model( 'checkout/order' );
            $this->load->model( 'catalog/product' );
            $this->load->model( 'catalog/category' );

            $order = $this->model_checkout_order->getOrder( $order_id );

            $this->factoryHelper();

            $orderProducts = $this->model_account_order->getOrderProducts( $order_id );
            $totals        = $this->model_account_order->getOrderTotals( $order_id );

            $tax      = 0;
            $shipping = 0;
            foreach( $totals as $total ) {
                if( $total['code'] == 'tax' ) {
                    $tax += $total['value'];
                }
                elseif( $total['code'] == 'shipping' ) {
                    $shipping += $total['value'];
                }
            }

            $arr = array();
            foreach( $orderProducts as $orderProduct ) {
                $product    = $this->model_catalog_product->getProduct( $orderProduct['product_id'] );
                $categories = $this->model_catalog_product->getCategories( $orderProduct['product_id'] );

                $category_info = null;
                if( count( $categories ) ) {
                    $categoryId    = $categories[0]['category_id'];
                    $category_info = $this->model_catalog_category->getCategory( $categoryId );
                }

                if( empty( $orderProduct['price'] ) ) {
                    continue;
                }

                $arr[] = array(
                        'code'      => $orderProduct['model'],
                        'title'     => $orderProduct['name'],
                        'category'  => $category_info
                                ? html_entity_decode( $category_info['name'] )
                                : null,
                        'price'     => $orderProduct['price'],
                        'amount'    => $orderProduct['quantity'],
                        'timestamp' => strtotime( $order['date_added'] )
                );
            }

            $data = array(
                    'transaction'       => array(
                            'order_id'  => $order['order_id'],
                            'email'     => $order['email'],
                            'shop'      => $order['store_url'],
                            'amount'    => $order['total'] - $tax,
                            'tax'       => $tax,
                            'shipping'  => $shipping,
                            'city'      => $order['shipping_method']
                                    ? $order['shipping_city']
                                    : $order['payment_city'],
                            'county'    => $order['shipping_method']
                                    ? $order['shipping_zone_code']
                                    : $order['payment_zone_code'],
                            'country'   => $order['shipping_method']
                                    ? $order['shipping_iso_code_2']
                                    : $order['payment_iso_code_2'],
                            'timestamp' => strtotime( $order['date_added'] )
                    ),
                    'transaction_items' => $arr
            );

            $r = $this->ecomail_helper->getApi()
                                      ->createTransaction( $data );
        }

        public function onCommonFooterAfter( &$route, &$data, &$output ) {

            $this->factoryHelper();
            $appId = $this->ecomail_helper->getConfigValue( 'app_id' );

            if( $appId ) {

                $this->document->addScript( 'catalog/view/javascript/ecomail/front.js' );

                $basePath = $this->getBasePath();

                $html = <<<HTML
<script type="text/javascript">
    EcomailFront.init({1});
</script>
HTML;

                $html = strtr(
                        $html,
                        array(
                                '{1}' => json_encode(
                                        array(
                                                'basePath'                   => $basePath,
                                                'cookieNameTrackStructEvent' => $this->ecomail_helper->getCookieNameTrackStructEvent(
                                                )
                                        )
                                )
                        )
                );

                $output .= $html;

                $html = <<<HTML
                
<!-- Ecomail starts -->
<script type="text/javascript">
;(function(p,l,o,w,i,n,g){if(!p[i]){p.GlobalSnowplowNamespace=p.GlobalSnowplowNamespace||[];
p.GlobalSnowplowNamespace.push(i);p[i]=function(){(p[i].q=p[i].q||[]).push(arguments)
};p[i].q=p[i].q||[];n=l.createElement(o);g=l.getElementsByTagName(o)[0];n.async=1;
n.src=w;g.parentNode.insertBefore(n,g)}}(window,document,"script","//d1fc8wv8zag5ca.cloudfront.net/2.4.2/sp.js","ecotrack"));
window.ecotrack('newTracker', 'cf', 'd2dpiwfhf3tz0r.cloudfront.net', {1});
window.ecotrack('setUserIdFromLocation', 'ecmid');
window.ecotrack('trackPageView');
</script>
<!-- Ecomail stops -->
HTML;

                $html = strtr(
                        $html,
                        array(
                                '{1}' => json_encode(
                                        array(
                                                'appId' => $appId
                                        )
                                )
                        )
                );

                $output .= $html;
            }

        }

        public function onCheckoutCartAddAfter( &$route, &$data, &$output ) {

            $this->factoryHelper();

            $id_product = $this->request->post['product_id'];

            $this->load->model( 'catalog/product' );

            $product_info = $this->model_catalog_product->getProduct( $id_product );

            if( $product_info ) {
                if( isset( $this->request->post['quantity'] ) && ( (int)$this->request->post['quantity'] >= $product_info['minimum'] ) ) {
                    $quantity = (int)$this->request->post['quantity'];
                }
                else {
                    $quantity = $product_info['minimum']
                            ? $product_info['minimum']
                            : 1;
                }

                $basePath = $this->getBasePath();

                setcookie(
                        $this->ecomail_helper->getCookieNameTrackStructEvent(),
                        json_encode(
                                array(
                                        'category' => 'Product',
                                        'action'   => 'AddToCart',
                                        'tag'      => implode(
                                                '|',
                                                array(
                                                        $id_product
                                                )
                                        ),
                                        'property' => 'quantity',
                                        'value'    => $quantity
                                )
                        ),
                        null,
                        $basePath
                );

            }
        }

        protected function getBasePath() {

            if( $this->request->server['HTTPS'] ) {
                $server = $this->config->get( 'config_ssl' );
            }
            else {
                $server = $this->config->get( 'config_url' );
            }
            return rtrim(
                    parse_url(
                            $server,
                            PHP_URL_PATH
                    ),
                    '/'
            );

        }

        protected function factoryHelper() {
            require_once DIR_SYSTEM . '../lib/ecomail/helper.php';
            $helper = new EcomailHelper();
            $helper->setConfig( $this->config );
            $this->registry->set(
                    'ecomail_helper',
                    $helper
            );
        }

    }
    