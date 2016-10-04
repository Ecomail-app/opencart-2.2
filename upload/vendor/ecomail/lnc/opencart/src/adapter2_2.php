<?php
    
     /**
     * @author Jan Cinert
     */

    namespace LNC\OpenCart;

    class Adapter2_2 implements AdapterInterface {

        protected $controller;

        public function __construct( \Controller $controller ) {
            $this->controller = $controller;
        }

        public function loadLanguage( $arg1 ) {

            $this->controller->load->language( $arg1 );

        }
        
        public function setCurrency( $arg1 ) {
            
            $this->controller->session->data['currency'] = $arg1;
            
        }

        public function redirect( $arg1 ) {

            $this->controller->response->redirect( $arg1 );

        }

        public function render( $tpl, $data ) {

            return $this->controller->load->view(
                    $tpl,
                    $data
            );

        }

        public function defaultChildControllers( & $data ) {
            
            $data['header']      = $this->controller->load->controller( 'common/header' );
            $data['column_left'] = $this->controller->load->controller( 'common/column_left' );
            $data['footer']      = $this->controller->load->controller( 'common/footer' );
            
        }

    }