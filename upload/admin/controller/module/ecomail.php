<?php

    class ControllerModuleEcomail extends Controller {

        private $error = array();

        public function index() {

            require_once( constant( 'DIR_SYSTEM' ) . '../vendor/ecomail/autoload.php' );

            $factory = new LNC\OpenCart\Factory();
            $adapter = $factory->factoryAdapter( $this );

            $adapter->loadLanguage( 'module/ecomail' );

            $this->document->setTitle( $this->language->get( 'heading_title' ) );
            $this->document->addScript( 'view/javascript/ecomail/back.js' );

            $this->load->model( 'setting/setting' );

            if( ( $this->request->server['REQUEST_METHOD'] == 'POST' ) && $this->validate() ) {
                $this->model_setting_setting->editSetting(
                        'ecomail',
                        $this->request->post
                );

                $this->session->data['success'] = $this->language->get( 'text_success' );

                $adapter->redirect(
                        $this->url->link(
                                'extension/module',
                                'token=' . $this->session->data['token'],
                                true
                        )
                );
            }

            $data['heading_title'] = $this->language->get( 'heading_title' );

            $data['text_edit']     = $this->language->get( 'text_edit' );
            $data['text_enabled']  = $this->language->get( 'text_enabled' );
            $data['text_disabled'] = $this->language->get( 'text_disabled' );
            $data['text_import']   = $this->language->get( 'text_import' );

            $data['entry_api_key'] = $this->language->get( 'entry_api_key' );
            $data['entry_list_id'] = $this->language->get( 'entry_list_id' );
            $data['entry_app_id']  = $this->language->get( 'entry_app_id' );
            $data['entry_status']  = $this->language->get( 'entry_status' );

            $data['button_import']       = $this->language->get( 'button_import' );
            $data['button_save']         = $this->language->get( 'button_save' );
            $data['button_cancel']       = $this->language->get( 'button_cancel' );
            $data['button_category_add'] = $this->language->get( 'button_category_add' );

            if( isset( $this->error['warning'] ) ) {
                $data['error_warning'] = $this->error['warning'];
            }
            else {
                $data['error_warning'] = '';
            }

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                    'text' => $this->language->get( 'text_home' ),
                    'href' => $this->url->link(
                            'common/dashboard',
                            'token=' . $this->session->data['token'],
                            true
                    )
            );

            $data['breadcrumbs'][] = array(
                    'text' => $this->language->get( 'text_module' ),
                    'href' => $this->url->link(
                            'extension/module',
                            'token=' . $this->session->data['token'],
                            true
                    )
            );

            $data['breadcrumbs'][] = array(
                    'text' => $this->language->get( 'heading_title' ),
                    'href' => $this->url->link(
                            'module/ecomail',
                            'token=' . $this->session->data['token'],
                            true
                    )
            );

            $data['action'] = $this->url->link(
                    'module/ecomail',
                    'token=' . $this->session->data['token'],
                    true
            );

            $data['cancel'] = $this->url->link(
                    'extension/module',
                    'token=' . $this->session->data['token'],
                    true
            );

            $data['token'] = $this->session->data['token'];

            $settings = $this->model_setting_setting->getSetting(
                    'ecomail'
            );
            foreach(
                    array(
                            'api_key',
                            'list_id',
                            'app_id',
                            'status'
                    ) as $item
            ) {
                $item = 'ecomail_' . $item;
                if( isset( $this->request->post[$item] ) ) {
                    $data[$item] = $this->request->post[$item];
                }
                else {
                    $data[$item] = isset( $settings[$item] )
                            ? $settings[$item]
                            : null;
                }
            }

            $this->load->model( 'ecomail/optionsListId' );

            $this->factoryHelper();

            $data['optionsListId'] = $this->model_ecomail_optionsListId->getOptions();

            $adapter->defaultChildControllers( $data );

            $html = <<<HTML
<script type="text/javascript">
    EcomailBackOffice.init({1});
</script>
HTML;

            $html = strtr(
                    $html,
                    array(
                            '{1}' => json_encode(
                                    array(
                                            'formFieldAPIKey' => 'input-ecomail_api_key',
                                            'formFieldList'   => 'input-ecomail_list_id',
                                            'ajaxUrl'         => html_entity_decode(
                                                    $this->url->link(
                                                            'module/ecomail/autocomplete',
                                                            'token=' . $this->session->data['token'],
                                                            true
                                                    )
                                            ),
                                            'templates'       => array(
                                                    'connect' => <<<HTML
<div class="form-group">
  <label class="control-label col-lg-2"></label>
  <div class="col-lg-8 ">
    <input type="submit" value="Připojit" id="{BUTTON_CONNECT}" class="btn">
  </div>
</div>
HTML
                                            )
                                    )
                            )
                    )
            );

            $data['footer'] = $html . $data['footer'];

            $this->response->setOutput(
                    $adapter->render(
                            'module/ecomail',
                            $data
                    )
            );
        }

        public function autocomplete() {

            $this->factoryHelper();

            $result = array();

            $cmd = $this->request->get['cmd'];
            if( $cmd == 'getLists' ) {

                $APIKey = $this->request->get['APIKey'];
                if( $APIKey ) {
                    $listsCollection = $this->ecomail_helper->getAPI()
                                                            ->setAPIKey( $APIKey )
                                                            ->getListsCollection();
                    if( $listsCollection ) {
                        foreach( $listsCollection as $list ) {
                            $result[] = array(
                                    'id'   => $list->id,
                                    'name' => $list->name
                            );
                        }
                    }
                }

            }


            $this->response->addHeader( 'Content-Type: application/json' );
            $this->response->setOutput( json_encode( $result ) );
        }

        protected function validate() {
            if( !$this->user->hasPermission(
                    'modify',
                    'module/ecomail'
            )
            ) {
                $this->error['warning'] = $this->language->get( 'error_permission' );
            }

            return !$this->error;
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

        public function install() {
            // Register the event triggers
            $this->load->model( 'extension/event' );

            $this->model_extension_event->addEvent(
                    __CLASS__,
                    'catalog/controller/common/footer/after',
                    'module/ecomail/onCommonFooterAfter'
            );
            $this->model_extension_event->addEvent(
                    __CLASS__,
                    'catalog/model/checkout/order/addOrder/after',
                    'module/ecomail/eventAddOrder'
            );
            $this->model_extension_event->addEvent(
                    __CLASS__,
                    'catalog/controller/checkout/cart/add/after',
                    'module/ecomail/onCheckoutCartAddAfter'
            );
            $this->model_extension_event->addEvent(
                    __CLASS__,
                    'catalog/model/account/customer/addCustomer/after',
                    'module/ecomail/eventAddCustomer'
            );
            $this->model_extension_event->addEvent(
                    __CLASS__,
                    'catalog/model/account/customer/editNewsletter/after',
                    'module/ecomail/eventEditNewsletter'
            );
        }

        public function uninstall() {
            // delete the event triggers
            $this->load->model( 'extension/event' );

            $this->model_extension_event->deleteEvent( __CLASS__ );
        }

    }
