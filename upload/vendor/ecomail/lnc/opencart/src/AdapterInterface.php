<?php
    
     /**
     * @author Jan Cinert
     */

    namespace LNC\OpenCart;

    interface AdapterInterface {

        public function loadLanguage( $arg1 );

        public function setCurrency( $arg1 );

        public function redirect( $arg1 );

        public function render( $tpl, $data );

        public function defaultChildControllers( & $data );

    }
