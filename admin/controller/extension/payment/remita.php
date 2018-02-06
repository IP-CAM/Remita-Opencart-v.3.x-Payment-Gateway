<?php

class ControllerExtensionPaymentRemita extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/payment/remita');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_remita', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_test'] = $this->language->get('text_test');
        $data['text_live'] = $this->language->get('text_live');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['entry_mercid'] = $this->language->get('entry_mercid');
        $data['entry_token'] = $this->language->get('entry_token');
        $data['entry_notification_url'] = $this->language->get('entry_notification_url');
        $data['entry_servicetypeid'] = $this->language->get('entry_servicetypeid');
        $data['entry_apikey'] = $this->language->get('entry_apikey');
        $data['entry_debug'] = $this->language->get('entry_debug');
        $data['entry_test'] = $this->language->get('entry_test');
        $data['entry_pending_status'] = $this->language->get('entry_pending_status');
        $data['entry_processed_status'] = $this->language->get('entry_processed_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['help_total'] = $this->language->get('help_total');
        $data['help_ipn'] = $this->language->get('help_ipn');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/remita', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['action'] = $this->url->link('extension/payment/remita', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_remita_mercid'])) {
            $data['payment_remita_mercid'] = $this->request->post['payment_remita_mercid'];
        } else {
            $data['payment_remita_mercid'] = $this->config->get('payment_remita_mercid');
        }
        if (isset($this->request->post['payment_remita_servicetypeid'])) {
            $data['payment_remita_servicetypeid'] = $this->request->post['payment_remita_servicetypeid'];
        } else {
            $data['payment_remita_servicetypeid'] = $this->config->get('payment_remita_servicetypeid');
        }
        if (isset($this->request->post['payment_remita_apikey'])) {
            $data['payment_remita_apikey'] = $this->request->post['payment_remita_apikey'];
        } else {
            $data['payment_remita_apikey'] = $this->config->get('payment_remita_apikey');
        }
        if (isset($this->request->post['payment_remita_mode'])) {
            $data['payment_remita_mode'] = $this->request->post['payment_remita_mode'];
        } else {
            $data['payment_remita_mode'] = $this->config->get('payment_remita_mode');
        }
        $data['payment_remita_notification_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/remita/notification';


        if (isset($this->request->post['payment_remita_total'])) {
            $data['payment_remita_total'] = $this->request->post['payment_remita_total'];
        } else {
            $data['payment_remita_total'] = $this->config->get('payment_remita_total');
        }

        if (isset($this->request->post['payment_remita_pending_status_id'])) {
            $data['payment_remita_pending_status_id'] = $this->request->post['payment_remita_pending_status_id'];
        } else {
            $data['payment_remita_pending_status_id'] = $this->config->get('payment_remita_pending_status_id');
        }

        if (isset($this->request->post['payment_remita_processed_status_id'])) {
            $data['payment_remita_processed_status_id'] = $this->request->post['payment_remita_processed_status_id'];
        } else {
            $data['payment_remita_processed_status_id'] = $this->config->get('payment_remita_processed_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_remita_geo_zone_id'])) {
            $data['payment_remita_geo_zone_id'] = $this->request->post['payment_remita_geo_zone_id'];
        } else {
            $data['payment_remita_geo_zone_id'] = $this->config->get('payment_remita_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_remita_status'])) {
            $data['payment_remita_status'] = $this->request->post['payment_remita_status'];
        } else {
            $data['payment_remita_status'] = $this->config->get('payment_remita_status');
        }

        if (isset($this->request->post['payment_remita_sort_order'])) {
            $data['payment_remita_sort_order'] = $this->request->post['payment_remita_sort_order'];
        } else {
            $data['payment_remita_sort_order'] = $this->config->get('payment_remita_sort_order');
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/payment/remita', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/remita')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_remita_mercid']) {
            $this->error['remita_mercid'] = $this->language->get('error_mercid');
        }
        if (!$this->request->post['payment_remita_servicetypeid']) {
            $this->error['remita_servicetypeid'] = $this->language->get('error_servicetypeid');
        }
        if (!$this->request->post['payment_remita_apikey']) {
            $this->error['remita_apikey'] = $this->language->get('error_apikey');
        }

        return !$this->error;
    }

}
