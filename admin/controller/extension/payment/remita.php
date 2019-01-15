<?php

class ControllerExtensionPaymentRemita extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/remita');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_remita', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=payment', true));
        }

        $data['action'] = $this->url->link('extension/payment/remita', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_disable_payment'] = $this->language->get('text_disable_payment');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_test'] = $this->language->get('text_test');
        $data['text_live'] = $this->language->get('text_live');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['entry_user_token'] = $this->language->get('entry_user_token');

        $data['entry_publickey'] = $this->language->get('entry_publickey');
        $data['entry_secretkey'] = $this->language->get('entry_secretkey');

        $data['entry_debug'] = $this->language->get('entry_debug');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_test'] = $this->language->get('entry_test');
        $data['entry_declined_status'] = $this->language->get('entry_declined_status');
        $data['entry_approved_status'] = $this->language->get('entry_approved_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['help_debug'] = $this->language->get('help_debug');
        $data['help_total'] = $this->language->get('help_total');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['payment_remita_publickey'])) {
            $data['error_publickey'] = $this->error['payment_remita_publickey'];
        } else {
            $data['error_publickey'] = '';
        }
        if (isset($this->error['payment_remita_secretkey'])) {
            $data['error_secretkey'] = $this->error['payment_remita_secretkey'];
        } else {
            $data['error_secretkey'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/payment/remita', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->request->post['payment_remita_publickey'])) {
            $data['payment_remita_publickey'] = $this->request->post['payment_remita_publickey'];
        } else {
            $data['payment_remita_publickey'] = $this->config->get('payment_remita_publickey');
        }
        if (isset($this->request->post['payment_remita_secretkey'])) {
            $data['payment_remita_secretkey'] = $this->request->post['payment_remita_secretkey'];
        } else {
            $data['payment_remita_secretkey'] = $this->config->get('payment_remita_secretkey');
        }
        if (isset($this->request->post['payment_remita_mode'])) {
            $data['payment_remita_mode'] = $this->request->post['payment_remita_mode'];
        } else {
            $data['payment_remita_mode'] = $this->config->get('payment_remita_mode');
        }
        if (isset($this->request->post['remita_user_token'])) {
            $data['remita_user_token'] = $this->request->post['remita_user_token'];
        } elseif ($this->config->get('remita_user_token')) {
            $data['remita_user_token'] = $this->config->get('remita_user_token');
        } else {
            $data['remita_user_token'] = sha1(uniqid(mt_rand(), 1));
        }

        if (isset($this->request->post['payment_remita_debug'])) {
            $data['payment_remita_debug'] = $this->request->post['payment_remita_debug'];
        } else {
            $data['payment_remita_debug'] = $this->config->get('payment_remita_debug');
        }

        if (isset($this->request->post['payment_remita_total'])) {
            $data['payment_remita_total'] = $this->request->post['payment_remita_total'];
        } else {
            $data['payment_remita_total'] = $this->config->get('payment_remita_total');
        }

        if (isset($this->request->post['payment_remita_declined_status_id'])) {
            $data['payment_remita_declined_status_id'] = $this->request->post['payment_remita_declined_status_id'];
        } else {
            $data['payment_remita_declined_status_id'] = $this->config->get('payment_remita_declined_status_id');
        }

        if (isset($this->request->post['payment_remita_approved_status_id'])) {
            $data['payment_remita_approved_status_id'] = $this->request->post['payment_remita_approved_status_id'];
        } else {
            $data['payment_remita_approved_status_id'] = $this->config->get('payment_remita_approved_status_id');
        }

        $this->load->model('localisation/order_status');


        if (isset($this->request->post['payment_remita_geo_zone_id'])) {
            $data['payment_remita_geo_zone_id'] = $this->request->post['payment_remita_geo_zone_id'];
        } else {
            $data['payment_remita_geo_zone_id'] = $this->config->get('payment_remita_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

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

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/remita')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_remita_publickey']) {
            $this->error['payment_remita_publickey'] = $this->language->get('error_publickey');
        }
        if (!$this->request->post['payment_remita_secretkey']) {
            $this->error['payment_remita_secretkey'] = $this->language->get('error_secretkey');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }

        return !$this->error;
    }
}
