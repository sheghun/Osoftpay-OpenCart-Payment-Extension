<?php

/**
 * @author  Oladiran Segun Solomon <sheghunoladiran9@gmail.com>
 * @link    github.com/sheghun
 * Osoftpay Payment Extension
 */

class ControllerExtensionPaymentOsoftpay extends Controller
{
    private $error = [];

    /**
     * Our index contains all the logic
     * We need for Our Admin side
     */
    public function index()
    {
        // Load the language file
        $this->load->language('extension/payment/osoftpay');

        // Set the title
        $this->document->setTitle(
            $this->language->get('heading_title')
        );

        // Load the settings
        $this->load->model('setting/setting');

        /**
         * @method Validate used for validation
         * For validating before saving the file
         */
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting(
                'payment_osoftpay',
                $this->request->post
            );

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect(
                $this->url->link(
                    'marketplace/extension',
                    'user_token=' .
                        $this->session->data['user_token'] .
                        '&type=payment',
                    true
                )
            );

        }

        $data['heading_title'] = $this->language->get('heading_title'); // Osoftpay
        $data['text_enabled'] = $this->language->get('text_enabled'); // Enabled
        $data['text_disabled'] = $this->language->get('text_disabled'); // Disabled
        $data['text_all_zones'] = $this->language->get('text_all_zones'); // All Zones
        $data['text_yes'] = $this->language->get('text_yes'); // Yes
        $data['text_no'] = $this->language->get('text_no'); // No
        $data['text_test'] = $this->language->get('text_test'); // Test
        $data['text_live'] = $this->language->get('text_live'); // Live
        $data['text_edit'] = $this->language->get('text_edit');
        $data['entry_mercid'] = $this->language->get('entry_mercid'); // Merchant ID
        $data['entry_notification_url'] = $this->language->get('entry_notification_url'); // Notification URL
        $data['entry_servicetypeid'] = $this->language->get('entry_servicetypeid'); // Service Type
        $data['entry_test'] = $this->language->get('entry_test'); // Environment
        $data['entry_order_status'] = $this->language->get('entry_order_status'); // Order Status
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone'); // Geo Zone
        $data['entry_status'] = $this->language->get('entry_status'); // Status
        $data['entry_sort_order'] = $this->language->get('entry_sort_order'); // Sort Order
        $data['entry_total'] = $this->language->get('entry_total'); // Total
        $data['help_total'] = $this->language->get('help_total'); // An information For the Total
        $data['help_ipn'] = $this->language->get('help_ipn'); // Information About The Redirect URL
        $data['button_save'] = $this->language->get('button_save'); // Save
        $data['button_cancel'] = $this->language->get('button_cancel'); // Cancel


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link(
                'common/dashboard',
                'user_token=' .
                    $this->session->data['user_token'],
                true
            )
        ];

        $data['breadcrumbs'][] = [
            'text' => 'Extension',
            'href' => $this->url->link(
                'marketplace/extension',
                'user_token=' .
                    $this->session->data['user_token'] .
                    '&type=payment',
                true
            )
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link(
                'extension/payment/osoftpay',
                'user_token' .
                    $this->session->data['user_token'],
                true
            )
        ];

        $data['action'] = $this->url->link(
            'extension/payment/osoftpay',
            'user_token=' .
                $this->session->data['user_token'],
            true
        );

        $data['cancel'] = $this->url->link(
            'marketplace/extension',
            'user_token=' .
                $this->session->data['user_token'] .
                '&type=payment',
            true
        );

        $data['payment_osoftpay_notification_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/osoftpay/notification';

        if (isset($this->request->post['payment_osoftpay_mercid'])) {
            $data['payment_osoftpay_mercid'] = $this->request->post['payment_osoftpay_mercid'];
        } else {
            $data['payment_osoftpay_mercid'] = $this->config->get('payment_osoftpay_mercid');
        }

        if (isset($this->request->post['payment_osoftpay_paymentItemName'])) {
            $data['payment_osoftpay_paymentItemName'] = $this->request->post['payment_osoftpay_paymentItemName'];
        } else {
            $data['payment_osoftpay_paymentItemName'] = $this->config->get('payment_osoftpay_paymentItemName');
        }

        if (isset($this->request->post['payment_osoftpay_servicetypeid'])) {
            $data['payment_osoftpay_servicetypeid'] = $this->request->post['payment_osoftpay_servicetypeid'];
        } else {
            $data['payment_osoftpay_servicetypeid'] = $this->config->get('payment_osoftpay_servicetypeid');
        }

        if (isset($this->request->post['payment_osoftpay_mode'])) {
            $data['payment_osoftpay_mode'] = $this->request->post['payment_osoftpay_mode'];
        } else {
            $data['payment_osoftpay_mode'] = $this->config->get('payment_osoftpay_mode');
        }

        if (isset($this->request->post['payment_osoftpay_total'])) {
            $data['payment_osoftpay_total'] = $this->request->post['payment_osoftpay_total'];
        } else {
            $data['payment_osoftpay_total'] = $this->config->get('payment_osoftpay_total');
        }

        if (isset($this->request->post['payment_osoftpay_order_status_id'])) {
            $data['payment_osoftpay_order_status_id'] = $this->request->post['payment_osoftpay_order_status_id'];
        } else {
            $data['payment_osoftpay_order_status_id'] = $this->config->get('payment_osoftpay_order_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_osoftpay_geo_zone_id'])) {
            $data['payment_osoftpay_geo_zone_id'] = $this->request->post['payment_osoftpay_geo_zone_id'];
        } else {
            $data['payment_osoftpay_geo_zone_id'] = $this->config->get('payment_osoftpay_geo_zone_id');
        }


        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_osoftpay_status'])) {
            $data['payment_osoftpay_status'] = $this->request->post['payment_osoftpay_status'];
        } else {
            $data['payment_osoftpay_status'] = $this->config->get('payment_osoftpay_status');
        }

        if (isset($this->request->post['payment_osoftpay_sort_order'])) {
            $data['payment_osoftpay_sort_order'] = $this->request->post['payment_osoftpay_sort_order'];
        } else {
            $data['payment_osoftpay_sort_order'] = $this->config->get('payment_osoftpay_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput(
            $this->load->view(
                'extension/payment/osoftpay',
                $data
            )
        );

    }

    /**
     * @method Validate
     * For validating the request made
     */
    private function validate()
    {
        // Check if the user has permission
        if (!$this->user->hasPermission('modify', 'extension/payment/osoftpay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        // Check if the user Merchant ID is set
        if (!$this->request->post['payment_osoftpay_mercid']) {
            $this->error['payment_osoftpay_mercid'] = $this->language->get('error_mercid');
        }
        
        
        // Check if the PaymentItemName ID is Set
        if (!$this->request->post['payment_osoftpay_paymentItemName']) {
            $this->error['payment_osoftpay_paymentItemName'] = $this->language->get('error_paymentItemName');
        }

        return empty($this->error) ? true : false;
        // return false;

    }

}

