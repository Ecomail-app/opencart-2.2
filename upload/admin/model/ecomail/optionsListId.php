<?php

    class ModelEcomailOptionsListId extends Model {

        public function getOptions() {

            $options = array();

            if( $this->ecomail_helper->getConfigValue( 'api_key' ) ) {
                $listsCollection = $this->ecomail_helper->getAPI()
                                                        ->getListsCollection();


                foreach( $listsCollection as $list ) {
                    $options[] = array(
                            'value' => $list->id,
                            'label' => $list->name
                    );
                }
            }

            return $options;

        }

    }