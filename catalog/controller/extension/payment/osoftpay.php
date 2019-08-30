<?php

/**
 * Developed By Oladiran Segun Solomon
 * @author Oladiran Segun Solomon <sheghunoladiran9@gmail.com>
 * @link  github.com/sheghun
 */
class ControllerExtensionPaymentOsoftpay extends Controller
{
    public function index()
    {
        $this->load->model('checkout/order');
        $this->load->language('extension/payment/osoftpay');

        $mode = trim($this->config->get('payment_osoftpay_mode'));

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        // Get The Merchat Id
        $data['payment_osoftpay_mercid'] = $this->config->get('payment_osoftpay_mercid');
        $data['payment_osoftpay_paymentItemName'] = $this->config->get('payment_osoftpay_paymentItemName');
        $data['payment_osoftpay_mode'] = $this->config->get('payment_osoftpay_mode');
        $data['order_id'] = $this->session->data['order_id'];
        $data['redirecturl'] = $this->url->link('extension/payment/osoftpay/callback', '', 'SSL');
        $data['notificationurl'] = $this->url->link('extension/payment/osoftpay/notification', '', 'SSL');
        $data['total'] = $this->currency->format(
            $order_info['total'],
            $order_info['currency_code'],
            $order_info['currency_value'],
            false
        );
        $data['payer_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $data['payer_email'] = $order_info['email'];
        $data['payer_phone'] = html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $unique_ref = uniqid();
        $data['trans_ref'] = $unique_ref . '_' . $data['order_id'];

        $hash_string = $data['payment_osoftpay_mercid'] . $data['trans_ref'] . 2 . $data['total'] . $data['redirecturl'];

        $data['hash'] = hash('sha512', $hash_string);

        $data['gatewayurl'] =
            $mode == 'test' ?
            'https://developer.osoftpay.net/api/TestPublicPayments' :
            'https://osoftpay.net/api/PublicPayments';

        return $this->load->view('extension/payment/osoftpay', $data);
    }

    /**
     * @method Callback is the Function that
     * gets fired when you are redirected back to
     * the site from osoftpay
     * 
     */
    public function callback()
    {
        if(!isset($_GET['TransactionReference'])){
            $this->response->redirect(
                $this->url->link(
                    'common/home',
                    true
                )
                );
        }

        $this->load->model('checkout/order');

        if(isset($_GET['TransactionReference']) && (isset($_GET['PayRef']) && $_GET['PayRef'] !== '')) {

            $refArray = explode('_', $_GET['TransactionReference']);
            $data['order_id'] = $refArray[1];
            $data['TransactionReference'] = $refArray[0];

            $data['response_code'] = $_GET['ResCode'];
            $data['response_description'] = $_GET['ResDesc'];
            if($data['response_code'] == '00') {
                
                $message = 'Payment Status: - Successful - Description: '. $data['response_description'] . ' - TransactionReference: ' . $_GET['TransactionReference'];

                // Add Order History
                $order_status_id = ($this->config->get('payment_osoftpay_order_status_id'));
                $this->model_checkout_order->addOrderHistory(
                    $data['order_id'],
                    1,
                    $message,
                    true
                );

                //Redirect Home
                $this->response->redirect(
                    $this->url->link(
                        'checkout/success',
                        '',
                        true
                    )
                    );
            }

        } else {
            $message = 'Payment Status: - Not Successful - Description: ' . $data['response_description'] . ' - TransactionReference: '. $_GET['TransactionReference'];

            // Add Order History

            $order_status_id = ($this->config->get('payment_osoftpay_order_status_id'));

            $this->model_checkout_order->addOrderHistory(
                $data['order_id'],
                7,
                $message,
                true
            );

            
            $this->response->redirect(
                $this->url->link(
                    'checkout/checkout',
                    '',
                    true
                )
            );

        }
    }

    /**
     * This is not useful
     */
    // private function osoftpay_transaction_details($orderId)
    // {
    //     $merchant_id = trim($this->config->get('payment_osoftpay_mercid'));
    //     $hash = hash('sha512', $hash_string);

    //     $query_url =
    //         $mode == 'test' ?
    //         '' :
    //         '';
    //     // Initiate Curl
    //     $ch = curl_init();

    //     // Disable SSL verification
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     // Will return the response, if false it print the response
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     // Set the url
    //     curl_setopt($ch, CURLOPT_URL, $query_url);

    //     // Execute
    //     $result = curl_exec($ch);

    //     // Closing
    //     curl_close($ch);
    //     $response = json_decode($result, true);

    //     return $response;

    // }
}