<?php

class ControllerExtensionPaymentRemita extends Controller {

    public function index() {
        $this->load->model('checkout/order');
        $this->load->language('extension/payment/remita');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $data['remita_mercid'] = trim($this->config->get('payment_remita_mercid'));
        $data['remita_servicetypeid'] = trim($this->config->get('payment_remita_servicetypeid'));
        $data['remita_apikey'] = trim($this->config->get('payment_remita_apikey'));
        $mode = trim($this->config->get('payment_remita_mode'));
        $data['storeorderid'] = $this->session->data['order_id'];
        $data['returnurl'] = $this->url->link('extension/payment/remita/callback', '', 'SSL');
        $data['notificationurl'] = $this->url->link('extension/payment/remita/notification', '', 'SSL');
        $data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $data['totalAmount'] = html_entity_decode($data['total']);
        $data['payerName'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $data['payerEmail'] = $order_info['email'];
        $data['payerPhone'] = html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $uniqueRef = uniqid();
        $data['orderid'] = $uniqueRef . '_' . $data['storeorderid'];
        if ($mode == 'test') {
            $data['gateway_url'] = 'http://www.remitademo.net/remita/ecomm/init.reg';
        } else if ($mode == 'live') {
            $data['gateway_url'] = 'https://login.remita.net/remita/ecomm/init.reg';
        }
        $hash_string = $data['remita_mercid'] . $data['remita_servicetypeid'] . $data['orderid'] . $data['total'] . $data['returnurl'] . $data['remita_apikey'];
        $data['hash'] = hash('sha512', $hash_string);
        return $this->load->view('extension/payment/remita', $data);
    }

    private function remita_transaction_details($orderId) {
        $mert = trim($this->config->get('payment_remita_mercid'));
        $api_key = trim($this->config->get('payment_remita_apikey'));
        $hash_string = $orderId . $api_key . $mert;
        $hash = hash('sha512', $hash_string);
        if (trim($this->config->get('payment_remita_mode')) == 'test') {
            $query_url = 'http://www.remitademo.net/remita/ecomm';
        } else if (trim($this->config->get('payment_remita_mode')) == 'live') {
            $query_url = 'https://login.remita.net/remita/ecomm';
        }
        $url = $query_url . '/' . $mert . '/' . $orderId . '/' . $hash . '/' . 'orderstatus.reg';
        //  Initiate curl
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }

    private function updatePaymentStatus($order_id, $response_code, $response_reason, $rrr) {
        switch ($response_code) {
            case "00":
                $message = 'Payment Status : - Successful - Remita Retrieval Reference: ' . $rrr;
                $this->model_checkout_order->addOrderHistory($order_id, trim($this->config->get('remita_processed_status_id')), $message, true);
                break;
            case "01":
                $message = 'Payment Status : - Successful - Remita Retrieval Reference: ' . $rrr;
                $this->model_checkout_order->addOrderHistory($order_id, trim($this->config->get('remita_processed_status_id')), $message, true);
                break;
            case "021":
                $message = 'Payment Status : - Pending Payment - RRR Generated Successfully - Remita Retrieval Reference: ' . $rrr;
                $this->model_checkout_order->addOrderHistory($order_id, 1, $message, true);
                break;
            default:
                //process a failed transaction
                $message = 'Payment Status : - Not Successful - Reason: ' . $response_reason . ' - Remita Retrieval Reference: ' . $rrr;
                //1 - Pending Status
                $this->model_checkout_order->addOrderHistory($order_id, 1, $message, true);
                break;
        }
    }

    public function callback() {
        if (isset($_GET['orderID'])) {
            $data['order_id'] = "";
            $data['response_code'] = "";
            $data['rrr'] = "";
            $data['response_reason'] = "";
            $order_id = $_GET['orderID'];
            $response = $this->remita_transaction_details($order_id);
            $order_details = explode('_', $order_id);
            $storeorder_id = $order_details[1];
            $data['order_id'] = $storeorder_id;
            $this->load->model('checkout/order');
            $data['response_code'] = $response['status'];
            if (isset($response['RRR'])) {
                $data['rrr'] = $response['RRR'];
            }
            $data['response_reason'] = $response['message'];
            $this->updatePaymentStatus($storeorder_id, $data['response_code'], $data['response_reason'], $data['rrr']);
            if ($data['response_code'] === '01' || $data['response_code'] === '00') {
                $this->response->redirect($this->url->link('checkout/success', '', true));
            } else {
                $this->response->redirect($this->url->link('checkout/checkout', '', true));
            }
        }
    }

    public function notification() {
        $json = file_get_contents('php://input');
        $arr = json_decode($json, true);
        try {
            if ($arr != null) {
                foreach ($arr as $key => $orderArray) {
                    $order_id = $orderArray["orderRef"];
                    $response = $this->remita_transaction_details($order_id);
                    $orderId = $response['orderId'];
                    $order_details = explode('_', $orderId);
                    $storeorder_id = $order_details[1];
                    $data['response_code'] = $response['status'];
                    $data['rrr'] = $response['RRR'];
                    $data['response_reason'] = $response['message'];
                    $this->load->model('checkout/order');
                    $this->updatePaymentStatus($storeorder_id, $data['response_code'], $data['response_reason'], $data['rrr']);
                }
            }

            exit('OK');
        } catch (Exception $e) {
            exit('Error Updating Notification: ' . $e);
        }
    }

}
