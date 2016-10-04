<?php

    /**
     * @author Jan Cinert
     */

    namespace LNC\OpenCart;

    class Adapter implements AdapterInterface {

        protected $controller;

        public function __construct( \Controller $controller ) {
            $this->controller = $controller;
        }

        public function loadLanguage( $arg1 ) {

            $this->controller->language->load( $arg1 );

        }
        
        public function setCurrency( $arg1 ) {

            $this->controller->currency->set( $arg1 );
            
        }

        public function redirect( $arg1 ) {

            $this->controller->redirect( $arg1 );

        }

        public function render( $tpl, $data ) {

            $this->controller->template = $tpl;
            $this->controller->data     = $data;

            return $this->controller->render();

        }

        public function defaultChildControllers( & $data ) {
            
            $this->controller->children = array(
                    'common/header',
                    'common/footer'
            );
            
        }

    }